<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Escritorio;
use App\Models\Area;
use Illuminate\Validation\Rule;

class EscritorioManagement extends Component
{
    public $escritorios, $areas;
    public $escritorio_id;
    public $nombre;
    public $area_id;
    public $isEditMode = false;

    protected $rules = [
        'nombre' => 'required|string|min:3',
        'area_id' => 'required|exists:areas,id',
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->escritorios = Escritorio::with('area')->get();
        $this->areas = Area::all();
    }

    public function resetInput()
    {
        $this->reset(['escritorio_id', 'nombre', 'area_id']);
        $this->isEditMode = false;
    }

    public function edit($id)
    {
        $escritorio = Escritorio::findOrFail($id);
        $this->escritorio_id = $escritorio->id;
        $this->nombre = $escritorio->nombre;
        $this->area_id = $escritorio->area_id;
        $this->isEditMode = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->isEditMode) {
            $escritorio = Escritorio::findOrFail($this->escritorio_id);
            $escritorio->update([
                'nombre_escritorio' => $this->nombre,
                'area_id' => $this->area_id,
            ]);
            session()->flash('message', 'Escritorio actualizado correctamente.');
        } else {
            Escritorio::create([
                'nombre_escritorio' => $this->nombre,
                'area_id' => $this->area_id,
            ]);
            session()->flash('message', 'Escritorio creado correctamente.');
        }

        $this->loadData();
        $this->resetInput();
        $this->closeModal();
    }


    public function delete($id)
    {
        $escritorio = Escritorio::findOrFail($id);
        $escritorio->delete();

        session()->flash('message', 'Escritorio eliminado correctamente.');
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.escritorio-management');
    }
    public $modalOpen = false;

    public function openModal()
    {
        $this->resetInput();
        $this->modalOpen = true;
    }

    public function closeModal()
    {
        $this->resetInput();
        $this->modalOpen = false;
    }

}
