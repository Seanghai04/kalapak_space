<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Cloudinary\Cloudinary;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StorageStatsController extends Controller
{
    public function index(): JsonResponse
    {
        $data = Cache::remember('storage_stats', 3600, function () {
            return [
                'cloudinary' => $this->getCloudinaryStats(),
                'supabase' => $this->getSupabaseStats(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'cached_until' => Cache::get('storage_stats') ? now()->addHour()->toIso8601String() : null,
        ]);
    }

    public function refresh(): JsonResponse
    {
        Cache::forget('storage_stats');

        return $this->index();
    }

    private function getCloudinaryStats(): array
    {
        try {
            $cloudUrl = config('cloudinary.cloud_url');
            if (empty($cloudUrl)) {
                return $this->errorResult('Cloudinary', 'CLOUDINARY_URL not configured');
            }

            $cloudinary = new Cloudinary($cloudUrl);
            $usage = $cloudinary->adminApi()->usage();

            // Cloudinary API may return 'limit' or 'allowed' or neither
            // Free plan defaults: 25 GB storage, 25 GB bandwidth, 25,000 transformations
            $freeStorageLimit = 25 * 1024 * 1024 * 1024;   // 25 GB
            $freeBandwidthLimit = 25 * 1024 * 1024 * 1024; // 25 GB
            $freeTransformLimit = 25000;

            $storageUsed = $usage['storage']['usage'] ?? 0;
            $storageLimit = $usage['storage']['limit'] ?? $usage['storage']['allowed'] ?? $freeStorageLimit;
            $bandwidthUsed = $usage['bandwidth']['usage'] ?? 0;
            $bandwidthLimit = $usage['bandwidth']['limit'] ?? $usage['bandwidth']['allowed'] ?? $freeBandwidthLimit;
            $transformations = $usage['transformations']['usage'] ?? 0;
            $transformationsLimit = $usage['transformations']['limit'] ?? $usage['transformations']['allowed'] ?? $freeTransformLimit;
            $resources = $usage['resources'] ?? 0;

            return [
                'configured' => true,
                'plan' => $usage['plan'] ?? 'Free',
                'storage' => [
                    'used' => $storageUsed,
                    'limit' => $storageLimit,
                    'used_formatted' => $this->formatBytes($storageUsed),
                    'limit_formatted' => $this->formatBytes($storageLimit),
                    'percentage' => $storageLimit > 0 ? round(($storageUsed / $storageLimit) * 100, 1) : 0,
                ],
                'bandwidth' => [
                    'used' => $bandwidthUsed,
                    'limit' => $bandwidthLimit,
                    'used_formatted' => $this->formatBytes($bandwidthUsed),
                    'limit_formatted' => $this->formatBytes($bandwidthLimit),
                    'percentage' => $bandwidthLimit > 0 ? round(($bandwidthUsed / $bandwidthLimit) * 100, 1) : 0,
                ],
                'transformations' => [
                    'used' => $transformations,
                    'limit' => $transformationsLimit,
                    'percentage' => $transformationsLimit > 0 ? round(($transformations / $transformationsLimit) * 100, 1) : 0,
                ],
                'resources' => $resources,
            ];
        } catch (\Throwable $e) {
            Log::error('Cloudinary stats error: ' . $e->getMessage());
            return $this->errorResult('Cloudinary', $e->getMessage());
        }
    }

    private function getSupabaseStats(): array
    {
        try {
            $url = rtrim(config('services.supabase.url'), '/');
            $key = config('services.supabase.secret_key');
            $bucket = config('services.supabase.bucket');

            if (empty($url) || empty($key)) {
                return $this->errorResult('Supabase', 'Supabase not configured');
            }

            // List all objects in the bucket to calculate total size
            $totalSize = 0;
            $fileCount = 0;
            $folderStats = [];

            $folders = ['covers', 'projects', 'team', 'media', 'avatars'];

            foreach ($folders as $folder) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $key,
                ])->post("{$url}/storage/v1/object/list/{$bucket}", [
                            'prefix' => $folder,
                            'limit' => 1000,
                            'offset' => 0,
                        ]);

                if ($response->successful()) {
                    $files = collect($response->json())->filter(fn($item) => isset($item['metadata']['size']));
                    $folderSize = $files->sum(fn($item) => $item['metadata']['size'] ?? 0);
                    $folderCount = $files->count();

                    $folderStats[$folder] = [
                        'files' => $folderCount,
                        'size' => $folderSize,
                        'size_formatted' => $this->formatBytes($folderSize),
                    ];

                    $totalSize += $folderSize;
                    $fileCount += $folderCount;
                }
            }

            // Supabase free tier: 1GB storage
            $limit = 1 * 1024 * 1024 * 1024; // 1 GB

            return [
                'configured' => true,
                'storage' => [
                    'used' => $totalSize,
                    'limit' => $limit,
                    'used_formatted' => $this->formatBytes($totalSize),
                    'limit_formatted' => $this->formatBytes($limit),
                    'percentage' => $limit > 0 ? round(($totalSize / $limit) * 100, 1) : 0,
                ],
                'files' => $fileCount,
                'folders' => $folderStats,
                'bucket' => $bucket,
            ];
        } catch (\Throwable $e) {
            Log::error('Supabase stats error: ' . $e->getMessage());
            return $this->errorResult('Supabase', $e->getMessage());
        }
    }

    private function errorResult(string $provider, string $message): array
    {
        return [
            'configured' => false,
            'error' => $message,
            'storage' => [
                'used' => 0,
                'limit' => 0,
                'used_formatted' => '0 B',
                'limit_formatted' => '0 B',
                'percentage' => 0,
            ],
        ];
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
