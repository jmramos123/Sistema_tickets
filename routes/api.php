<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TTSController;


Route::post('/tts', [TTSController::class, 'speak']);

