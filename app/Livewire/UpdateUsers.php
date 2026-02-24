<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class UpdateUsers extends Component
{
    public $id;
    public $name;
    public $ncelular;
    public $email;
    public $dni;

    public function mount($id)
    {
        $this->id = $id;

        $use = DB::table('gamnielb_admision.users as u')
            ->join('gamnielb_sia.userprofile as up', 'u.id', '=', 'up.id_users')
            ->select('u.id', 'u.dni', 'up.nombre', 'up.num_celualr', 'up.correo')
            ->where('u.id', '=', $id)
            ->first();
        // dd($use);
        $this->name = $use->nombre;
        $this->email = $use->correo;
        $this->ncelular = $use->num_celualr;
        $this->dni = $use->dni;
    }

    public function updateUser()
    {
        $user = User::find($this->id);
        $dni_anterior = $user->dni;

        // dd($this->dni);
        $validatedData = $this->validate([
            'dni' => 'required|string|regex:/^[0-9]+$/|min:8|max:8|unique:users,dni,' . $this->id . ',id',
            'name' => 'required|string|max:200',
            'ncelular' => 'required|integer|min:0|max:999999999',
            //'email' => 'required|email|max:200|unique:mysql_segunda.userprofile,correo,' . $this->id . ',id_users',
        ]);

        $update_dni = DB::table('users')
            ->where('users.id', '=', $this->id)
            ->update([
                'dni' => $this->dni,
                'updated_at' => now()
            ]);

        $update_dat = DB::connection('mysql_segunda')->table('userprofile')
            ->where('id_users', '=', $this->id)
            ->update([
                'nombre' => $this->name,
                'num_celualr' => $this->ncelular,
                'correo' => $this->email,
            ]);

        $this->dispatch('mensaje', ['msm' => 'Usuario actualizado con éxito.', 'name' => $this->name, 'id' => $this->id]);
    }

    public function render()
    {
        return view('livewire.update-users');
    }
}
