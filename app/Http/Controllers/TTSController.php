<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TTSController extends Controller
{
    public function speak(Request $request)
    {
        $text = $request->input('text');

        if (!$text) {
            return response()->json(['error' => 'No text provided'], 400);
        }

        // Example: using ElevenLabs TTS API
        $response = Http::withHeaders([
            'xi-api-key' => env('ELEVENLABS_API_KEY'),
        ])->post("https://api.elevenlabs.io/v1/text-to-speech/".env('ELEVENLABS_VOICE_ID'), [
            'text' => $text,
            'voice_settings' => [
                'stability' => 0.5,
                'similarity_boost' => 0.7
            ]
        ]);

        if ($response->successful()) {
            return response($response->body(), 200)
                ->header('Content-Type', 'audio/mpeg');
        }

        return response()->json(['error' => 'TTS failed'], 500);
    }
}
