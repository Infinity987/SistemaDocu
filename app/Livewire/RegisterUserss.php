<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;

class RegisterUserss extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $rol;
    public $dni;
    public $ncelular;

    protected $rules = [
        'dni' => 'required|string|regex:/^[0-9]+$/|unique:users,dni',
        'name' => 'required|string|max:255',
        'ncelular' => 'required|integer|min:0|max:999999999',
        // 'email' => 'required|string|email|max:255',
        'password' => 'required|string|min:8|confirmed',
        'rol' => 'required|int|exists:roles,id'
    ];

    public function register()
    {
        $this->validate();

        if ($this->rol == 1) {
            try {
                DB::connection('mysql_segunda')->beginTransaction();
                $usu = User::create([
                    'dni' => $this->dni,
                    'password' => Hash::make($this->password),
                ])->assignRole('admin');

                $usu_id = $usu->id;

                $save_userProfile = DB::connection('mysql_segunda')->table('userprofile')->insert([
                    'nombre' => mb_strtoupper($this->name, 'UTF-8'),
                    'num_celualr' => $this->ncelular,
                    'correo' => $this->email,
                    'id_users' => $usu_id,
                ]);
                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email']);
                $this->dispatch('mensaje', ['msm' => 'Usuario registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario NO registrado.']);
            }
        } elseif ($this->rol == 2) {
            try {
                DB::connection('mysql_segunda')->beginTransaction();
                $usu = User::create([
                    'dni' => $this->dni,
                    'password' => Hash::make($this->password),
                ])->assignRole('docente');

                $usu_id = $usu->id;

                $save_userProfile = DB::connection('mysql_segunda')->table('userprofile')->insert([
                    'nombre' => mb_strtoupper($this->name, 'UTF-8'),
                    'num_celualr' => $this->ncelular,
                    'correo' => $this->email,
                    'id_users' => $usu_id,
                ]);

                $insert_tabla_doce = DB::connection('mysql_segunda')->table('docente')->insert([
                    'especialidad' => 'docente',
                    'estado' => 1,
                    'id_users' => $usu_id,
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email']);
                $this->dispatch('mensaje', ['msm' => 'Usuario registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario NO registrado.']);
            }
        } elseif ($this->rol == 6) {
            try {
                DB::connection('mysql_segunda')->beginTransaction();
                $usu = User::create([
                    'dni' => $this->dni,
                    'password' => Hash::make($this->password),
                ])->assignRole('admision');

                $usu_id = $usu->id;

                $save_userProfile = DB::connection('mysql_segunda')->table('userprofile')->insert([
                    'nombre' => mb_strtoupper($this->name, 'UTF-8'),
                    'num_celualr' => $this->ncelular,
                    'correo' => $this->email,
                    'id_users' => $usu_id,
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email']);
                $this->dispatch('mensaje', ['msm' => 'Usuario registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => $th->getMessage()]);
            }
        } else {
            $this->dispatch('mensaje', ['msm' => 'Error al agregar usuario ...']);
        }
    }

    public function render()
    {
        return view('livewire.register-userss');
    }
}
