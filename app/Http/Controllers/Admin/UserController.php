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
        $id_depen = session('dependencia_id');
        $rol = DB::connection('mysql_documentario')->table('dependencias')->where('iddependencias', $id_depen)->first();
        $cont_est = DB::connection('mysql_documentario')->select('SELECT estado.idestado, COALESCE(COUNT(movimiento.iddocumentos), 0) as cont_estado
                            FROM estado
                            LEFT JOIN movimiento ON movimiento.idestado = estado.idestado
                            AND movimiento.iddependencias_receptor = ?
                            WHERE estado.idestado IN (1,2,3)
                            GROUP BY estado.idestado;', [$id_depen]);

        return view('admin.users.index')->with('nom_usu', $nom_usu)->with('id_depen', $id_depen)->with('rol', $rol)->with('cont_est', $cont_est);
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
        // if (is_array($request->roles) && in_array(2, $request->roles)) {
        //     $search_doce = DB::connection('mysql_segunda')->table('docente')->where('id_users', '=', $user->id)->exists();
        //     if (!$search_doce) {
        //         $insert_tabla_doce = DB::connection('mysql_segunda')->table('docente')->insert([
        //             'especialidad' => 'docente',
        //             'estado' => 1,
        //             'id_users' => $user->id,
        //         ]);
        //     }
        // }

        // $user->roles()->Sync($request->roles);
        // return redirect()->route('admin.users.edit', $user)->with('info', 'Se asignó los roles');

        $user->roles()->Sync($request->roles);
        $verTodasDepenUsu = DB::connection('mysql_documentario')->table('dependencia_user')
            ->select('dependencia_id', 'estado')
            ->where('user_id', $user->id)
            ->Where('estado', 1)
            ->get();
        $actuales = $verTodasDepenUsu->pluck('dependencia_id')->map(fn($id) => (string) $id)->toArray();

        if (empty($request->roles)) {
            foreach ($actuales as $actu) {
                $update_cero = DB::connection('mysql_documentario')->update('UPDATE dependencia_user SET updated_at = ?, estado = ? WHERE user_id = ? AND dependencia_id = ? AND estado = ?', [now(), 0, $user->id, $actu, 1]);
            }
        } else {
            $nuevas = $request->roles;
            $faltantes = array_diff($actuales, $nuevas);
            $agregados = array_diff($nuevas, $actuales);
            if (empty($faltantes) && empty($agregados)) {
            } elseif (empty($faltantes)) {
                foreach ($agregados as $agrega_depen) {
                    DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                        'user_id' => $user->id,
                        'dependencia_id' => $agrega_depen,
                        'created_at' => now(),
                        'estado' => 1
                    ]);
                }
            } else {
                if (!empty($agregados)) {
                    foreach ($agregados as $agrega_depen) {
                        DB::connection('mysql_documentario')->table('dependencia_user')->insert([
                            'user_id' => $user->id,
                            'dependencia_id' => $agrega_depen,
                            'created_at' => now(),
                            'estado' => 1
                        ]);
                    }
                }
                foreach ($faltantes as $quitd) {
                    $update_cero = DB::connection('mysql_documentario')->update('UPDATE dependencia_user SET updated_at = ?, estado = ? WHERE user_id = ? AND dependencia_id = ? AND estado = ?', [now(), 0, $user->id, $quitd, 1]);
                }
            }
        }
        // dump("----------------------------------");
        // dd('pause');

        return redirect()->route('admin.users.edit', $user)->with('info', 'Se asignó los roles');
    }
}
