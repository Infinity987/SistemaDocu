<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination; // 👈 Importante para la paginación reactiva
use Illuminate\Support\Facades\DB;

class GestionarPermisosPagos extends Component
{
    use WithPagination; // 👈 Activamos el uso de paginación en el componente

    // Estilos de paginación para Bootstrap (AdminLTE usa Bootstrap 4)
    protected $paginationTheme = 'bootstrap';

    // Propiedades para el formulario
    public $tupa_seleccionado = '';
    public $dependencia_seleccionada = '';

    // Propiedad para el buscador de la tabla de permisos
    public $searchMatriz = '';

    // Colecciones estáticas para los selectores del formulario
    public $conceptosTupa = [];
    public $dependencias = [];
    
    // Lista de conceptos huérfanos (esta la dejamos en memoria o se actualiza)
    public $tupasHuerfanos = [];

    public function mount()
    {
        // 1. Obtener conceptos únicos para el formulario
        $this->conceptosTupa = DB::connection('mysql_pagos')->table('tupas')
            ->select('tupDes')
            ->distinct()
            ->orderBy('tupDes', 'asc')
            ->pluck('tupDes')
            ->toArray();

        // 2. Obtener las dependencias para el formulario
        $this->dependencias = DB::connection('mysql')->table('roles')
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get()
            ->toArray();

        // 3. Cargar inicialmente los TUPAs huérfanos
        $this->actualizarHuerfanos();
    }

    // Limpia la paginación cada vez que el usuario escribe en el buscador de la tabla
    public function updatingSearchMatriz()
    {
        $this->resetPage();
    }

    public function actualizarHuerfanos()
    {
        // Obtener conceptos que ya tienen dueño
        $tupasAsignados = DB::connection('mysql_pagos')->table('ver_pagos')
            ->pluck('nombre_tupa')
            ->unique()
            ->toArray();

        // Cargar huérfanos con la corrección DISTINCT para evitar errores de MySQL
        $this->tupasHuerfanos = DB::connection('mysql_pagos')->table('tupas')
            ->select('tupCod', 'tupDes')
            ->whereNotIn('tupDes', $tupasAsignados)
            ->distinct()
            ->orderBy('tupCod', 'asc')
            ->get()
            ->toArray();
    }

    public function guardarPermiso()
    {
        if (empty($this->tupa_seleccionado) || empty($this->dependencia_seleccionada)) {
            session()->flash('error', 'Debe seleccionar un concepto TUPA y escribir una Dependencia.');
            return;
        }

        $rol = DB::connection('mysql')->table('roles')
            ->where('name', trim($this->dependencia_seleccionada))
            ->first();

        if (!$rol) {
            session()->flash('error', 'La dependencia escrita no es válida. Seleccione una de la lista.');
            return;
        }

        $existe = DB::connection('mysql_pagos')->table('ver_pagos')
            ->where('id_dependencia', $rol->id)
            ->where('nombre_tupa', $this->tupa_seleccionado)
            ->exists();

        if ($existe) {
            session()->flash('error', 'Esta dependencia ya cuenta con el permiso para este concepto.');
            return;
        }

        DB::connection('mysql_pagos')->table('ver_pagos')->insert([
            'id_dependencia' => $rol->id,
            'id_user' => null,
            'nombre_tupa' => $this->tupa_seleccionado,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->tupa_seleccionado = '';
        $this->dependencia_seleccionada = '';
        
        $this->actualizarHuerfanos();
        session()->flash('message', 'Permiso asignado correctamente.');
    }

    public function eliminarPermiso($id_verpagos)
    {
        DB::connection('mysql_pagos')->table('ver_pagos')
            ->where('id_verpagos', $id_verpagos)
            ->delete();

        $this->actualizarHuerfanos();
        session()->flash('message', 'Permiso revocado correctamente.');
    }

    public function render()
    {
        // 🔎 CONSULTA DINÁMICA CON BUSCADOR Y PAGINACIÓN INTEGRADA
        $queryPermisos = DB::connection('mysql_pagos')->table('ver_pagos');

        // Mapeamos los roles locales para poder filtrar por nombre si el usuario busca una oficina
        $rolesMapeados = DB::connection('mysql')->table('roles')->pluck('name', 'id')->toArray();

        // Si el usuario escribe en el buscador de la tabla
        if (!empty($this->searchMatriz)) {
            $search = trim($this->searchMatriz);

            // Buscamos qué IDs de dependencias coinciden con el texto escrito
            $idsDependenciasFiltradas = array_keys(
                array_filter($rolesMapeados, function($name) use ($search) {
                    return stripos($name, $search) !== false;
                })
            );

            $queryPermisos->where(function($q) use ($search, $idsDependenciasFiltradas) {
                $q->where('nombre_tupa', 'like', "%{$search}%")
                  ->orWhereIn('id_dependencia', $idsDependenciasFiltradas);
            });
        }

        // Paginamos los resultados directamente desde la BD (ej: de 10 en 10)
        $permisosPaginados = $queryPermisos->orderBy('id_dependencia', 'asc')->paginate(10);

        // Transformamos los datos para añadirles el nombre de la dependencia antes de renderizar la vista
        $permisosPaginados->getCollection()->transform(function ($permiso) use ($rolesMapeados) {
            $permiso->nombre_dependencia = $rolesMapeados[$permiso->id_dependencia] ?? 'Dependencia Desconocida';
            return $permiso;
        });

        // Contamos el total global para el globito (badge) de la pestaña sin importar la página actual
        $totalPermisosActivos = DB::connection('mysql_pagos')->table('ver_pagos')->count();

        return view('livewire.gestionar-permisos-pagos', [
            'permisosActivos' => $permisosPaginados,
            'totalActivosCount' => $totalPermisosActivos
        ]);
    }
}