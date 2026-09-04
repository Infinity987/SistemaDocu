<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class AsignarProyecto extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Control de navegación: 'bandeja' o 'gestionar'
    public $vistaActual = 'bandeja';

    // Proyecto seleccionado actualmente para gestionar
    public $proyectoIdSeleccionado = null;
    public $proyectoSeleccionado = null;

    // Buscador para la bandeja principal
    public $searchBandeja = '';

    // Variables para la búsqueda de alumnos
    public $searchDni = '';
    public $alumnoEncontrado = null;
    public $searchError = '';

    // 🚀 VARIABLES PARA LA GESTIÓN DE DOCENTES (AUTORIDADES)
    public $searchDocenteDni = '';         // Buscador de docente por DNI o Nombre
    public $docenteEncontrado = null;      // Datos del docente seleccionado
    public $searchDocenteError = '';
    public $id_rol_proyecto = '';          // 1 = Asesor, 2 = Jurado


    // Formulario de creación de proyectos
    public $titulo_investigacion = '';
    public $tipo_tramite = ''; 
    public $id_carrera = '';   

    public function updatingSearchBandeja()
    {
        $this->resetPage();
    }

    public function abrirModalNuevoProyecto()
    {
        $this->reset([
            'titulo_investigacion', 
            'tipo_tramite',
            'id_carrera',
            'searchDni',
            'alumnoEncontrado',
            'searchError',
            'searchDocenteDni',
            'docenteEncontrado',
            'searchDocenteError',
            'id_rol_proyecto'
        ]);
        
        $this->dispatch('abrir-modal-nuevo-proyecto');
    }

    public function guardarNuevoProyecto()
    {
        $this->validate([
            'titulo_investigacion' => 'required|string|min:10',
            'tipo_tramite' => 'required|in:1,2',
            'id_carrera' => 'required',
        ], [
            'titulo_investigacion.required' => 'El título de investigación es obligatorio.',
            'titulo_investigacion.min' => 'El título debe tener al menos 10 caracteres.',
            'tipo_tramite.required' => 'Seleccione si el trámite es para Bachiller o Título.',
            'id_carrera.required' => 'Debe seleccionar la escuela profesional de origen.',
        ]);

        try {
            $idProyectoInsertado = DB::connection('mysql_documentario')->table('proyecto_investigacion')->insertGetId([
                'titulo_investigacion' => trim($this->titulo_investigacion),
                'tipo_tramite' => (int)$this->tipo_tramite,
                'id_carrera' => (int)$this->id_carrera, 
                'estado' => 1 
            ]);

            $this->dispatch('cerrar-modal-nuevo-proyecto');

            $this->dispatch('swal:success', [
                'title' => '¡Proyecto Registrado!',
                'text' => 'El proyecto se guardó con éxito. Ahora puedes gestionarlo para agregar integrantes y autoridades.'
            ]);

            $this->reset(['titulo_investigacion', 'tipo_tramite', 'id_carrera']);

        } catch (\Exception $e) {
            session()->flash('error_global', 'Hubo un error al guardar: ' . $e->getMessage());
        }
    }

    // ==========================================
    // SECCIÓN ALUMNOS
    // ==========================================

    public function updatedSearchDni()
    {
        $this->alumnoEncontrado = null;
        $this->searchError = '';

        $dni = trim($this->searchDni);
        if (strlen($dni) < 8) return;

        $alumno = DB::connection('mysql')->table('postulante as p')
            ->select('p.idpostulante as dni', 'p.nombres_postulante', 'p.apellidos_pater_postulante', 'p.apellidos_mater_postulante', 'p.id_malla')
            ->where('p.idpostulante', $dni)
            ->first();

        if (!$alumno) {
            $this->searchError = 'DNI no encontrado en el sistema de postulantes.';
            return;
        }

        $id_carrera_local = null;
        $nombre_carrera_local = 'Sin Carrera';

        if ($alumno->id_malla) {
            $malla = DB::connection('mysql_segunda')->table('malla_curricular')
                ->select('carrera_malla')
                ->where('idmalla_curricular', $alumno->id_malla)
                ->first();

            if ($malla && $malla->carrera_malla) {
                $carreraInfo = DB::connection('mysql')->table('carreras')
                    ->select('idcarreras', 'nombre_de_carrera')
                    ->where('idcarreras', $malla->carrera_malla)
                    ->first();

                if ($carreraInfo) {
                    $id_carrera_local = $carreraInfo->idcarreras;
                    $nombre_carrera_local = $carreraInfo->nombre_de_carrera;
                }
            }
        }

        $this->alumnoEncontrado = [
            'dni' => $alumno->dni,
            'nombre_completo' => trim("{$alumno->apellidos_pater_postulante} {$alumno->apellidos_mater_postulante}, {$alumno->nombres_postulante}"),
            'id_carrera' => $id_carrera_local,
            'carrera' => $nombre_carrera_local
        ];
    }

    public function agregarAlumnoAlProyecto()
    {
        if (!$this->alumnoEncontrado) return;

        $existeEnProyecto = DB::connection('mysql_documentario')->table('alumno_proyecto')
            ->where('id_proyecto', $this->proyectoIdSeleccionado)
            ->where('id_alumno', $this->alumnoEncontrado['dni'])
            ->exists();

        if ($existeEnProyecto) {
            session()->flash('error_alumno', 'Este alumno ya está registrado en este proyecto.');
            return;
        }

        if ($this->proyectoSeleccionado->id_carrera && $this->alumnoEncontrado['id_carrera'] !== $this->proyectoSeleccionado->id_carrera) {
            session()->flash('error_alumno', 'Este alumno pertenece a una carrera diferente a la asignada en el proyecto.');
            return;
        }

        DB::connection('mysql_documentario')->table('alumno_proyecto')->insert([
            'id_proyecto' => $this->proyectoIdSeleccionado,
            'id_alumno' => $this->alumnoEncontrado['dni'],
            'estado_alumno' => '1',
            'fecha_integracion' => now()->toDateString()
        ]);

        $this->reset(['searchDni', 'alumnoEncontrado']);
        session()->flash('success_alumno', 'Alumno agregado correctamente.');
    }

    public function quitarAlumno($id_alumno_proyecto)
    {
        DB::connection('mysql_documentario')->table('alumno_proyecto')
            ->where('id_alumno_proyecto', $id_alumno_proyecto)
            ->delete();

        session()->flash('success_alumno', 'Alumno removido del proyecto.');
    }


    // ==========================================
    // 🚀 SECCIÓN GESTIÓN DE DOCENTES (AUTORIDADES)
    // ==========================================

    /**
     * Busca un docente activo con rol = 2 cruzando ambas bases de datos
     */
    public function updatedSearchDocenteDni()
    {
        $this->docenteEncontrado = null;
        $this->searchDocenteError = '';

        $dni = trim($this->searchDocenteDni);
        if (strlen($dni) < 8) return;

        // 1. Buscamos en mysql el usuario que tenga DNI coincidente y tenga el rol de docente (role_id = 2)
        $usuarioDocente = DB::connection('mysql')->table('users as u')
            ->join('model_has_roles as mhr', function($join) {
                $join->on('u.id', '=', 'mhr.model_id')
                     ->where('mhr.role_id', '=', 2); // Filtro estricto de Rol Docente
            })
            ->select('u.id as user_id', 'u.dni')
            ->where('u.dni', $dni)
            ->first();

        if (!$usuarioDocente) {
            $this->searchDocenteError = 'Docente no encontrado o no cuenta con rol de Docente asignado.';
            return;
        }

        // 2. Buscamos sus datos personales en la base de datos mysql_segunda usando su id de usuario (id_users)
        $perfilDocente = DB::connection('mysql_segunda')->table('userprofile')
            ->select('iduserProfile', 'nombre', 'num_celualr', 'correo')
            ->where('id_users', $usuarioDocente->user_id)
            ->first();

        if (!$perfilDocente) {
            $this->searchDocenteError = 'Se encontró el usuario pero no su perfil en la base de datos secundaria.';
            return;
        }

        $this->docenteEncontrado = [
            'id_users' => $usuarioDocente->user_id, // ID relacional del docente
            'dni' => $usuarioDocente->dni,
            'id_perfil' => $perfilDocente->iduserProfile,
            'nombre' => trim($perfilDocente->nombre),
            'celular' => $perfilDocente->num_celualr ?? 'Sin número',
            'correo' => $perfilDocente->correo ?? 'Sin correo'
        ];
    }

    /**
     * Vincula al docente como autoridad del proyecto (Asesor o Jurado)
     */
    public function agregarDocenteAlProyecto()
    {
        if (!$this->docenteEncontrado) return;

        $this->validate([
            'id_rol_proyecto' => 'required|in:1,2'
        ], [
            'id_rol_proyecto.required' => 'Debe seleccionar si el rol será Asesor o Jurado.'
        ]);

        // Evitar que el mismo docente tenga el mismo rol activo en el proyecto
        $existeAsignado = DB::connection('mysql_documentario')->table('autoridades_proyecto')
            ->where('id_proyecto', $this->proyectoIdSeleccionado)
            ->where('id_docente', $this->docenteEncontrado['id_users'])
            ->where('id_rol_proyecto', $this->id_rol_proyecto)
            ->where('estado_asignado', 1)
            ->exists();

        if ($existeAsignado) {
            session()->flash('error_docente', 'Este docente ya se encuentra asignado activamente con este rol en el proyecto.');
            return;
        }

        // Si es un Asesor (Rol 1), generalmente solo se permite uno activo por proyecto.
        if ($this->id_rol_proyecto == 1) {
            $tieneAsesorActivo = DB::connection('mysql_documentario')->table('autoridades_proyecto')
                ->where('id_proyecto', $this->proyectoIdSeleccionado)
                ->where('id_rol_proyecto', 1)
                ->where('estado_asignado', 1)
                ->exists();

            if ($tieneAsesorActivo) {
                session()->flash('error_docente', 'Este proyecto ya cuenta con un Asesor activo. Desactívelo antes de asignar uno nuevo.');
                return;
            }
        }

        // Guardamos físicamente en autoridades_proyecto sin agregar columnas ficticias
        DB::connection('mysql_documentario')->table('autoridades_proyecto')->insert([
            'id_proyecto' => $this->proyectoIdSeleccionado,
            'id_docente' => $this->docenteEncontrado['id_users'],
            'id_rol_proyecto' => (int)$this->id_rol_proyecto,
            'estado_asignado' => 1, // 1 = Activo
            'fecha_asignacion' => now()->toDateString()
        ]);

        $this->reset(['searchDocenteDni', 'docenteEncontrado', 'id_rol_proyecto']);
        session()->flash('success_docente', 'Autoridad agregada con éxito al proyecto.');
    }

    /**
     * Da de baja (desactiva) a un jurado o asesor registrando opcionalmente el motivo
     */
    public function darDeBajaAutoridad($id_autoridades_proyecto, $motivo = 'Reemplazo o cambio de autoridad')
    {
        DB::connection('mysql_documentario')->table('autoridades_proyecto')
            ->where('id_autoridades_proyecto', $id_autoridades_proyecto)
            ->update([
                'estado_asignado' => 0, // Desactivado
                'fecha_fin' => now()->toDateString(),
                'motivo_cambio' => $motivo
            ]);

        session()->flash('success_docente', 'La autoridad ha sido dada de baja del proyecto.');
    }

    public function gestionarProyecto($id_proyecto)
    {
        $this->proyectoIdSeleccionado = $id_proyecto;
        
        $this->proyectoSeleccionado = DB::connection('mysql_documentario')
            ->table('proyecto_investigacion as pi')
            ->leftJoin('tipo_tramite as tt', 'pi.tipo_tramite', '=', 'tt.id_tipo_tramite')
            ->select('pi.*', 'tt.nombre_tramite as nombre_tramite_texto')
            ->where('pi.id_proyecto', $id_proyecto)
            ->first();

        $this->vistaActual = 'gestionar';
        $this->reset([
            'searchDni', 'alumnoEncontrado', 'searchError',
            'searchDocenteDni', 'docenteEncontrado', 'id_rol_proyecto', 'searchDocenteError'
        ]);
    }

    public function volverALaBandeja()
    {
        $this->vistaActual = 'bandeja';
        $this->proyectoIdSeleccionado = null;
        $this->proyectoSeleccionado = null;
    }

    public function render()
    {
        $proyectos = [];
        $alumnosProyectoActual = [];
        $autoridadesProyectoActual = []; // Para almacenar las autoridades asignadas
        
        $carrerasDisponibles = DB::connection('mysql')->table('carreras')
            ->select('idcarreras', 'nombre_de_carrera')
            ->orderBy('nombre_de_carrera', 'asc')
            ->get();

        if ($this->vistaActual === 'bandeja') {
            $query = DB::connection('mysql_documentario')->table('proyecto_investigacion as pi')
                ->leftJoin('tipo_tramite as tt', 'pi.tipo_tramite', '=', 'tt.id_tipo_tramite')
                ->select(
                    'pi.id_proyecto', 
                    'pi.titulo_investigacion', 
                    'pi.tipo_tramite', 
                    'tt.nombre_tramite as nombre_tramite_texto', 
                    'pi.id_carrera', 
                    'pi.estado'
                );

            if (!empty(trim($this->searchBandeja))) {
                $query->where('pi.titulo_investigacion', 'LIKE', '%' . trim($this->searchBandeja) . '%');
            }

            $proyectos = $query->orderBy('pi.id_proyecto', 'desc')->paginate(10);

            foreach ($proyectos as $proyecto) {
                $integrantesIds = DB::connection('mysql_documentario')->table('alumno_proyecto')
                    ->where('id_proyecto', $proyecto->id_proyecto)
                    ->pluck('id_alumno')
                    ->toArray();

                $nombresIntegrantes = [];
                if (count($integrantesIds) > 0) {
                    $nombresIntegrantes = DB::connection('mysql')->table('postulante')
                        ->select(DB::raw("CONCAT(apellidos_pater_postulante, ' ', apellidos_mater_postulante, ', ', nombres_postulante) as nombre_completo"))
                        ->whereIn('idpostulante', $integrantesIds)
                        ->pluck('nombre_completo')
                        ->toArray();
                }
                $proyecto->integrantes_lista = $nombresIntegrantes;

                if ($proyecto->id_carrera) {
                    $carrera = DB::connection('mysql')->table('carreras')
                        ->where('idcarreras', $proyecto->id_carrera)
                        ->value('nombre_de_carrera');
                    $proyecto->nombre_carrera = $carrera ?? 'Carrera no identificada';
                } else {
                    $proyecto->nombre_carrera = 'Sin Carrera';
                }

                $proyecto->estado_texto = match((int)$proyecto->estado) {
                    1 => 'REGISTRADO',
                    2 => 'EN EVALUACIÓN',
                    3 => 'APTO SUSTENTACIÓN',
                    4 => 'APROBADO',
                    default => 'DESCONOCIDO'
                };
            }
        } else {
            // Cargar alumnos asignados al proyecto actual
            $alumnosProyectoActual = DB::connection('mysql_documentario')->table('alumno_proyecto as ap')
                ->where('ap.id_proyecto', $this->proyectoIdSeleccionado)
                ->get();

            foreach ($alumnosProyectoActual as $ap) {
                $datosPostulante = DB::connection('mysql')->table('postulante')
                    ->select(DB::raw("CONCAT(apellidos_pater_postulante, ' ', apellidos_mater_postulante, ', ', nombres_postulante) as nombre_completo"))
                    ->where('idpostulante', $ap->id_alumno)
                    ->first();
                $ap->nombre_completo = $datosPostulante ? $datosPostulante->nombre_completo : 'Datos de alumno no localizados';
            }

            // 🚀 CARGAR AUTORIDADES (Asesores y Jurados) del proyecto actual
            $autoridadesProyectoActual = DB::connection('mysql_documentario')->table('autoridades_proyecto as aut')
                ->where('aut.id_proyecto', $this->proyectoIdSeleccionado)
                ->orderBy('aut.estado_asignado', 'desc') // Los activos primero
                ->orderBy('aut.id_rol_proyecto', 'asc')  // Asesores primero, luego Jurados
                ->get();

            foreach ($autoridadesProyectoActual as $aut) {
                // Obtenemos los datos del docente cruzando con mysql_segunda userprofile
                $perfil = DB::connection('mysql_segunda')->table('userprofile')
                    ->select('nombre', 'num_celualr', 'correo')
                    ->where('id_users', $aut->id_docente)
                    ->first();

                $aut->nombre_completo = $perfil ? $perfil->nombre : 'Docente sin Datos en Perfil';
                $aut->celular = $perfil ? $perfil->num_celualr : '-';
                $aut->correo = $perfil ? $perfil->correo : '-';
            }
        }

        return view('livewire.asignar-proyecto', [
            'proyectos' => $proyectos,
            'alumnosProyectoActual' => $alumnosProyectoActual,
            'autoridadesProyectoActual' => $autoridadesProyectoActual,
            'carrerasDisponibles' => $carrerasDisponibles
        ]);
    }
}