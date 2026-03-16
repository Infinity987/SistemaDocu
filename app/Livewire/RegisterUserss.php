<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class RegisterUserss extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $rol;
    public $dni;
    public $ncelular;

    public $tipos_usuario = []; // para la lista
    public $tipo_usuario = null;  // para el valor seleccionado


    protected $rules = [
        'dni' => 'required|string|regex:/^[0-9]+$/|unique:users,dni',
        'name' => 'required|string|max:255',
        'ncelular' => 'required|integer|min:0|max:999999999',
        // 'email' => 'required|string|email|max:255',
        'password' => 'required|string|min:8|confirmed',
        'rol' => 'required|int|exists:roles,id'
    ];

    public function mount()
    {
        $this->tipos_usuario = DB::connection('mysql_documentario')->table('tipo_usuario')->get(); // así está bien
    }

    public function register()
    {
        $this->validate();

        $fechaHoraPeru = Carbon::now('America/Lima');
        $fechayhora = $fechaHoraPeru->toDateTimeString();

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

                DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                    'user_id' => $usu->id,
                    'dependencia_id' => 1,
                    'created_at' => $fechayhora,
                    'estado' => 1,
                    'tipo_usuario' => $this->tipo_usuario,
                    'token' => Str::uuid()
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email', 'tipo_usuario']);
                $this->dispatch('mensaje', ['msm' => 'Usuario tipo ADMINISTRADOR SISTEMA registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario NO registrado ADMINISTRADOR SISTEMA.']);
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

                DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                    'user_id' => $usu->id,
                    'dependencia_id' => 2,
                    'created_at' => $fechayhora,
                    'estado' => 1,
                    'tipo_usuario' => $this->tipo_usuario,
                    'token' => Str::uuid()
                ]);

                $insert_tabla_doce = DB::connection('mysql_segunda')->table('docente')->insert([
                    'especialidad' => 'docente',
                    'estado' => 1,
                    'id_users' => $usu_id,
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email', 'tipo_usuario']);
                $this->dispatch('mensaje', ['msm' => 'Usuario tipo DOCENTE registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario tipo DOCENTE NO registrado.']);
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

                DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                    'user_id' => $usu->id,
                    'dependencia_id' => 6,
                    'created_at' => $fechayhora,
                    'estado' => 1,
                    'tipo_usuario' => $this->tipo_usuario,
                    'token' => Str::uuid()
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email', 'tipo_usuario']);
                $this->dispatch('mensaje', ['msm' => 'Usuario tipo ADMISIÓN registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario tipo ADMISIÓN no registrado']);
            }
        } elseif ($this->rol == 7) {
            try {
                DB::connection('mysql_segunda')->beginTransaction();
                $usu = User::create([
                    'dni' => $this->dni,
                    'password' => Hash::make($this->password),
                ])->assignRole('Dirección');

                $save_userProfile = DB::connection('mysql_segunda')->table('userprofile')->insert([
                    'nombre' => mb_strtoupper($this->name, 'UTF-8'),
                    'num_celualr' => $this->ncelular,
                    'correo' => $this->email,
                    'id_users' => $usu->id,
                ]);

                DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                    'user_id' => $usu->id,
                    'dependencia_id' => 7,
                    'created_at' => $fechayhora,
                    'estado' => 1,
                    'tipo_usuario' => $this->tipo_usuario,
                    'token' => Str::uuid()
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email', 'tipo_usuario']);
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO DIRECCION registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO DIRECCION no registrado']);
            }
        } elseif ($this->rol == 8) {
            try {
                DB::connection('mysql_segunda')->beginTransaction();
                $usu = User::create([
                    'dni' => $this->dni,
                    'password' => Hash::make($this->password),
                ])->assignRole('Jefatura de unidad Académica');

                $save_userProfile = DB::connection('mysql_segunda')->table('userprofile')->insert([
                    'nombre' => mb_strtoupper($this->name, 'UTF-8'),
                    'num_celualr' => $this->ncelular,
                    'correo' => $this->email,
                    'id_users' => $usu->id,
                ]);

                DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                    'user_id' => $usu->id,
                    'dependencia_id' => 8,
                    'created_at' => $fechayhora,
                    'estado' => 1,
                    'tipo_usuario' => $this->tipo_usuario,
                    'token' => Str::uuid()
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email', 'tipo_usuario']);
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO JEFATURA DE UNIDAD ACADÉMICA registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO JEFATURA DE UNIDAD ACADÉMICA no registrado']);
            }
        } elseif ($this->rol == 9) {
            try {
                DB::connection('mysql_segunda')->beginTransaction();
                $usu = User::create([
                    'dni' => $this->dni,
                    'password' => Hash::make($this->password),
                ])->assignRole('Jefatura de unidad Administrativa');

                $save_userProfile = DB::connection('mysql_segunda')->table('userprofile')->insert([
                    'nombre' => mb_strtoupper($this->name, 'UTF-8'),
                    'num_celualr' => $this->ncelular,
                    'correo' => $this->email,
                    'id_users' => $usu->id,
                ]);

                DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                    'user_id' => $usu->id,
                    'dependencia_id' => 9,
                    'created_at' => $fechayhora,
                    'estado' => 1,
                    'tipo_usuario' => $this->tipo_usuario,
                    'token' => Str::uuid()
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email', 'tipo_usuario']);
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO JEFATURA DE UNIDAD ADMINISTRATIVA registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO JEFATURA DE UNIDAD ADMINISTRATIVA no registrado']);
            }
        } elseif ($this->rol == 10) {
            try {
                DB::connection('mysql_segunda')->beginTransaction();
                $usu = User::create([
                    'dni' => $this->dni,
                    'password' => Hash::make($this->password),
                ])->assignRole('Secretaria Académica');

                $save_userProfile = DB::connection('mysql_segunda')->table('userprofile')->insert([
                    'nombre' => mb_strtoupper($this->name, 'UTF-8'),
                    'num_celualr' => $this->ncelular,
                    'correo' => $this->email,
                    'id_users' => $usu->id,
                ]);

                DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                    'user_id' => $usu->id,
                    'dependencia_id' => 10,
                    'created_at' => $fechayhora,
                    'estado' => 1,
                    'tipo_usuario' => $this->tipo_usuario,
                    'token' => Str::uuid()
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email', 'tipo_usuario']);
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO SECRETARIA ACADÉMICA registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO SECRETARIA ACADÉMICA no registrado']);
            }
        } elseif ($this->rol == 11) {
            try {
                DB::connection('mysql_segunda')->beginTransaction();
                $usu = User::create([
                    'dni' => $this->dni,
                    'password' => Hash::make($this->password),
                ])->assignRole('Coordin. Prog. Estudios Educ. Inicial');

                $save_userProfile = DB::connection('mysql_segunda')->table('userprofile')->insert([
                    'nombre' => mb_strtoupper($this->name, 'UTF-8'),
                    'num_celualr' => $this->ncelular,
                    'correo' => $this->email,
                    'id_users' => $usu->id,
                ]);

                DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                    'user_id' => $usu->id,
                    'dependencia_id' => 11,
                    'created_at' => $fechayhora,
                    'estado' => 1,
                    'tipo_usuario' => $this->tipo_usuario,
                    'token' => Str::uuid()
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email', 'tipo_usuario']);
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO COORDIN. PROG. ESTUDIOS EDUC. INICIAL registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO COORDIN. PROG. ESTUDIOS EDUC. INICIAL no registrado']);
            }
        } elseif ($this->rol == 12) {
            try {
                DB::connection('mysql_segunda')->beginTransaction();
                $usu = User::create([
                    'dni' => $this->dni,
                    'password' => Hash::make($this->password),
                ])->assignRole('Coordin. Prog. Estudios Primaria Epib');

                $save_userProfile = DB::connection('mysql_segunda')->table('userprofile')->insert([
                    'nombre' => mb_strtoupper($this->name, 'UTF-8'),
                    'num_celualr' => $this->ncelular,
                    'correo' => $this->email,
                    'id_users' => $usu->id,
                ]);

                DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                    'user_id' => $usu->id,
                    'dependencia_id' => 12,
                    'created_at' => $fechayhora,
                    'estado' => 1,
                    'tipo_usuario' => $this->tipo_usuario,
                    'token' => Str::uuid()
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email', 'tipo_usuario']);
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO COORDIN. PROG. ESTUDIOS PRIMARIA EPIB registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO COORDIN. PROG. ESTUDIOS PRIMARIA EPIB no registrado']);
            }
        } elseif ($this->rol == 13) {
            try {
                DB::connection('mysql_segunda')->beginTransaction();
                $usu = User::create([
                    'dni' => $this->dni,
                    'password' => Hash::make($this->password),
                ])->assignRole('Coordin. Prog. Estudios Educ. Física');

                $save_userProfile = DB::connection('mysql_segunda')->table('userprofile')->insert([
                    'nombre' => mb_strtoupper($this->name, 'UTF-8'),
                    'num_celualr' => $this->ncelular,
                    'correo' => $this->email,
                    'id_users' => $usu->id,
                ]);

                DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                    'user_id' => $usu->id,
                    'dependencia_id' => 13,
                    'created_at' => $fechayhora,
                    'estado' => 1,
                    'tipo_usuario' => $this->tipo_usuario,
                    'token' => Str::uuid()
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email', 'tipo_usuario']);
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO COORDIN. PROG. ESTUDIOS EDUC. FÍSICA registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO COORDIN. PROG. ESTUDIOS EDUC. FÍSICA no registrado']);
            }
        } elseif ($this->rol == 14) {
            try {
                DB::connection('mysql_segunda')->beginTransaction();
                $usu = User::create([
                    'dni' => $this->dni,
                    'password' => Hash::make($this->password),
                ])->assignRole('Coordin. Prog. Educac. Secundaria');

                $save_userProfile = DB::connection('mysql_segunda')->table('userprofile')->insert([
                    'nombre' => mb_strtoupper($this->name, 'UTF-8'),
                    'num_celualr' => $this->ncelular,
                    'correo' => $this->email,
                    'id_users' => $usu->id,
                ]);

                DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                    'user_id' => $usu->id,
                    'dependencia_id' => 14,
                    'created_at' => $fechayhora,
                    'estado' => 1,
                    'tipo_usuario' => $this->tipo_usuario,
                    'token' => Str::uuid()
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email', 'tipo_usuario']);
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO COORDIN. PROG. EDUCAC. SECUNDARIA registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO COORDIN. PROG. EDUCAC. SECUNDARIA no registrado']);
            }
        } elseif ($this->rol == 15) {
            try {
                DB::connection('mysql_segunda')->beginTransaction();
                $usu = User::create([
                    'dni' => $this->dni,
                    'password' => Hash::make($this->password),
                ])->assignRole('J. Area Acad. Educ. Básica Regular');

                $save_userProfile = DB::connection('mysql_segunda')->table('userprofile')->insert([
                    'nombre' => mb_strtoupper($this->name, 'UTF-8'),
                    'num_celualr' => $this->ncelular,
                    'correo' => $this->email,
                    'id_users' => $usu->id,
                ]);

                DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                    'user_id' => $usu->id,
                    'dependencia_id' => 15,
                    'created_at' => $fechayhora,
                    'estado' => 1,
                    'tipo_usuario' => $this->tipo_usuario,
                    'token' => Str::uuid()
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email', 'tipo_usuario']);
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO J. AREA ACAD. EDUC. BÁSICA REGULAR registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO J. AREA ACAD. EDUC. BÁSICA REGULAR no registrado']);
            }
        } elseif ($this->rol == 16) {
            try {
                DB::connection('mysql_segunda')->beginTransaction();
                $usu = User::create([
                    'dni' => $this->dni,
                    'password' => Hash::make($this->password),
                ])->assignRole('Jefe de Unidad de Formación Contínua');

                $save_userProfile = DB::connection('mysql_segunda')->table('userprofile')->insert([
                    'nombre' => mb_strtoupper($this->name, 'UTF-8'),
                    'num_celualr' => $this->ncelular,
                    'correo' => $this->email,
                    'id_users' => $usu->id,
                ]);

                DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                    'user_id' => $usu->id,
                    'dependencia_id' => 16,
                    'created_at' => $fechayhora,
                    'estado' => 1,
                    'tipo_usuario' => $this->tipo_usuario,
                    'token' => Str::uuid()
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email', 'tipo_usuario']);
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO JEFE DE UNIDAD DE FORMACIÓN CONTÍNUA registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO JEFE DE UNIDAD DE FORMACIÓN CONTÍNUA no registrado']);
            }
        } elseif ($this->rol == 17) {
            try {
                DB::connection('mysql_segunda')->beginTransaction();
                $usu = User::create([
                    'dni' => $this->dni,
                    'password' => Hash::make($this->password),
                ])->assignRole('J. Unidad de bienestar y empleabilidad');

                $save_userProfile = DB::connection('mysql_segunda')->table('userprofile')->insert([
                    'nombre' => mb_strtoupper($this->name, 'UTF-8'),
                    'num_celualr' => $this->ncelular,
                    'correo' => $this->email,
                    'id_users' => $usu->id,
                ]);

                DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                    'user_id' => $usu->id,
                    'dependencia_id' => 17,
                    'created_at' => $fechayhora,
                    'estado' => 1,
                    'tipo_usuario' => $this->tipo_usuario,
                    'token' => Str::uuid()
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email', 'tipo_usuario']);
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO J. UNIDAD DE BIENESTAR Y EMPLEABILIDAD registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO J. UNIDAD DE BIENESTAR Y EMPLEABILIDAD no registrado']);
            }
        } elseif ($this->rol == 18) {
            try {
                DB::connection('mysql_segunda')->beginTransaction();
                $usu = User::create([
                    'dni' => $this->dni,
                    'password' => Hash::make($this->password),
                ])->assignRole('J. Unidad de Investigación');

                $save_userProfile = DB::connection('mysql_segunda')->table('userprofile')->insert([
                    'nombre' => mb_strtoupper($this->name, 'UTF-8'),
                    'num_celualr' => $this->ncelular,
                    'correo' => $this->email,
                    'id_users' => $usu->id,
                ]);

                DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                    'user_id' => $usu->id,
                    'dependencia_id' => 18,
                    'created_at' => $fechayhora,
                    'estado' => 1,
                    'tipo_usuario' => $this->tipo_usuario,
                    'token' => Str::uuid()
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email', 'tipo_usuario']);
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO J. UNIDAD DE INVESTIGACIÓN registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO J. UNIDAD DE INVESTIGACIÓN no registrado']);
            }
        } elseif ($this->rol == 19) {
            try {
                DB::connection('mysql_segunda')->beginTransaction();
                $usu = User::create([
                    'dni' => $this->dni,
                    'password' => Hash::make($this->password),
                ])->assignRole('J. Area de Calidad');

                $save_userProfile = DB::connection('mysql_segunda')->table('userprofile')->insert([
                    'nombre' => mb_strtoupper($this->name, 'UTF-8'),
                    'num_celualr' => $this->ncelular,
                    'correo' => $this->email,
                    'id_users' => $usu->id,
                ]);

                DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                    'user_id' => $usu->id,
                    'dependencia_id' => 19,
                    'created_at' => $fechayhora,
                    'estado' => 1,
                    'tipo_usuario' => $this->tipo_usuario,
                    'token' => Str::uuid()
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email', 'tipo_usuario']);
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO J. AREA DE CALIDAD registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO J. AREA DE CALIDAD no registrado']);
            }
        } elseif ($this->rol == 20) {
            try {
                DB::connection('mysql_segunda')->beginTransaction();
                $usu = User::create([
                    'dni' => $this->dni,
                    'password' => Hash::make($this->password),
                ])->assignRole('Coord. del área de Práctica Profesional e investigación');

                $save_userProfile = DB::connection('mysql_segunda')->table('userprofile')->insert([
                    'nombre' => mb_strtoupper($this->name, 'UTF-8'),
                    'num_celualr' => $this->ncelular,
                    'correo' => $this->email,
                    'id_users' => $usu->id,
                ]);

                DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                    'user_id' => $usu->id,
                    'dependencia_id' => 20,
                    'created_at' => $fechayhora,
                    'estado' => 1,
                    'tipo_usuario' => $this->tipo_usuario,
                    'token' => Str::uuid()
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email', 'tipo_usuario']);
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO COORD. DEL ÁREA DE PRÁCTICA PROFESIONAL E INVESTIGACIÓN registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO COORD. DEL ÁREA DE PRÁCTICA PROFESIONAL E INVESTIGACIÓN no registrado']);
            }
        } elseif ($this->rol == 21) {
            try {
                DB::connection('mysql_segunda')->beginTransaction();
                $usu = User::create([
                    'dni' => $this->dni,
                    'password' => Hash::make($this->password),
                ])->assignRole('Biblioteca');

                $save_userProfile = DB::connection('mysql_segunda')->table('userprofile')->insert([
                    'nombre' => mb_strtoupper($this->name, 'UTF-8'),
                    'num_celualr' => $this->ncelular,
                    'correo' => $this->email,
                    'id_users' => $usu->id,
                ]);

                DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                    'user_id' => $usu->id,
                    'dependencia_id' => 21,
                    'created_at' => $fechayhora,
                    'estado' => 1,
                    'tipo_usuario' => $this->tipo_usuario,
                    'token' => Str::uuid()
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email', 'tipo_usuario']);
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO BIBLIOTECA registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO BIBLIOTECA no registrado']);
            }
        } elseif ($this->rol == 22) {
            try {
                DB::connection('mysql_segunda')->beginTransaction();
                $usu = User::create([
                    'dni' => $this->dni,
                    'password' => Hash::make($this->password),
                ])->assignRole('Y/O Cargos');

                $save_userProfile = DB::connection('mysql_segunda')->table('userprofile')->insert([
                    'nombre' => mb_strtoupper($this->name, 'UTF-8'),
                    'num_celualr' => $this->ncelular,
                    'correo' => $this->email,
                    'id_users' => $usu->id,
                ]);

                DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                    'user_id' => $usu->id,
                    'dependencia_id' => 22,
                    'created_at' => $fechayhora,
                    'estado' => 1,
                    'tipo_usuario' => $this->tipo_usuario,
                    'token' => Str::uuid()
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email', 'tipo_usuario']);
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO Y/O CARGOS registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO Y/O CARGOS no registrado']);
            }
        } elseif ($this->rol == 23) {
            try {
                DB::connection('mysql_segunda')->beginTransaction();
                $usu = User::create([
                    'dni' => $this->dni,
                    'password' => Hash::make($this->password),
                ])->assignRole('PPD');

                $save_userProfile = DB::connection('mysql_segunda')->table('userprofile')->insert([
                    'nombre' => mb_strtoupper($this->name, 'UTF-8'),
                    'num_celualr' => $this->ncelular,
                    'correo' => $this->email,
                    'id_users' => $usu->id,
                ]);

                DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                    'user_id' => $usu->id,
                    'dependencia_id' => 23,
                    'created_at' => $fechayhora,
                    'estado' => 1,
                    'tipo_usuario' => $this->tipo_usuario,
                    'token' => Str::uuid()
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email', 'tipo_usuario']);
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO PPD registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO PPD no registrado']);
            }
        } elseif ($this->rol == 24) {
            try {
                DB::connection('mysql_segunda')->beginTransaction();
                $usu = User::create([
                    'dni' => $this->dni,
                    'password' => Hash::make($this->password),
                ])->assignRole('Mesa de Partes');

                $save_userProfile = DB::connection('mysql_segunda')->table('userprofile')->insert([
                    'nombre' => mb_strtoupper($this->name, 'UTF-8'),
                    'num_celualr' => $this->ncelular,
                    'correo' => $this->email,
                    'id_users' => $usu->id,
                ]);

                DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                    'user_id' => $usu->id,
                    'dependencia_id' => 24,
                    'created_at' => $fechayhora,
                    'estado' => 1,
                    'tipo_usuario' => $this->tipo_usuario,
                    'token' => Str::uuid()
                ]);

                DB::connection('mysql_segunda')->commit();
                $this->reset(['dni', 'password', 'password_confirmation', 'rol', 'name', 'ncelular', 'email', 'tipo_usuario']);
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO MESA DE PARTES registrado con éxito.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('mensaje', ['msm' => 'Usuario TIPO MESA DE PARTES no registrado']);
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
