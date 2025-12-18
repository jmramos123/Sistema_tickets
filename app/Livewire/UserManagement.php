<?php

// app/Livewire/UserManagement.php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Usuario;
use App\Models\Persona;
use App\Models\Area;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagement extends Component
{
    use WithPagination;

    public $filterArea = '';
    public $filterStatus = '';

    public $search = '';
    public $modalOpen = false;
    public $isEditMode = false;
    public $selectedUsuario;

    public $nombre, $apellido, $telefono, $email;
    public $username, $password, $role, $area_id;

    protected $paginationTheme = 'bootstrap';

    protected function rules()
    {
        $usernameRule = 'required|string|min:3|unique:usuarios,username';
        $passwordRule = 'required|string|min:6';

        if ($this->isEditMode && $this->selectedUsuario) {
            $usernameRule = [
                'required', 'string', 'min:3',
                Rule::unique('usuarios', 'username')->ignore($this->selectedUsuario->id),
            ];
            $passwordRule = 'nullable|string|min:6';
        }

        return [
            'nombre' => 'required|string|min:2',
            'apellido' => 'required|string|min:2',
            'telefono' => 'required|string',
            'email' => 'required|email|unique:personas,email' . ($this->isEditMode ? ',' . $this->selectedUsuario->persona_id : ''),
            'username' => $usernameRule,
            'password' => $passwordRule,
            'role' => 'required|exists:roles,name',
            'area_id' => 'required|exists:areas,id',
        ];
    }

    public function render()
    {
        $query = Usuario::with('persona', 'area', 'roles')
            ->when($this->search, function ($q) {
                $q->whereHas('persona', function ($subQuery) {
                    $subQuery->where('nombre', 'like', "%{$this->search}%")
                        ->orWhere('apellido', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterArea, function ($q) {
                $q->where('area_id', $this->filterArea);
            })
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            });


        if ($this->filterArea) {
            $query->where('area_id', $this->filterArea);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        return view('livewire.user-management', [
            'usuarios' => $query->paginate(5),
            'areas' => Area::all(),
            'roles' => Role::pluck('name'),
        ]);
    }
    
    public function updatedFilterArea()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->modalOpen = true;
    }

    public function closeModal()
    {
        $this->modalOpen = false;
        $this->resetForm();
    }

    public function edit(Usuario $usuario)
    {
        $this->selectedUsuario = $usuario;
        $this->nombre = $usuario->persona->nombre;
        $this->apellido = $usuario->persona->apellido;
        $this->telefono = $usuario->persona->telefono;
        $this->email = $usuario->persona->email;
        $this->username = $usuario->username;
        $this->role = $usuario->roles->first()?->name;
        $this->area_id = $usuario->area_id;
        $this->password = '';
        $this->isEditMode = true;
        $this->modalOpen = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->isEditMode && $this->selectedUsuario) {
            $this->selectedUsuario->persona->update([
                'nombre' => $this->nombre,
                'apellido' => $this->apellido,
                'telefono' => $this->telefono,
                'email' => $this->email,
            ]);

            $this->selectedUsuario->update([
                'username' => $this->username,
                'area_id' => $this->area_id,
            ]);

            if ($this->password) {
                $this->selectedUsuario->password = Hash::make($this->password);
                $this->selectedUsuario->save();
            }

            $this->selectedUsuario->syncRoles($this->role);
            session()->flash('message', 'Usuario actualizado.');
        } else {
            $persona = Persona::create([
                'nombre' => $this->nombre,
                'apellido' => $this->apellido,
                'telefono' => $this->telefono,
                'email' => $this->email,
            ]);

            $usuario = Usuario::create([
                'persona_id' => $persona->id,
                'username' => $this->username,
                'password' => Hash::make($this->password),
                'area_id' => $this->area_id,
                'status' => 'enabled',
            ]);

            $usuario->assignRole($this->role);
            session()->flash('message', 'Usuario creado.');
        }

        $this->closeModal();
    }

    public function disableUser($id)
    {
        Usuario::findOrFail($id)->update(['status' => 'disabled']);
        session()->flash('message', 'Usuario deshabilitado.');
        $this->resetPage();
    }

    public function enableUser($id)
    {
        Usuario::findOrFail($id)->update(['status' => 'enabled']);
        session()->flash('message', 'Usuario habilitado.');
        $this->resetPage();
    }

    public function delete($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->persona->delete();
        $usuario->delete();
        session()->flash('message', 'Usuario eliminado.');
        $this->resetPage();
    }

    private function resetForm()
    {
        $this->reset([
            'nombre', 'apellido', 'telefono', 'email',
            'username', 'password', 'role', 'area_id',
            'isEditMode', 'selectedUsuario'
        ]);
    }
}
