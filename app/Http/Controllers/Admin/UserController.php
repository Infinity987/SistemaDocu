<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelIgnition\Recorders\DumpRecorder\Dump;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $nom_usu = DB::connection('mysql_segunda')->table('userprofile')->where('id_users', '=', Auth::user()->id)->pluck('nombre')->first();
        return view('admin.users.index', compact('nom_usu'));
    }

    public function registerUser(Request $request)
    {
        dd($request);
    }

    public function edit($user)
    {
        $nom_usu = DB::connection('mysql_segunda')->table('userprofile')->where('id_users', '=', Auth::user()->id)->pluck('nombre')->first();
        $use = User::leftJoin('gamnielb_sia.userprofile as up', 'users.id', '=', 'up.id_users')
            ->select('users.id', 'users.dni', 'up.nombre', 'up.num_celualr', 'up.correo')
            ->where('users.id', '=', $user)
            ->first();

        $roles = Role::all();
        return view('admin.users.edit', compact('use', 'roles', 'nom_usu'));
    }

    public function update(Request $request, User $user)
    {
        if (is_array($request->roles) && in_array(2, $request->roles)) {
            $search_doce = DB::connection('mysql_segunda')->table('docente')->where('id_users', '=', $user->id)->exists();
            if (!$search_doce) {
                $insert_tabla_doce = DB::connection('mysql_segunda')->table('docente')->insert([
                    'especialidad' => 'docente',
                    'estado' => 1,
                    'id_users' => $user->id,
                ]);
            }
        }

        $user->roles()->Sync($request->roles);
        return redirect()->route('admin.users.edit', $user)->with('info', 'Se asignó los roles');
    }
}
