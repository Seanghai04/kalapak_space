<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'Kalapak Code Team API',
        'version' => '1.0.0',
    ]);
});
