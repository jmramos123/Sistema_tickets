<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Escritorio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DeskSelection extends Component
{
    public $desks = [];

    public function mount()
    {
        $userAreaId = Auth::user()->area_id;

        // Load desks from user's area
        $this->desks = Escritorio::where('area_id', $userAreaId)->get();
    }

    public function selectDesk($deskId)
    {
        Session::put('escritorio_id', $deskId);

        return redirect()->route('user.tickets');  // or wherever your user ticket queue lives
    }

    public function render()
    {
        return view('livewire.desk-selection')->layout('components.layouts.client');
    }
}
