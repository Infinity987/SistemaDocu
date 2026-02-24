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
            $sitieneCursosAsi = DB::connection('mysql_segunda')->table('docente')->where('id_users', $this->confirmingId)->first();
            if (is_null($sitieneCursosAsi)) {
                $delteUserProfile = DB::connection('mysql_segunda')->table('userprofile')->where('id_users', '=', $this->confirmingId)->delete();
                $delteUsers = DB::table('users')->where('id', '=', $this->confirmingId)->delete();
                $deleModeRoles = DB::table('model_has_roles')->where('model_id', '=', $this->confirmingId)->delete();
                $deledocente = DB::connection('mysql_segunda')->table('docente')->where('id_users', '=', $this->confirmingId)->delete();

                $this->confirmingId = 0;
                $this->dispatch('alerta-exito', [
                    'titulo' => '¡Eliminado!',
                    'mensaje' => 'Usuario eliminado correctamente.',
                    'icono' => 'success'
                ]);
            } else {
                $existeIncriCurso = DB::connection('mysql_segunda')->table('docente_curso')->where('id_docente', $sitieneCursosAsi->iddocente)->exists();
                if ($existeIncriCurso) {
                    $this->confirmingId = 0;
                    $this->dispatch('alerta-exito', [
                        'titulo' => 'ALERTA!',
                        'mensaje' => 'Este usuario tiene cursos asignados, nose puede eliminar.',
                        'icono' => 'info'
                    ]);
                } else {
                    $delteUserProfile = DB::connection('mysql_segunda')->table('userprofile')->where('id_users', '=', $this->confirmingId)->delete();
                    $delteUsers = DB::table('users')->where('id', '=', $this->confirmingId)->delete();
                    $deleModeRoles = DB::table('model_has_roles')->where('model_id', '=', $this->confirmingId)->delete();
                    $deledocente = DB::connection('mysql_segunda')->table('docente')->where('id_users', '=', $this->confirmingId)->delete();

                    $this->confirmingId = 0;
                    $this->dispatch('alerta-exito', [
                        'titulo' => '¡Eliminado!',
                        'mensaje' => 'Usuario eliminado correctamente.',
                        'icono' => 'success'
                    ]);
                }
                // dd($existeIncriCurso);
            }
        } catch (\Throwable $e) {
            $this->dispatch('alerta-exito', [
                'titulo' => 'ERROR!',
                'mensaje' => 'Error inesperado ...',
                'icono' => 'error'
            ]);
        }
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
