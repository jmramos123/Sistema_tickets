<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use App\Events\VideoDeleted;

class VideoManagement extends Component
{
    use WithFileUploads;

    public $confirmingVideoDeleteId = null;
    public $nombre;
    public $videoFile;

    protected $listeners = [
        'videoUploaded'  => '$refresh',
    ];

    public function confirmDelete($id)
    {
        $this->confirmingVideoDeleteId = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingVideoDeleteId = null;
    }

    public function render()
    {
        return view('livewire.video-management', [
            'videos'            => Video::latest('uploaded_at')->get(),
            'currentlyPlayingId'=> Video::where('is_active', true)->value('id'),
        ]);
    }

    public function delete($id)
    {
        $video = Video::findOrFail($id);

        // if it was active, you might still want to clear its flag...
        if ($video->is_active) {
            $video->is_active = false;
            $video->save();
        }

        // broadcast deletion for **any** deleted video
        broadcast(new VideoDeleted($id))->toOthers();

        Storage::disk('public')->delete($video->ruta_archivo);
        $video->delete();

        session()->flash('message', 'Video eliminado.');
        $this->confirmingVideoDeleteId = null;
    }

    public function setAsActive($id)
    {
        Video::query()->update(['is_active' => false]);

        $video = Video::findOrFail($id);
        $video->is_active = true;
        $video->save();

        session()->flash('message', "Video '{$video->nombre}' ahora activo en TV.");
    }

    public function uploadVideo()
    {
        $this->validate([
            'nombre'    => 'required|min:3',
            'videoFile' => 'required|file|mimes:mp4,avi,mov,webm|max:51200',
        ]);

        // Guardamos el archivo
        $path = $this->videoFile->store('videos', 'public');

        // STEP 1: desactivar todos
        Video::query()->update(['is_active' => false]);

        // STEP 2: crear y activar el nuevo
        $video = Video::create([
            'nombre'        => $this->nombre,
            'ruta_archivo'  => $path,
            'uploaded_at'   => now(),
            'is_active'     => true,
        ]);

        // —— REGISTRO DE DEBUG ——
        logger("[VideoManagement] 📤 uploadVideo() creado video ID {$video->id}, ruta: {$video->ruta_archivo}");

        // broadcast upload + new active flag
        broadcast(new \App\Events\VideoUploaded($video))->toOthers();

        logger("[VideoManagement] 📡 Broadcast VideoUploaded para ID {$video->id} enviado");

        // notify frontend to refresh
        $this->dispatch('videoUploaded');

        $this->reset(['nombre', 'videoFile']);
        session()->flash('message', 'Video subido exitosamente y activado en TV.');
    }

}
