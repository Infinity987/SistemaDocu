<?php

namespace App\Http\Controllers\postulante;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Throwable;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelIgnition\Recorders\DumpRecorder\Dump;
use Illuminate\Support\Carbon;

class postulanteController extends Controller
{

    public function index()
    {

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Obtener el DNI del usuario autenticado
        $userIddni = $user->dni;


        // Realizar la consulta en la base de datos
        $existeDni = DB::table('postulante')->where('idpostulante', $userIddni)->exists();

        // Pasar los datos a la vista
        $mostrardatos = $existeDni;
        $mostrarFormulario = !$existeDni;

        $postulantesdatos = DB::select('SELECT
            postulante.idpostulante,
            postulante.edad_postulante,
            postulante.apellidos_pater_postulante,
            postulante.apellidos_mater_postulante,
            postulante.nombres_postulante,
            postulante.fecha_de_nacimiento_postu,
            postulante.celular,
            postulante.correo,
            postulante.lengua_mater,
            postulante.lengua_secun,
            postulante.direccion_domicilio,
            postulante.colegio,
            postulante.codigo_modular,
            postulante.direccion_colegio,
            postulante.año_de_termino_colegio,
            postulante.foto_postulante,
            nacimiento.Distrito AS distrito_nacimiento,
            nacimiento.Provincia AS provincia_nacimiento,
            nacimiento.Departamento AS departamento_nacimiento,
            domicilio.Distrito AS distrito_domicilio,
            domicilio.Provincia AS provincia_domicilio,
            domicilio.Departamento AS departamento_domicilio,
            colegio.Distrito AS distrito_colegio,
            colegio.Provincia AS provincia_colegio,
            colegio.Departamento AS departamento_colegio
        FROM
            postulante
        LEFT JOIN
            ubigeo AS nacimiento ON postulante.idubigeo_nacimiento = nacimiento.Ubigeo
        LEFT JOIN
            ubigeo AS domicilio ON postulante.idubigeo_domicilio = domicilio.Ubigeo
        LEFT JOIN
            ubigeo AS colegio ON postulante.idubigeo_colegio = colegio.Ubigeo where postulante.idpostulante = ?', [$userIddni]);


        $tipo_cole = DB::select('SELECT * FROM tipo_colegio ');
        $lenguasmater = DB::select('SELECT * FROM lenguas ');
        $discapacidad = DB::select('SELECT * FROM discapacidad');

        //identidad etnica
        $idenEtnicas = DB::table('identidad_etnica')->get();

        //MODALIDAD BECA
        $moda_beca = DB::table('modalidad_beca')->get();

        //ESTU PREVIOS
        $estu_previos = DB::table('estudios_previos')->get();

        //estado_civil
        $est_civil = DB::table('est_civil')->get();

        // ape, nom y carre del usuario;
        $nom_usuC = DB::connection('mysql_segunda')
            ->table('gamnielb_admision.postulante as p')
            ->select('p.apellidos_pater_postulante', 'p.apellidos_mater_postulante', 'p.nombres_postulante', 'c.nombre_de_carrera')
            ->join('malla_curricular as mc', 'p.id_malla', '=', 'mc.idmalla_curricular')
            ->join('gamnielb_admision.carreras as c', 'mc.carrera_malla', '=', 'c.idcarreras')
            ->where('p.idpostulante', '=', Auth::user()->dni)->first();

        //programa de estud
        $programa_estu = DB::table('carreras')->get();

        if (is_null($nom_usuC)) {
            $nom_usu = 'Postulante - Cerrar Sesión';
            $nom_carre = 'Postulante';
        } else {
            $nom_usu = $nom_usuC->apellidos_pater_postulante . ' ' . $nom_usuC->apellidos_mater_postulante . ' ' . $nom_usuC->nombres_postulante;
            $nom_carre = $nom_usuC->nombre_de_carrera;
        }

        //para ver si tiene una inscripcion para postular
        $ver_si_esta_inscri = DB::select('SELECT p.nombre_proceso, m.nombre_modalidad, i.idinscripcion FROM inscripcion i
            INNER JOIN procesos p ON i.proceso_distin = p.idprocesos
            INNER JOIN modalidad m ON i.modalidad_distin = m.idmodalidad
        WHERE i.idpostulante = ? AND p.estado_proceso = ? LIMIT 1;', [$userIddni, 1]);

        return view('postulantes.index', compact('user', 'tipo_cole', 'mostrardatos', 'mostrarFormulario', 'postulantesdatos', 'userIddni', 'lenguasmater', 'discapacidad', 'nom_usu', 'nom_carre', 'idenEtnicas', 'moda_beca', 'estu_previos', 'est_civil', 'programa_estu', 'ver_si_esta_inscri'));
    }

    public function agregarpostulante(Request $request)
    {

        // dd($request);

        // Verifica si el DNI ya existe en la base de datos
        $dniExistente = DB::table('postulante')->where('idpostulante', $request->dni)->first();

        if ($dniExistente) {
            // Si el DNI existe, redirige con un mensaje de error
            return redirect()->route('postulante.index')->with('error', 'DNI duplicado. El postulante ya está registrado.');
        }

        // Verificar si se cargó un archivo de imagen
        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $destinationPath = 'fotos_postulantes/';
            // Obtener la extensión original del archivo
            $extension = $file->getClientOriginalExtension();
            // Crear el nombre del archivo usando el DNI (y la extensión)
            $filename = $request->dni . '.' . $extension;
            // Mover el archivo a la carpeta de destino
            $file->move($destinationPath, $filename);
            // Construir la ruta final
            $finalnomb = $destinationPath . $filename;
        } else {
            // Si no se ha cargado imagen, asigna null (o un valor por defecto si lo prefieres)
            $finalnomb = null;
        }

        $request->merge([
            'apellido_paterno' => strtoupper($request->apellido_paterno),
            'apellido_materno' => strtoupper($request->apellido_materno),
            'nombres' => strtoupper($request->nombres),
            'direccion' => strtoupper($request->direccion),
            'lengua_secundaria' => strtoupper($request->lengua_secundaria),
            'correo_electronico' => strtoupper($request->correo_electronico),

            'con_proyecto' => strtoupper($request->con_proyecto),
            'nom_proyecto' => strtoupper($request->nom_proyecto),
        ]);

        try {
            DB::beginTransaction();
            $agregarpostulante = DB::insert("INSERT INTO `postulante`(`idpostulante`, `tipodocumento`, `nacionalidad`, `edad_postulante`, `genero_postulante`, `apellidos_pater_postulante`, `apellidos_mater_postulante`, `nombres_postulante`, `fecha_de_nacimiento_postu`, `idubigeo_nacimiento`, `lengua_mater`, `lengua_secun`, `direccion_domicilio`, `idubigeo_domicilio`, `celular`, `correo`, `colegio`, `codigo_modular`, `direccion_colegio`, `año_de_termino_colegio`, `idubigeo_colegio`, `idtipo_colegio`, `foto_postulante`, `discapacidad`, `tipo_discapacidad`, num_conadis, id_identidad_etnica, id_est_civil, rebred, num_reso_rebred, con_beca, id_moda_beca, reso_beca, con_hijos, cant_hijos, con_trabajo, tipo_trabajo, con_estudios, id_estu_previos, num_reso_metas, fecha_inscripcion, con_proyecto, nom_proyecto, opcion_carrera) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);", [
                $request->dni,

                $request->tipodocumento,
                $request->nacionalidad,

                $request->edad,
                $request->genero_postu,
                $request->apellido_paterno,
                $request->apellido_materno,
                $request->nombres,
                $request->fecha_nacimiento,
                $request->distrito_nacimiento,
                $request->lengua_matern,
                $request->lengua_secundaria,
                $request->direccion,
                $request->distrito_domicilio,
                $request->celular,
                $request->correo_electronico,
                $request->nombre_colegio,
                $request->codigo_modular,
                $request->direccion_colegio,
                $request->anio_promocion,
                $request->distrito_cole,
                $request->tipo_colegio,
                $finalnomb,
                $request->discapa_postu,
                $request->tipo_discapacidad,

                $request->num_conadis,
                $request->identidadetnica,
                $request->est_civil,
                $request->rebred,
                $request->num_reso_rebred,
                $request->con_beca,
                $request->moda_beca,
                $request->num_reso_beca,
                $request->con_hijos,
                $request->cant_hijos,
                $request->con_trabajo,
                $request->tipo_trabajo,
                $request->con_estudios,
                $request->estu_previos,
                $request->num_reso_metas,
                now(),

                $request->con_proyecto,
                $request->nom_proyecto,

                $request->opcion_carrera,

            ]);

            DB::commit();
            return redirect()->route('postulante.index')->with('success', 'Postulante Agregado con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('postulante.index')->with('error', 'Error al insertar datos del Postulante ...');
        }
    }

    public function verpostulantes()
    {
        return view('postulantes.verpostulantes');
    }

    public function ajaxPostulantes()
    {
        $usuario = Auth::user();
        if ($usuario->hasRole('admin')) {
            $postulantes = DB::select("SELECT
                postulante.idpostulante,
                CONCAT(postulante.apellidos_pater_postulante, ' ', postulante.apellidos_mater_postulante, ' ', postulante.nombres_postulante) AS nombre_postu,
                postulante.celular,
                postulante.correo, postulante.fecha_inscripcion
                FROM
                    postulante
                WHERE postulante.id_malla IS NOT NULL
                ORDER BY fecha_inscripcion DESC");
            return DataTables::of(collect($postulantes))->toJson();
        }
        if ($usuario->hasRole('admision')) {
            // $postulantes = DB::select("SELECT
            //     postulante.idpostulante,
            //     CONCAT(postulante.apellidos_pater_postulante, ' ', postulante.apellidos_mater_postulante, ' ', postulante.nombres_postulante) AS nombre_postu,
            //     postulante.celular,
            //     postulante.correo, postulante.fecha_inscripcion, carreras.nombre_de_carrera
            //     FROM
            //         postulante
            //     LEFT JOIN carreras ON postulante.opcion_carrera = carreras.idcarreras
            //     WHERE postulante.id_malla IS NULL OR postulante.id_malla = 0
            //     ORDER BY fecha_inscripcion DESC");

            $postulantes = DB::select("SELECT users.dni as idpostulante,
                    COALESCE(CONCAT(postulante.apellidos_pater_postulante, ' ', postulante.apellidos_mater_postulante, ' ', postulante.nombres_postulante), '- ---------- -') AS nombre_postu,
                    COALESCE(postulante.celular, '- ---------- -') AS celular,
                    COALESCE(postulante.correo, '- ---------- -') AS correo,
                    COALESCE(postulante.fecha_inscripcion, '- ---------- -') AS fecha_inscripcion,
                    COALESCE(carreras.nombre_de_carrera, '- ---------- -') AS nombre_de_carrera
                FROM
                    users
                LEFT JOIN postulante ON users.dni = postulante.idpostulante
                LEFT JOIN gamnielb_sia.userprofile AS up ON up.id_users = users.id
                LEFT JOIN carreras ON postulante.opcion_carrera = carreras.idcarreras
                WHERE (postulante.id_malla IS NULL OR postulante.id_malla = 0) AND up.nombre IS NULL
                ORDER BY fecha_inscripcion DESC;");
            return DataTables::of(collect($postulantes))->toJson();
        }

        abort(403, 'Acceso no autorizado.');
    }

    public function verDetalle($idpostulante)
    {
        $usuario = Auth::user();
        $idusu = DB::table('users')->where('dni', $idpostulante)->select('id')->first();
        // dd($idusu);

        // Si el usuario tiene los roles 'admisión' o 'admin', puede ver cualquier postulante
        if ($usuario->hasRole('admin')) {
            $tipo_cole = DB::select('SELECT * FROM tipo_colegio ');
            $lenguasmater = DB::select('SELECT * FROM lenguas ');
            $discapacidad = DB::select('SELECT * FROM discapacidad');

            //identidad etnica
            $idenEtnicas = DB::table('identidad_etnica')->get();

            //MODALIDAD BECA
            $moda_beca = DB::table('modalidad_beca')->get();

            //ESTU PREVIOS
            $estu_previos = DB::table('estudios_previos')->get();

            //estado_civil
            $est_civil = DB::table('est_civil')->get();

            $datospostuss = DB::select('SELECT
            postulante.idpostulante,
            postulante.nacionalidad,
            postulante.tipodocumento,
            postulante.genero_postulante,
            postulante.edad_postulante,
            postulante.apellidos_pater_postulante,
            postulante.apellidos_mater_postulante,
            postulante.nombres_postulante,
            postulante.fecha_de_nacimiento_postu,
            postulante.celular,
            postulante.correo,
            postulante.lengua_mater,
            postulante.lengua_secun,
            postulante.direccion_domicilio,
            postulante.colegio,
            postulante.codigo_modular,
            postulante.direccion_colegio,
            postulante.año_de_termino_colegio,
            postulante.foto_postulante,
            nacimiento.Distrito AS distrito_nacimiento,
            nacimiento.Provincia AS provincia_nacimiento,
            nacimiento.Departamento AS departamento_nacimiento,
            domicilio.Distrito AS distrito_domicilio,
            domicilio.Provincia AS provincia_domicilio,
            domicilio.Departamento AS departamento_domicilio,
            colegio.Distrito AS distrito_colegio,
            colegio.Provincia AS provincia_colegio,
            colegio.Departamento AS departamento_colegio,

            postulante.idubigeo_colegio,
            postulante.idtipo_colegio,

            postulante.discapacidad,
            postulante.tipo_discapacidad,
            postulante.num_conadis,

            postulante.id_identidad_etnica,
            postulante.id_est_civil,
            postulante.con_hijos,
            postulante.cant_hijos,
            postulante.rebred,
            postulante.num_reso_rebred,
            postulante.con_beca,
            postulante.id_moda_beca,
            postulante.reso_beca,
            postulante.con_trabajo,
            postulante.tipo_trabajo,
            postulante.con_estudios,
            postulante.id_estu_previos,
            postulante.num_reso_metas,
            postulante.fecha_de_nacimiento_postu,

            postulante.con_proyecto,
            postulante.nom_proyecto

            FROM
                postulante
            LEFT JOIN
                ubigeo AS nacimiento ON postulante.idubigeo_nacimiento = nacimiento.Ubigeo
            LEFT JOIN
                ubigeo AS domicilio ON postulante.idubigeo_domicilio = domicilio.Ubigeo
            LEFT JOIN
                ubigeo AS colegio ON postulante.idubigeo_colegio = colegio.Ubigeo where postulante.idpostulante = ?', [$idpostulante]);

            // dd($postulantesdatos);
            // $datospostuss = collect($postulantesdatos);

            return view('postulantes.verDetallePostu', compact('tipo_cole', 'lenguasmater', 'discapacidad', 'idenEtnicas', 'moda_beca', 'estu_previos', 'est_civil', 'idpostulante', 'datospostuss', 'idusu'));
        }

        if ($usuario->hasRole('admision')) {
            $tipo_cole = DB::select('SELECT * FROM tipo_colegio ');
            $lenguasmater = DB::select('SELECT * FROM lenguas ');
            $discapacidad = DB::select('SELECT * FROM discapacidad');

            //identidad etnica
            $idenEtnicas = DB::table('identidad_etnica')->get();

            //MODALIDAD BECA
            $moda_beca = DB::table('modalidad_beca')->get();

            //ESTU PREVIOS
            $estu_previos = DB::table('estudios_previos')->get();

            //estado_civil
            $est_civil = DB::table('est_civil')->get();

            $datospostuss = DB::select('SELECT
            postulante.idpostulante,
            postulante.nacionalidad,
            postulante.tipodocumento,
            postulante.genero_postulante,
            postulante.edad_postulante,
            postulante.apellidos_pater_postulante,
            postulante.apellidos_mater_postulante,
            postulante.nombres_postulante,
            postulante.fecha_de_nacimiento_postu,
            postulante.celular,
            postulante.correo,
            postulante.lengua_mater,
            postulante.lengua_secun,
            postulante.direccion_domicilio,
            postulante.colegio,
            postulante.codigo_modular,
            postulante.direccion_colegio,
            postulante.año_de_termino_colegio,
            postulante.foto_postulante,
            nacimiento.Distrito AS distrito_nacimiento,
            nacimiento.Provincia AS provincia_nacimiento,
            nacimiento.Departamento AS departamento_nacimiento,
            domicilio.Distrito AS distrito_domicilio,
            domicilio.Provincia AS provincia_domicilio,
            domicilio.Departamento AS departamento_domicilio,
            colegio.Distrito AS distrito_colegio,
            colegio.Provincia AS provincia_colegio,
            colegio.Departamento AS departamento_colegio,

            postulante.idubigeo_colegio,
            postulante.idtipo_colegio,

            postulante.discapacidad,
            postulante.tipo_discapacidad,
            postulante.num_conadis,

            postulante.id_identidad_etnica,
            postulante.id_est_civil,
            postulante.con_hijos,
            postulante.cant_hijos,
            postulante.rebred,
            postulante.num_reso_rebred,
            postulante.con_beca,
            postulante.id_moda_beca,
            postulante.reso_beca,
            postulante.con_trabajo,
            postulante.tipo_trabajo,
            postulante.con_estudios,
            postulante.id_estu_previos,
            postulante.num_reso_metas,
            postulante.fecha_de_nacimiento_postu,

            postulante.con_proyecto,
            postulante.nom_proyecto

            FROM
                postulante
            LEFT JOIN
                ubigeo AS nacimiento ON postulante.idubigeo_nacimiento = nacimiento.Ubigeo
            LEFT JOIN
                ubigeo AS domicilio ON postulante.idubigeo_domicilio = domicilio.Ubigeo
            LEFT JOIN
                ubigeo AS colegio ON postulante.idubigeo_colegio = colegio.Ubigeo where postulante.idpostulante = ?', [$idpostulante]);

            // dd($postulantesdatos);
            // $datospostuss = collect($postulantesdatos);

            return view('postulantes.verDetallePostu', compact('tipo_cole', 'lenguasmater', 'discapacidad', 'idenEtnicas', 'moda_beca', 'estu_previos', 'est_civil', 'idpostulante', 'datospostuss', 'idusu'));
        }

        // Si el usuario tiene el rol 'alumno', solo puede ver su propio DNI
        if ($usuario->hasRole('alumno')) {
            if ($usuario->dni !== $idpostulante) {
                abort(403, 'No tienes permiso para acceder a este postulante.');
            }

            $tipo_cole = DB::select('SELECT * FROM tipo_colegio ');
            $lenguasmater = DB::select('SELECT * FROM lenguas ');
            $discapacidad = DB::select('SELECT * FROM discapacidad');

            //identidad etnica
            $idenEtnicas = DB::table('identidad_etnica')->get();

            //MODALIDAD BECA
            $moda_beca = DB::table('modalidad_beca')->get();

            //ESTU PREVIOS
            $estu_previos = DB::table('estudios_previos')->get();

            //estado_civil
            $est_civil = DB::table('est_civil')->get();

            $datospostuss = DB::select('SELECT
            postulante.idpostulante,
            postulante.nacionalidad,
            postulante.tipodocumento,
            postulante.genero_postulante,
            postulante.edad_postulante,
            postulante.apellidos_pater_postulante,
            postulante.apellidos_mater_postulante,
            postulante.nombres_postulante,
            postulante.fecha_de_nacimiento_postu,
            postulante.celular,
            postulante.correo,
            postulante.lengua_mater,
            postulante.lengua_secun,
            postulante.direccion_domicilio,
            postulante.colegio,
            postulante.codigo_modular,
            postulante.direccion_colegio,
            postulante.año_de_termino_colegio,
            postulante.foto_postulante,
            nacimiento.Distrito AS distrito_nacimiento,
            nacimiento.Provincia AS provincia_nacimiento,
            nacimiento.Departamento AS departamento_nacimiento,
            domicilio.Distrito AS distrito_domicilio,
            domicilio.Provincia AS provincia_domicilio,
            domicilio.Departamento AS departamento_domicilio,
            colegio.Distrito AS distrito_colegio,
            colegio.Provincia AS provincia_colegio,
            colegio.Departamento AS departamento_colegio,

            postulante.idubigeo_colegio,
            postulante.idtipo_colegio,

            postulante.discapacidad,
            postulante.tipo_discapacidad,
            postulante.num_conadis,

            postulante.id_identidad_etnica,
            postulante.id_est_civil,
            postulante.con_hijos,
            postulante.cant_hijos,
            postulante.rebred,
            postulante.num_reso_rebred,
            postulante.con_beca,
            postulante.id_moda_beca,
            postulante.reso_beca,
            postulante.con_trabajo,
            postulante.tipo_trabajo,
            postulante.con_estudios,
            postulante.id_estu_previos,
            postulante.num_reso_metas,
            postulante.fecha_de_nacimiento_postu,

            postulante.con_proyecto,
            postulante.nom_proyecto

            FROM
                postulante
            LEFT JOIN
                ubigeo AS nacimiento ON postulante.idubigeo_nacimiento = nacimiento.Ubigeo
            LEFT JOIN
                ubigeo AS domicilio ON postulante.idubigeo_domicilio = domicilio.Ubigeo
            LEFT JOIN
                ubigeo AS colegio ON postulante.idubigeo_colegio = colegio.Ubigeo where postulante.idpostulante = ?', [$idpostulante]);

            // dd($postulantesdatos);
            // $datospostuss = collect($postulantesdatos);

            return view('postulantes.verDetallePostu', compact('tipo_cole', 'lenguasmater', 'discapacidad', 'idenEtnicas', 'moda_beca', 'estu_previos', 'est_civil', 'idpostulante', 'datospostuss', 'idusu'));
        }

        abort(403, 'Acceso no autorizado.');
    }

    public function eliminarPostulante($id)
    {
        $verMalla = DB::table('postulante')
            ->where('idpostulante', '=', $id)
            ->where('id_malla', '!=', 0)
            ->exists();

        $ver_id_users = DB::table('users')
            ->select('id')
            ->where('dni', '=', $id)
            ->first();

        if (!$verMalla) {
            try {
                DB::beginTransaction();
                $delete_postu = DB::table('postulante')->where('idpostulante', $id)->delete();
                $delete_users = DB::table('users')->where('dni', $id)->delete();
                $delete_model_roles = DB::table('model_has_roles')->where('model_id', $ver_id_users->id)->delete();
                DB::commit();

                return response()->json(['icon' => 'success', 'title' => 'Eliminado.', 'mensaje' => 'Postulante eliminado.']);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json(['icon' => 'error', 'title' => 'Error.', 'mensaje' => 'Error al eliminar.']);
            }
        } else {
            return response()->json(['icon' => 'info', 'title' => 'Alerta.', 'mensaje' => 'Este usuario tiene una malla asignada, NO SE PUEDE ELIMINAR.']);
        }
    }

    public function update(Request $request)
    {
        // dd($request);
        $dniAnte = $request->dni;
        $dniActu = $request->dni_a;
        if ($dniAnte != $dniActu) {
            if (DB::table('users')->where('dni', $dniActu)->exists()) {
                return redirect()->back()->with('error', 'El N° de DNI ingresado ya existe a otro usuario');
            }
        }
        // Verificar si se cargó un archivo de imagen
        if ($request->hasFile('imagen')) {
            // dd('si s');
            $file = $request->file('imagen');
            $destinationPath = 'fotos_postulantes/';
            // Obtener la extensión original del archivo
            $extension = $file->getClientOriginalExtension();

            // Crear el nombre del archivo usando el DNI (y la extensión)
            $filename = $request->dni . '.' . $extension;
            // Obtener la imagen anterior del postulante
            $imagenAnterior = public_path($destinationPath . $filename);
            $imagenAnteriorConOtraExtencion = public_path($destinationPath . basename($request->foto));
            if (file_exists($imagenAnteriorConOtraExtencion) && is_file($imagenAnteriorConOtraExtencion)) {
                // dd('si s');
                unlink($imagenAnteriorConOtraExtencion);
            }

            if (file_exists($imagenAnterior) && is_file($imagenAnterior)) {

                unlink($imagenAnterior);
                // unlink($imagenAnteriorConOtraExtencion);
                $file->move($destinationPath, $filename);
                // Construir la ruta final
                $finalnomb = $destinationPath . $filename;
            } else {
                $file->move($destinationPath, $filename);
                // Construir la ruta final
                $finalnomb = $destinationPath . $filename;
            }
        } else {
            // dd('no s');
            if ($request->foto == 'fotos_postulantes/') {
                $finalnomb = null;
            } else {
                // Si no se ha cargado imagen, asigna null (o un valor por defecto si lo prefieres)
                $finalnomb = $request->foto;
            }
        }

        try {
            DB::beginTransaction();
            if ($dniAnte != $dniActu) {
                $dnif = $dniActu;
                $actuInscripcion = DB::table('inscripcion')->where('idpostulante', $dniAnte)->update([
                    'idpostulante' => $dniActu,
                ]);

                $actuPostulante = DB::table('postulante')->where('idpostulante', $dniAnte)->update([
                    'idpostulante' => $dniActu,
                ]);

                $actuUsers = DB::table('users')->where('dni', $dniAnte)->update([
                    'dni' => $dniActu,
                ]);

                $actuMatricula = DB::connection('mysql_segunda')->table('matricula')->where('id_alumno', $dniAnte)->update([
                    'id_alumno' => $dniActu,
                ]);
            } else {
                $dnif = $dniAnte;
                // dd('igu');
            }

            $updatepostu = DB::update('update postulante set
                tipodocumento = ?,
                nacionalidad = ?,

                genero_postulante = ?,
                edad_postulante = ?,
                apellidos_pater_postulante = ?,
                apellidos_mater_postulante = ?,
                nombres_postulante = ?,
                fecha_de_nacimiento_postu = ?,
                idubigeo_nacimiento = ?,
                lengua_mater = ?,
                lengua_secun = ?,
                direccion_domicilio = ?,
                idubigeo_domicilio  = ?,
                celular = ?,
                correo = ?,
                colegio = ?,
                codigo_modular = ?,
                direccion_colegio = ?,
                año_de_termino_colegio = ?,
                idubigeo_colegio = ?,
                idtipo_colegio = ?,
                foto_postulante = ?,

                discapacidad = ?,
                tipo_discapacidad = ?,
                num_conadis = ?,
                id_identidad_etnica = ?,
                id_est_civil = ?,
                rebred = ?,
                num_reso_rebred = ?,
                con_beca = ?,
                id_moda_beca = ?,
                reso_beca = ?,
                con_hijos = ?,
                cant_hijos = ?,
                con_trabajo = ?,
                tipo_trabajo = ?,
                con_estudios = ?,
                id_estu_previos = ?,
                num_reso_metas = ?,
                fecha_inscripcion = ?,

                con_proyecto = ?,
                nom_proyecto = ?

                where idpostulante = ?', [
                $request->tipodocumento,
                $request->nacionalidad,

                $request->genero_postu,
                $request->edad,
                $request->apellido_paterno,
                $request->apellido_materno,
                $request->nombres,
                $request->fecha_nacimiento,
                $request->distrito,
                $request->lengua_matern,
                $request->lengua_secundaria,
                $request->direccion,
                $request->distridomicilio,
                $request->celular,
                $request->correo_electronico,
                $request->nombrecolegio,
                $request->codimodu,
                $request->direcole,
                $request->anio_promocion,
                $request->districolegio,
                $request->tipo_colegio,
                $finalnomb,

                $request->discapa_postu,
                $request->tipo_discapacidad,
                $request->num_conadis,
                $request->identidadetnica,
                $request->est_civil,
                $request->rebred,
                $request->num_reso_rebred,
                $request->con_beca,
                $request->moda_beca,
                $request->num_reso_beca,
                $request->con_hijos,
                $request->cant_hijos,
                $request->con_trabajo,
                $request->tipo_trabajo,
                $request->con_estudios,
                $request->estu_previos,
                $request->num_reso_metas,
                now(),

                $request->con_proyectoe,
                $request->nom_proyectoe,

                $dnif
            ]);
            DB::commit();
            if ($dniAnte != $dniActu) {
                return redirect()->route('verDetalle.postulante', ['idpostulante' => $dnif])->with('success', 'Actualizado con éxito :D');
            }
            return redirect()->back()->with('success', 'Actualizado con éxito');
        } catch (\Throwable $th) {
            // dd($th->getMessage());
            DB::rollBack();
            // return redirect()->back()->with('error', $th->getMessage());
            return redirect()->back()->with('error', 'Debe ingresar los campos OBLIGATORIOS (*)');
        }
    }

    public function buscarpostu($id)
    {

        $datos = DB::select(' SELECT
        postulante.genero_postulante,
        postulante.idpostulante,
        postulante.edad_postulante,
        postulante.apellidos_pater_postulante,
        postulante.apellidos_mater_postulante,
        postulante.nombres_postulante,
        postulante.fecha_de_nacimiento_postu,
        postulante.celular,
        postulante.correo,
        postulante.lengua_mater,
        postulante.lengua_secun,
        postulante.direccion_domicilio,
        postulante.colegio,
        postulante.codigo_modular,
        postulante.direccion_colegio,
        postulante.año_de_termino_colegio,
        postulante.foto_postulante as foto_postulante,
        postulante.idtipo_colegio as idtipo_colegio,
        postulante.idubigeo_colegio as idubigeo_colegio,
        nacimiento.Distrito AS distrito_nacimiento,
        nacimiento.Provincia AS provincia_nacimiento,
        nacimiento.Departamento AS departamento_nacimiento,
        domicilio.Distrito AS distrito_domicilio,
        domicilio.Provincia AS provincia_domicilio,
        domicilio.Departamento AS departamento_domicilio,
        colegio.Distrito AS distrito_colegio,
        colegio.Provincia AS provincia_colegio,
        colegio.Departamento AS departamento_colegio
        FROM postulante
        LEFT JOIN ubigeo AS nacimiento ON postulante.idubigeo_nacimiento = nacimiento.Ubigeo
        LEFT JOIN ubigeo AS domicilio ON postulante.idubigeo_domicilio = domicilio.Ubigeo
        LEFT JOIN ubigeo AS colegio ON postulante.idubigeo_colegio = colegio.Ubigeo
        WHERE postulante.idpostulante = ?', [$id]);

        $postulante = reset($datos);

        if (!isset($postulante->foto_postulante) || empty($postulante->foto_postulante)) {
            $postulante->foto_postulante = asset('fotos_postulantes/default-user.jpg');
        } else {
            $postulante->foto_postulante = asset($postulante->foto_postulante);
        }
        return response()->json($postulante);
    }

    public function getlenguamaterna()
    {
        $lengua = DB::table('lenguas')
            ->select('*')
            ->get();
        return response()->json($lengua);
    }

    public function colegio($distrito, $tipo)
    {
        // dd($distrito);
        $distritoFormateado = str_pad($distrito, 6, '0', STR_PAD_LEFT);
        if ($tipo == 1) {
            $colegios = DB::table('colegio')
                ->select('*')
                ->where('Ubigeo', '=', $distritoFormateado)
                ->get();
            // dd($colegios);
            return response()->json($colegios);
        } else if ($tipo == 2) {
            $colegios = DB::table('colegio')
                ->select('*')
                ->where('Codigo_Modular', $distritoFormateado)
                ->get();
            return response()->json($colegios);
        }
    }
}
