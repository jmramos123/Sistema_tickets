<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;

class VideoManagement extends Component
{
    public $nombre;
    protected $listeners = ['videoUploaded' => '$refresh'];

    public function render()
    {
        return view('livewire.video-management', [
            'videos' => Video::latest('uploaded_at')->get(),
        ]);
    }

    public function delete($id)
    {
        $video = Video::findOrFail($id);
        Storage::disk('public')->delete($video->ruta_archivo);
        $video->delete();
        session()->flash('message', 'Video eliminado.');
    }
    public function setAsActive($id)
    {
        Video::query()->update(['is_active' => false]); // Clear existing

        $video = Video::findOrFail($id);
        $video->is_active = true;
        $video->save();

        session()->flash('message', "Video '{$video->nombre}' seleccionado para la TV.");
    }
}