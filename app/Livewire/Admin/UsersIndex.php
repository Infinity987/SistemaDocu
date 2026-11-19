<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Log; // Importar Log
use Livewire\Attributes\On;


use Illuminate\Support\Facades\DB;

class UsersIndex extends Component
{
    use WithPagination;

    public int $confirmingId = 0;
    public $search;
    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }


    public function confirmarEliminacion(int $id): void
    {
        $this->confirmingId = $id;
        $this->dispatch('mostrar-modal-confirmacion');
    }

    #[On('eliminar')]
    public function eliminar(): void
    {
        try {
            // 1. Verificamos dependencias de forma eficiente (retorna true o false)
            $esDocente = DB::connection('mysql_segunda')->table('docente')->where('id_users', $this->confirmingId)->exists();
            $tieneDocumentos = DB::connection('mysql_documentario')->table('documentos')->where('emisor', $this->confirmingId)->exists();
            $tieneMovimientos = DB::connection('mysql_documentario')->table('movimiento')->where('id_user_receptor', $this->confirmingId)->exists();

            $tieneCursos = false;
            if ($esDocente) {
                $docente = DB::connection('mysql_segunda')->table('docente')->where('id_users', $this->confirmingId)->first();
                $tieneCursos = DB::connection('mysql_segunda')->table('docente_curso')->where('id_docente', $docente->iddocente)->exists();
            }

            // 2. Validamos si tiene CUALQUIERA de las dependencias (usando OR ||)
            // Esto corrige el bug lógico y coincide con el mensaje de alerta
            if ($tieneCursos || $tieneDocumentos || $tieneMovimientos) {
                $this->dispatch('alerta-info', [ // Asegúrate de que tu frontend escuche este evento (o usa 'alerta-exito' con icono 'warning' si es tu estándar)
                    'titulo' => '¡Atención!',
                    'mensaje' => 'Este usuario tiene cursos asignados o trámites documentarios, NO SE PUEDE ELIMINAR.',
                    'icono' => 'warning'
                ]);
                return; // Detenemos la ejecución aquí, no se elimina nada
            }

            // 3. Si no tiene dependencias, procedemos a eliminar dentro de una Transacción
            DB::beginTransaction();

            try {
                // Eliminamos en orden: primero tablas dependientes, luego la tabla principal (users)
                DB::connection('mysql_segunda')->table('userprofile')->where('id_users', $this->confirmingId)->delete();
                DB::table('model_has_roles')->where('model_id', $this->confirmingId)->delete();
                DB::connection('mysql_segunda')->table('docente')->where('id_users', $this->confirmingId)->delete();
                DB::table('users')->where('id', $this->confirmingId)->delete();
                DB::connection('mysql_documentario')->table('dependencia_user')->where('user_id', $this->confirmingId)->delete();

                DB::commit(); // Confirmamos los cambios

                $this->confirmingId = 0;
                $this->dispatch('alerta-exito', [
                    'titulo' => '¡Eliminado!',
                    'mensaje' => 'Usuario eliminado correctamente.',
                    'icono' => 'success'
                ]);
            } catch (\Throwable $e) {
                DB::rollBack(); // Revertimos cualquier eliminación parcial si algo falla

                // Opcional pero recomendado: Log::error('Error al eliminar usuario: ' . $e->getMessage());

                $this->dispatch('alerta-error', [
                    'titulo' => '¡Error!',
                    'mensaje' => 'Error inesperado al eliminar el usuario.',
                    'icono' => 'error'
                ]);
            }
        } catch (\Throwable $e) {
            // Captura errores en la fase de consulta inicial
            $this->dispatch('alerta-error', [
                'titulo' => '¡Error!',
                'mensaje' => 'Error inesperado al verificar dependencias.',
                'icono' => 'error'
            ]);
        }

        // try {
        //     $sitieneCursosAsi = DB::connection('mysql_segunda')->table('docente')->where('id_users', $this->confirmingId)->first();
        //     $existeDocu = DB::connection('mysql_documentario')->table('documentos')->where('emisor', $this->confirmingId)->first();
        //     $existeDocuMovi = DB::connection('mysql_documentario')->table('movimiento')->where('id_user_receptor', $this->confirmingId)->first();

        //     if (is_null($sitieneCursosAsi) and is_null($existeDocu) and is_null($existeDocuMovi)) {
        //         $delteUserProfile = DB::connection('mysql_segunda')->table('userprofile')->where('id_users', '=', $this->confirmingId)->delete();
        //         $delteUsers = DB::table('users')->where('id', '=', $this->confirmingId)->delete();
        //         $deleModeRoles = DB::table('model_has_roles')->where('model_id', '=', $this->confirmingId)->delete();
        //         $deledocente = DB::connection('mysql_segunda')->table('docente')->where('id_users', '=', $this->confirmingId)->delete();

        //         $this->confirmingId = 0;
        //         $this->dispatch('alerta-exito', [
        //             'titulo' => '¡Eliminado!',
        //             'mensaje' => 'Usuario eliminado correctamente.',
        //             'icono' => 'success'
        //         ]);
        //     } else {
        //         $existeIncriCurso = DB::connection('mysql_segunda')->table('docente_curso')->where('id_docente', $sitieneCursosAsi->iddocente)->exists();
        //         if ($existeIncriCurso == TRUE and !is_null($existeDocu) and !is_null($existeDocuMovi)) {
        //             $this->confirmingId = 0;
        //             $this->dispatch('alerta-exito', [
        //                 'titulo' => 'ALERTA!',
        //                 'mensaje' => 'Este usuario tiene cursos asignados o tramites documentarios, NO SE PUEDE ELIMINAR.',
        //                 'icono' => 'info'
        //             ]);
        //         } else {
        //             $delteUserProfile = DB::connection('mysql_segunda')->table('userprofile')->where('id_users', '=', $this->confirmingId)->delete();
        //             $delteUsers = DB::table('users')->where('id', '=', $this->confirmingId)->delete();
        //             $deleModeRoles = DB::table('model_has_roles')->where('model_id', '=', $this->confirmingId)->delete();
        //             $deledocente = DB::connection('mysql_segunda')->table('docente')->where('id_users', '=', $this->confirmingId)->delete();

        //             $this->confirmingId = 0;
        //             $this->dispatch('alerta-exito', [
        //                 'titulo' => '¡Eliminado!',
        //                 'mensaje' => 'Usuario eliminado correctamente.',
        //                 'icono' => 'success'
        //             ]);
        //         }
        //         // dd($existeIncriCurso);
        //     }
        // } catch (\Throwable $e) {
        //     $this->dispatch('alerta-exito', [
        //         'titulo' => 'ERROR!',
        //         'mensaje' => 'Error inesperado ...',
        //         'icono' => 'error'
        //     ]);
        // }
    }

    public function render()
    {
        $search = $this->search;

        $users = DB::table('gamnielb_admision.users as u')
            ->join('gamnielb_sia.userprofile as up', 'u.id', '=', 'up.id_users')
            ->select('u.id', 'u.dni', 'up.nombre', 'up.num_celualr', 'up.correo')
            ->where(function ($query) use ($search) {
                $query->where('u.dni', 'LIKE', "%$search%")
                    ->orWhere('up.nombre', 'LIKE', "%$search%");
            })
            ->orderBy('u.updated_at', 'DESC')
            ->paginate(10);

        return view('livewire.Admin.users-index', compact('users'));
    }
}
