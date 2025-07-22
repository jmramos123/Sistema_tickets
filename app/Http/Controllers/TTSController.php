<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TTSController extends Controller
{
    public function speak(Request $request)
    {
        $text = $request->input('text');

        if (!$text) {
            return response()->json(['error' => 'No text provided'], 400);
        }

        // Log the incoming request for debugging
        Log::info('TTS speak request received', ['text' => $text]);

        $url = "https://api.elevenlabs.io/v1/text-to-speech/" . env('ELEVENLABS_VOICE_ID');

        $response = Http::withHeaders([
            'xi-api-key' => env('ELEVENLABS_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post($url, [
            'text' => $text,
            'voice_settings' => [
                'stability' => 0.5,
                'similarity_boost' => 0.7
            ]
        ]);

        // If the request fails, log full details
        if (!$response->successful()) {
            Log::error('TTS request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'url' => $url,
                'text' => $text,
            ]);

            return response()->json([
                'error' => 'TTS failed',
                'status' => $response->status(),
                'details' => $response->json(),
            ], 500);
        }

        // Optional: Log success status
        Log::info('TTS request succeeded', ['status' => $response->status()]);

        return response($response->body(), 200)
            ->header('Content-Type', 'audio/mpeg');
    }
}
