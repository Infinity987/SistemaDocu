<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\pdf as PDF;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras as NumeroALetrasNumeroALetras;
use PHPUnit\Framework\Constraint\Count;

class pdfcontro extends Controller
{
    public function fichainscritos(request $resquest)
    {
        $fichainscripcion = DB::select('SELECT postulante.idpostulante as dni,
                                              postulante.edad_postulante,
                                              postulante.apellidos_pater_postulante,
                                              postulante.apellidos_mater_postulante,
                                              postulante.nombres_postulante,
                                              nacimiento.Distrito AS distrito_nacimiento,
                                              nacimiento.Provincia AS provincia_nacimiento,
                                              nacimiento.Departamento AS departamento_nacimiento,
                                              domicilio.Distrito AS distrito_domicilio,
                                              domicilio.Provincia AS provincia_domicilio,
                                              domicilio.Departamento AS departamento_domicilio,
                                              colegio.Distrito AS distrito_colegio,
                                              colegio.Provincia AS provincia_colegio,
                                              colegio.Departamento AS departamento_colegio,
                                              postulante.fecha_de_nacimiento_postu,
                                              postulante.celular,
                                              postulante.correo,
                                              lenguas.nombre,
                                              postulante.lengua_secun,
                                              postulante.direccion_domicilio,
                                              postulante.colegio,
                                              postulante.codigo_modular,
                                              postulante.direccion_colegio,
                                              postulante.año_de_termino_colegio,
                                              postulante.idtipo_colegio,
                                              postulante.foto_postulante,
                                              carreras.nombre_de_carrera FROM `inscripcion` INNER JOIN postulante ON inscripcion.idpostulante = postulante.idpostulante
                                               INNER JOIN vacantes ON inscripcion.idvacantes = vacantes.idvacantes
                                               INNER JOIN carreras ON vacantes.idcarreras = carreras.idcarreras
                                                INNER JOIN procesos ON procesos.idprocesos = vacantes.idprocesos
                                                INNER JOIN lenguas on postulante.lengua_mater = lenguas.id_lengua
                                                  LEFT JOIN
                                                                                            ubigeo AS nacimiento ON postulante.idubigeo_nacimiento = nacimiento.Ubigeo
                                                                                        LEFT JOIN
                                                                                            ubigeo AS domicilio ON postulante.idubigeo_domicilio = domicilio.Ubigeo
                                                                                        LEFT JOIN
                                                                                            ubigeo AS colegio ON postulante.idubigeo_colegio = colegio.Ubigeo where inscripcion.idinscripcion  = ?', [$resquest->idpostu]);

        view()->share('fichainscripcion', $fichainscripcion);
        $pdf = PDF::loadView('pdf.pdftotal', $fichainscripcion)->setPaper('a4');
        return $pdf->stream();
    }


public function fichainscritosconstancia(request $resquest)
    {
        $fichainscripcion = DB::select('SELECT
        carreras.idcarreras, 	
        postulante.discapacidad, postulante.tipo_discapacidad, inscripcion.Fecha_inscripcion,									  
											 postulante.genero_postulante,
                                             inscripcion.idinscripcion,
                                             procesos.nombre_proceso,
                                             modalidad.nombre_modalidad,
                                             
                                             	postulante.idpostulante as dni,
                                              postulante.edad_postulante,
                                              postulante.apellidos_pater_postulante,
                                              postulante.apellidos_mater_postulante,
                                              postulante.nombres_postulante,
                                              nacimiento.Distrito AS distrito_nacimiento,
                                              nacimiento.Provincia AS provincia_nacimiento,
                                              nacimiento.Departamento AS departamento_nacimiento,
                                              domicilio.Distrito AS distrito_domicilio,
                                              domicilio.Provincia AS provincia_domicilio,
                                              domicilio.Departamento AS departamento_domicilio,
                                              colegio.Distrito AS distrito_colegio,
                                              colegio.Provincia AS provincia_colegio,
                                              colegio.Departamento AS departamento_colegio,
                                              postulante.fecha_de_nacimiento_postu,
                                              postulante.celular,
                                              postulante.correo,
                                              lenguas.nombre,
                                              postulante.lengua_secun,
                                              postulante.direccion_domicilio,
                                              postulante.colegio,
                                              postulante.codigo_modular,
                                              postulante.direccion_colegio,
                                              postulante.año_de_termino_colegio,
                                              postulante.idtipo_colegio,
                                              postulante.foto_postulante,
                                              carreras.nombre_de_carrera FROM `inscripcion` INNER JOIN postulante ON inscripcion.idpostulante = postulante.idpostulante
                                               INNER JOIN vacantes ON inscripcion.idvacantes = vacantes.idvacantes
                                               INNER JOIN carreras ON vacantes.idcarreras = carreras.idcarreras
                                                INNER JOIN procesos ON procesos.idprocesos = vacantes.idprocesos
                                                INNER JOIN lenguas on postulante.lengua_mater = lenguas.id_lengua
                                                INNER JOIN modalidad ON vacantes.idmodalidad = modalidad.idmodalidad 
                                                 LEFT JOIN
                                                                                            ubigeo AS nacimiento ON postulante.idubigeo_nacimiento = nacimiento.Ubigeo
                                                                                        LEFT JOIN
                                                                                            ubigeo AS domicilio ON postulante.idubigeo_domicilio = domicilio.Ubigeo
                                                                                        LEFT JOIN
                                                                                            ubigeo AS colegio ON postulante.idubigeo_colegio = colegio.Ubigeo where inscripcion.idinscripcion  = ?', [$resquest->idpostu]);

        view()->share('fichainscripcion', $fichainscripcion);
        $pdf = PDF::loadView('pdf.pdfconstanciainscripcion', $fichainscripcion)->setPaper('a4');
        return $pdf->stream();
    }





    public function fichaprimeranota(request $resquest){

        $nombreproceso = DB :: select( 'SELECT DISTINCT(procesos.nombre_proceso) as nombreproceso,modalidad.nombre_modalidad FROM resultados INNER JOIN inscripcion ON resultados.idinscripcion = inscripcion.idinscripcion INNER JOIN postulante ON inscripcion.idpostulante = postulante.idpostulante INNER JOIN vacantes ON inscripcion.idvacantes = vacantes.idvacantes INNER JOIN procesos ON vacantes.idprocesos = procesos.idprocesos INNER JOIN modalidad ON vacantes.idmodalidad = modalidad.idmodalidad INNER JOIN carreras ON vacantes.idcarreras = carreras.idcarreras WHERE resultados.id_pdfprimeranota = ?',[$resquest->idprimeranota]);
        $datospostulantesprimera= DB::select('SELECT carreras.nombre_de_carrera, postulante.idpostulante, postulante.apellidos_pater_postulante, postulante.apellidos_mater_postulante, postulante.nombres_postulante, resultados.nota1, resultados.estado_apro_desa, resultados.asistencia FROM resultados INNER JOIN inscripcion ON resultados.idinscripcion = inscripcion.idinscripcion INNER JOIN postulante ON inscripcion.idpostulante = postulante.idpostulante INNER JOIN vacantes ON inscripcion.idvacantes = vacantes.idvacantes INNER JOIN carreras ON vacantes.idcarreras = carreras.idcarreras WHERE resultados.id_pdfprimeranota = ? ORDER BY carreras.idcarreras DESC, resultados.nota1 DESC',[$resquest->idprimeranota]);
           view()->share('datospostulantesprimera',$datospostulantesprimera);
           $pdf = PDF::loadView('pdf.pdfprimeranota', ['nombreproceso' => $nombreproceso,'datospostulantesprimera' => $datospostulantesprimera])->setPaper('a4', 'landscape');

             return $pdf->stream();


    }

    public function fichaingresantes(request $resquest){

            $procesonombre= DB::select('SELECT DISTINCT(procesos.nombre_proceso) as nombreproceso,modalidad.nombre_modalidad FROM `resultados` INNER JOIN inscripcion ON resultados.idinscripcion = inscripcion.idinscripcion INNER JOIN postulante ON inscripcion.idpostulante = postulante.idpostulante INNER JOIN vacantes ON inscripcion.idvacantes = vacantes.idvacantes INNER JOIN procesos ON vacantes.idprocesos = procesos.idprocesos INNER JOIN modalidad on vacantes.idmodalidad =modalidad.idmodalidad INNER JOIN carreras ON vacantes.idcarreras = carreras.idcarreras WHERE resultados.id_pdfingresantes= ?',[$resquest->idpdfingresa]);
            $ddatospostulantesingresantes= DB::select('SELECT carreras.nombre_de_carrera, postulante.idpostulante, postulante.apellidos_pater_postulante, postulante.apellidos_mater_postulante, postulante.nombres_postulante,resultados.nota_total,resultados.estado_ingreso,resultados.orden_de_merito FROM `resultados` INNER JOIN inscripcion ON resultados.idinscripcion = inscripcion.idinscripcion INNER JOIN postulante ON inscripcion.idpostulante = postulante.idpostulante INNER JOIN vacantes ON inscripcion.idvacantes = vacantes.idvacantes INNER JOIN carreras ON vacantes.idcarreras = carreras.idcarreras WHERE resultados.id_pdfingresantes= ? ORDER BY carreras.idcarreras DESC, resultados.nota_total DESC, resultados.orden_de_merito ASC;',[$resquest->idpdfingresa]);
            view()->share('ddatospostulantesingresantes',$ddatospostulantesingresantes);
            $pdf = PDF::loadView('pdf.pdfingresantes',['procesonombre' => $procesonombre,'ddatospostulantesingresantes' => $ddatospostulantesingresantes])->setPaper('a4', 'landscape');
            return $pdf->stream();


   }

   public function fichapadron(Request $request)
   {
       $proceso = DB::select('SELECT DISTINCT(modalidad.nombre_modalidad) as nombremodalidad FROM `padron`
           INNER JOIN inscripcion ON padron.id_padron = inscripcion.id_padron
           INNER JOIN postulante ON inscripcion.idpostulante = postulante.idpostulante
           INNER JOIN vacantes ON inscripcion.idvacantes = vacantes.idvacantes
           INNER JOIN carreras ON vacantes.idcarreras = carreras.idcarreras
           INNER JOIN modalidad ON vacantes.idmodalidad = modalidad.idmodalidad
           WHERE inscripcion.id_padron = ?', [$request->idpadron]);

       $datosparapadron = DB::select('SELECT postulante.foto_postulante, postulante.apellidos_pater_postulante, postulante.apellidos_mater_postulante, postulante.nombres_postulante, postulante.idpostulante, carreras.nombre_de_carrera, inscripcion.idaula FROM `padron`
           INNER JOIN inscripcion ON padron.id_padron = inscripcion.id_padron
           INNER JOIN postulante ON inscripcion.idpostulante = postulante.idpostulante
           INNER JOIN vacantes ON inscripcion.idvacantes = vacantes.idvacantes
           INNER JOIN carreras ON vacantes.idcarreras = carreras.idcarreras
           WHERE inscripcion.id_padron = ?
           ORDER BY inscripcion.idaula ASC', [$request->idpadron]);

       // Validar resultados
       if (empty($datosparapadron)) {
           return response()->json(['error' => 'No se encontraron datos para el padrón especificado.']);
       }

       if (empty($proceso)) {
           return response()->json(['error' => 'No se encontraron datos para el proceso.']);
       }

       // Generar el PDF
       $pdf = PDF::loadView('pdf.pdfpadron', ['datosparapadron' => $datosparapadron,'proceso' => $proceso])->setPaper('a4');

       return $pdf->stream();
   }


public function fichaconstancia(request $resquest){

     $datosconstanciaingre= DB::select('SELECT procesos.nombre_proceso, modalidad.nombre_modalidad, carreras.nombre_de_carrera, postulante.idpostulante, postulante.apellidos_pater_postulante, postulante.apellidos_mater_postulante, postulante.nombres_postulante,resultados.nota_total,resultados.estado_ingreso,resultados.orden_de_merito FROM `resultados` INNER JOIN inscripcion ON resultados.idinscripcion = inscripcion.idinscripcion INNER JOIN postulante ON inscripcion.idpostulante = postulante.idpostulante INNER JOIN vacantes ON inscripcion.idvacantes = vacantes.idvacantes INNER JOIN carreras ON vacantes.idcarreras = carreras.idcarreras INNER JOIN procesos ON vacantes.idprocesos = procesos.idprocesos INNER JOIN modalidad ON vacantes.idmodalidad = modalidad.idmodalidad WHERE resultados.idresultados = ?',[$resquest->idresultado]);
     view()->share('datosconstanciaingre', $datosconstanciaingre);
    $pdf = PDF::loadView('pdf.pdfconstancia', compact('datosconstanciaingre'))->setPaper('a4');
    return $pdf->stream();


}

}
