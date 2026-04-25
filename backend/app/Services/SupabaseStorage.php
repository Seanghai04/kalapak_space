<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class SupabaseStorage
{
    protected string $url;
    protected string $key;
    protected string $bucket;

    public function __construct()
    {
        $this->url = rtrim(config('services.supabase.url'), '/');
        $this->key = config('services.supabase.secret_key');
        $this->bucket = config('services.supabase.bucket');
    }

    /**
     * Upload a file to Supabase Storage.
     */
    public function upload(UploadedFile $file, string $folder = ''): string|false
    {
        $filename = $folder . '/' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filename = ltrim($filename, '/');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->key,
            'Content-Type' => $file->getMimeType(),
            'x-upsert' => 'true',
        ])->withBody(
                file_get_contents($file->getRealPath()),
                $file->getMimeType()
            )->post("{$this->url}/storage/v1/object/{$this->bucket}/{$filename}");

        if ($response->successful()) {
            return $filename;
        }

        throw new \RuntimeException(
            'Supabase upload failed: ' . $response->body()
        );
    }

    /**
     * Delete a file from Supabase Storage.
     */
    public function delete(string $path): bool
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->key,
        ])->delete("{$this->url}/storage/v1/object/{$this->bucket}", [
                    'prefixes' => [$path],
                ]);

        return $response->successful();
    }

    /**
     * Returns true when Supabase is configured with real credentials.
     */
    public function isConfigured(): bool
    {
        if (empty($this->url) || empty($this->key)) {
            return false;
        }

        $urlLower = strtolower($this->url);
        $keyLower = strtolower($this->key);
        $host = strtolower((string) parse_url($this->url, PHP_URL_HOST));

        if (
            str_contains($urlLower, 'your_project_id') ||
            str_contains($host, 'your_project_id') ||
            $keyLower === 'your-anon-key' ||
            $keyLower === 'your-service-role-key'
        ) {
            return false;
        }

        return true;
    }

    /**
     * Get the public URL for a file, or null when Supabase is not configured.
     */
    public function url(string $path): ?string
    {
        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        if (!$this->isConfigured()) {
            return null;
        }
        return "{$this->url}/storage/v1/object/public/{$this->bucket}/{$path}";
    }

    /**
     * Test the connection by uploading and deleting a test file.
     */
    public function test(): array
    {
        $result = [
            'url' => $this->url,
            'bucket' => $this->bucket,
            'key_set' => !empty($this->key),
        ];

        try {
            $testPath = '_test_' . time() . '.txt';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->key,
                'Content-Type' => 'text/plain',
                'x-upsert' => 'true',
            ])->withBody('test', 'text/plain')
                ->post("{$this->url}/storage/v1/object/{$this->bucket}/{$testPath}");

            if ($response->successful()) {
                $this->delete($testPath);
                $result['upload_test'] = 'SUCCESS';
            } else {
                $result['upload_test'] = 'FAILED';
                $result['error'] = $response->body();
                $result['status_code'] = $response->status();
            }
        } catch (\Exception $e) {
            $result['upload_test'] = 'FAILED';
            $result['error'] = $e->getMessage();
        }

        return $result;
    }
}
