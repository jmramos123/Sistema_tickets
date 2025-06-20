<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|min:3',
            'videoFile' => 'required|file|mimes:mp4,avi,mov,webm|max:51200',
        ]);
        
        try {
            $file = $request->file('videoFile');
            $filename = $this->generateSafeFilename($request->nombre, $file->extension());
            $path = $file->storeAs('videos', $filename, 'public');
            
            Video::create([
                'nombre' => $request->nombre,
                'ruta_archivo' => $path,
                'uploaded_at' => now(),
            ]);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function generateSafeFilename($name, $extension)
    {
        $base = Str::slug($name);
        $filename = "{$base}.{$extension}";
        $counter = 1;

        while (Storage::disk('public')->exists("videos/{$filename}")) {
            $filename = "{$base}_{$counter}.{$extension}";
            $counter++;
        }

        return $filename;
    }
}