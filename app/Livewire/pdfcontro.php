<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\pdf as PDF;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras as NumeroALetrasNumeroALetras;
use PHPUnit\Framework\Constraint\Count;

class pdfcontro extends Controller
{
    public function fichainscritos(request $resquest){       
        $fichainscripcion= DB::select('SELECT postulante.idpostulante as dni,
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
                                              postulante.lengua_mater,
                                              postulante.lengua_secun,
                                              postulante.direccion_domicilio,
                                              postulante.colegio,
                                              postulante.codigo_modular,
                                              postulante.direccion_colegio,
                                              postulante.año_de_termino_colegio,
                                              postulante.idtipo_colegio,
                                              postulante.foto_postulante,
                                              carreras.nombre_de_carrera FROM `inscripcion` INNER JOIN postulante ON inscripcion.idpostulante = postulante.idpostulante INNER JOIN vacantes ON inscripcion.idvacantes = vacantes.idvacantes INNER JOIN carreras ON vacantes.idcarreras = carreras.idcarreras INNER JOIN procesos ON procesos.idprocesos = vacantes.idprocesos  LEFT JOIN 
                                                                                            ubigeo AS nacimiento ON postulante.idubigeo_nacimiento = nacimiento.Ubigeo
                                                                                        LEFT JOIN 
                                                                                            ubigeo AS domicilio ON postulante.idubigeo_domicilio = domicilio.Ubigeo
                                                                                        LEFT JOIN 
                                                                                            ubigeo AS colegio ON postulante.idubigeo_colegio = colegio.Ubigeo where inscripcion.idinscripcion  = ?',[$resquest->idpostu]);
                                                                                            
        view()->share('fichainscripcion',$fichainscripcion);                               
        $pdf = PDF::loadView('pdf.pdftotal',$fichainscripcion)->setPaper('a4');   
       return $pdf->stream();      
        
    }


    public function fichaprimeranota(request $resquest){       
                 
         $datospostulantesprimera= DB::select('SELECT carreras.nombre_de_carrera, postulante.idpostulante, postulante.apellidos_pater_postulante, postulante.apellidos_mater_postulante, postulante.nombres_postulante,resultados.nota1,resultados.estado_apro_desa FROM `resultados` 
         INNER JOIN inscripcion ON resultados.idinscripcion = inscripcion.idinscripcion
          INNER JOIN postulante ON inscripcion.idpostulante = postulante.idpostulante 
          INNER JOIN vacantes ON inscripcion.idvacantes = vacantes.idvacantes 
          INNER JOIN carreras ON vacantes.idcarreras = carreras.idcarreras 
          WHERE resultados.id_pdfprimeranota = ?',[$resquest->idprimeranota]);
           view()->share('datospostulantesprimera',$datospostulantesprimera); 
           $pdf = PDF::loadView('pdf.pdfprimeranota',$datospostulantesprimera)->setPaper('a4', 'landscape');   
             return $pdf->stream();  
         
        
    }

    public function fichaingresantes(request $resquest){       
                 
        $datospostulantesprimera= DB::select('SELECT carreras.nombre_de_carrera, postulante.idpostulante, postulante.apellidos_pater_postulante, postulante.apellidos_mater_postulante, postulante.nombres_postulante,resultados.nota1,resultados.estado_apro_desa FROM `resultados` 
        INNER JOIN inscripcion ON resultados.idinscripcion = inscripcion.idinscripcion
         INNER JOIN postulante ON inscripcion.idpostulante = postulante.idpostulante 
         INNER JOIN vacantes ON inscripcion.idvacantes = vacantes.idvacantes 
         INNER JOIN carreras ON vacantes.idcarreras = carreras.idcarreras 
         WHERE resultados.id_pdfprimeranota = ?',[$resquest->idprimeranota]);
          view()->share('datospostulantesprimera',$datospostulantesprimera); 
          $pdf = PDF::loadView('pdf.pdfprimeranota',$datospostulantesprimera)->setPaper('a4', 'landscape');   
            return $pdf->stream();  
        
       
   }
}
