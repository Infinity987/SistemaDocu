<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class UpdatePassword extends Component
{
    public $id;
    public $password;
    public $password_confirmation;

    public function mount($id)
    {
        $this->id = $id;
    }

    public function updatePassword()
    {
        $this->validate([
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password'
        ]);

        $user = User::find($this->id);
        if ($user) {
            $user->update(['password' => Hash::make($this->password)]);
        }

        session()->flash('message', 'Contraseña actualizada correctamente.');
    }
    public function render()
    {
        return view('livewire.update-password');
    }
}
