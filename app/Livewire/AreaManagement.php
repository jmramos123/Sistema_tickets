<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Area;
use Illuminate\Validation\Rule;

class AreaManagement extends Component
{
    public $search = '';

    public $areas, $selectedArea = null;
    public $codigo_area, $nombre_area, $descripcion;
    public $isEditMode = false;

    protected $rules = [
        'codigo_area' => 'nullable|exists:codigo_area',
        'nombre_area' => 'required|string|min:3',
        'descripcion' => 'nullable|string',
    ];

    public function mount()
    {
        $this->loadAreas();
    }

    public function loadAreas()
    {
        $this->areas = Area::query()
            ->when($this->search, function ($query) {
                $query->where('codigo_area', 'like', '%' . $this->search . '%')
                    ->orWhere('nombre_area', 'like', '%' . $this->search . '%')
                    ->orWhere('descripcion', 'like', '%' . $this->search . '%');
            })
            ->get();
    }

    public function resetInput()
    {
        $this->reset(['codigo_area', 'nombre_area', 'descripcion', 'isEditMode', 'selectedArea']);
    }

    public function edit(Area $area)
    {
        $this->selectedArea = $area;
        $this->codigo_area = $area->codigo_area;
        $this->nombre_area = $area->nombre_area;
        $this->descripcion = $area->descripcion;
        $this->isEditMode = true;
    }

    public function applySearch()
    {
        $this->loadAreas(); // reload with the current $search value
    }

    public function save()
    {
        $rules = $this->rules;

        if ($this->isEditMode && $this->selectedArea) {
            // Ignore current area's codigo_area in unique validation
            $rules['codigo_area'] = [
                'required',
                'string',
                Rule::unique('areas', 'codigo_area')->ignore($this->selectedArea->id),
            ];
        }

        $this->validate($rules);

        if ($this->isEditMode) {
            $area = $this->selectedArea;
            $area->update([
                'codigo_area' => $this->codigo_area ?: null, 
                'nombre_area' => $this->nombre_area,
                'descripcion' => $this->descripcion,
            ]);

            session()->flash('message', 'Área actualizada correctamente.');
        } else {
            Area::create([
                'codigo_area' => $this->codigo_area ?: null,
                'nombre_area' => $this->nombre_area,
                'descripcion' => $this->descripcion,
            ]);

            session()->flash('message', 'Área creada correctamente.');
        }

        $this->loadAreas();
        $this->resetInput();
        $this->closeModal();

    }

    public function delete(Area $area)
    {
        $area->delete();
        session()->flash('message', 'Área eliminada correctamente.');
        $this->loadAreas();
    }

    public function render()
    {
        return view('livewire.area-management');
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
