<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TTSController;
use Illuminate\Support\Facades\Log;

Route::post('/tts', function () {
    Log::info('/api/tts hit');
    return response()->json(['status' => 'ok']);
});


Route::post('/tts', [TTSController::class, 'speak']);

