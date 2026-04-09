<?php

namespace App\Http\Controllers\PublicApi;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    public function settings(): JsonResponse
    {
        $settings = Setting::whereIn('group', ['general', 'social', 'seo'])->get()
            ->mapWithKeys(fn($s) => [$s->key => $s->value]);

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }
}
