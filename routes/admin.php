<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\UserController; //para los roles
use App\Http\Controllers\postulante\postulanteController;

use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\procesoscontro;
use App\Http\Controllers\inscripcioncontro;
use App\Http\Controllers\prueba;
use Livewire\Component;
use Livewire\Livewire;
use App\Http\Controllers\pdfcontro;
use App\Http\Controllers\resultado;
use App\Http\Controllers\resultadoprimera;
use App\Http\Controllers\resultadosingresantes;
use App\Http\Controllers\padron;
use App\Http\Controllers\constancia;
use App\Http\Controllers\UbigeoController;
use App\Http\Controllers\reportes;
use App\Http\Controllers\malla_curri;
use App\Http\Controllers\matricula_proceso;
use App\Http\Controllers\Admin\asignarCurso;
use App\Http\Controllers\Admin\HorarioController;
use App\Http\Controllers\Admin\semesAcademicoController;
use App\Http\Controllers\Admin\reporteActaEvaController;
use App\Http\Controllers\encargadosController;


Route::resource('users', UserController::class)->names('admin.users');
route::get('/admin/encargados', [encargadosController::class, 'index'])->name('encargados.index');
Route::post('/admin/encargados/guardarEncargado',[encargadosController::class, 'guardarEncargado'])->middleware('auth')->name('guardar.encargado'); //agregar un bien
Route::get('/admin/encargados/ajaxEncargados',[encargadosController::class, 'ajaxEncargados'])->middleware('auth')->name('ajaxEncargados'); //agregar un bien
Route::post('/admin/encargados/ajaxactualizarEstado/{estado}',[encargadosController::class, 'ajaxactualizarEstado'])->middleware('auth')->name('ajaxactualizarEstado'); //agregar un bien
Route::get('/admin/encargados/gedDataEncargado/{id}',[encargadosController::class, 'gedDataEncargado'])->middleware('auth')->name('gedDataEncargado'); //agregar un bien
Route::post('/admin/encargados/editarEncargado',[encargadosController::class, 'editarEncargado'])->middleware('auth')->name('editarEncargado'); //agregar un bien
Route::post('/admin/encargados/eliminarEncargados/{id}',[encargadosController::class, 'eliminarEncargados'])->middleware('auth')->name('eliminarEncargados'); //agregar un bien

route::get('/postulantes/ajaxPostulantes', [postulanteController::class, 'ajaxPostulantes'])->middleware('auth')->name('ajaxPostulantes');
route::get('/postulantes/verpostulantes', [postulanteController::class, 'verpostulantes'])->middleware('auth')->name('admin.verpostulantes');
Route::put('/postulantes/update', [postulanteController::class, 'update'])->middleware('auth')->name('postulante.update'); //agregar un bien
route::get('/postulantes/verDetalle/{idpostulante}', [postulanteController::class, 'verDetalle'])->middleware('auth')->name('verDetalle.postulante'); //para traer toda la data de postulante luego editar
Route::post('/postulantes/eliminarPostulante/{idpostulante}',[postulanteController::class, 'eliminarPostulante'])->middleware('auth')->name('eliminarPostulante'); //agregar un bien


// Controlador procesos
route::get('/procesos',[procesoscontro::class, 'index'])->middleware('auth')->name('procesos.index'); //muestra el ingreso de postulantes
Route::post('/procesos/agregarprocesos',[procesoscontro::class, 'agregarprocesos'])->middleware('auth')->name('procesos.agregar'); //agregar un bien
Route::post('/procesos/editarprocesos',[procesoscontro::class, 'editarprocesos'])->middleware('auth')->name('procesos.editar'); //agregar un bien
Route::post('/procesos/cambiar-estado', [procesoscontro::class, 'cambiarEstado'])->name('procesos.cambiarEstado');
Route::delete('/procesos/eliminar/{id}', [procesoscontro::class, 'eliminarProceso'])->name('procesos.eliminar');
//contolador vacantes
route::get('/vacantes',[procesoscontro::class, 'indexvacantes'])->middleware('auth')->name('procesos.indexvacantes'); //muestra el ingreso de postulantes
route::post('/vacantes/agregarvacantes',[procesoscontro::class, 'agregarvacantes'])->middleware('auth')->name('procesos.agregarvacantes'); //muestra el ingreso de postulantes
Route::post('/vacantes/editarvacantes',[procesoscontro::class, 'editarvacantes'])->middleware('auth')->name('procesos.editarvacantes'); //agregar un bien
// controlador inscripcion
route::get('/inscripcion',[inscripcioncontro::class, 'index'])->middleware('auth')->name('inscripcion.index'); //muestra el ingreso de postulantes
Route::post('/inscripcion/agregarinscripcion',[inscripcioncontro::class, 'agregarinscripcion'])->middleware('auth')->name('inscripcion.agregarinscripcion'); //agregar un bien
Route::post('/inscripcion/cambiar', [inscripcioncontro::class, 'cambiar'])->middleware('auth')->name('inscripcion.cambiar');
Route::post('/inscripcion/eliminarInscrip', [inscripcioncontro::class, 'eliminarInscrip'])->middleware('auth')->name('inscripcion.eliminar'); //eliminar inscripcion
route::get('/buscarPostul',[inscripcioncontro::class, 'buscarPostul'])->middleware('auth')->name('inscripcion.buscarPostul');
Route::get('/exportar-inscritos/{idProceso}/{idModalidad}', [inscripcioncontro::class, 'exportarExcel'])->name('inscripcion.exportar');

Route::get('/ubigeo/departamentos', [UbigeoController::class, 'getDepartamentos'])->name('departamento');
Route::get('/ubigeo/provincias/{departamento}', [UbigeoController::class, 'getProvincias'])->name('provincia');
Route::get('/ubigeo/distritos/{provincia}', [UbigeoController::class, 'getDistritos'])->name('distrito');
Route::get('/colegio/{distrito}/{tipo}', [postulanteController::class, 'colegio'])->name('colegio');
Route::get('/postulantes/edit/{id}', [postulanteController::class, 'buscarpostu'])->name('edit.postulante');
Route::get('/getlenguamaterna', [postulanteController::class, 'getlenguamaterna'])->name('edit.getlenguamaterna');



// controlador resultado
route::get('/resultado',[resultado::class, 'index'])->middleware('auth')->name('resultado.index'); //muestra vista de resultados
route::get('/resultado/ingre',[resultado::class, 'indexingre'])->middleware('auth')->name('ingre.index'); //muestra vista de resultados

// controlador resultados primera nota
route::get('/resultadoprimera',[resultadoprimera::class, 'index'])->middleware('auth')->name('resultadoprimera.index'); //muestra vista de resultados
route::post('/resultadoprimera/eliminarnota1',[resultadoprimera::class, 'eliminarnota1'])->middleware('auth')->name('resultadoprimera.eliminar'); //muestra vista de resultados
Route::post('/resultadoprimera/excel', [resultadoprimera::class, 'exportExcel'])->name('excel.resultadoprimera');

// controlador resultados ingresantes
route::get('/resultadoingresantes',[resultadosingresantes::class, 'index'])->middleware('auth')->name('resultadoingresantes.index'); //muestra vista de resultados
route::post('/resultadoingresantes/eliminaringresantes',[resultadosingresantes::class, 'eliminaringresantes'])->middleware('auth')->name('resultadoingresantes.eliminaringresantes'); //muestra vista de resultados
Route::post('/resultadoingresantes/excel', [resultadosingresantes::class, 'fichaingresantesExcel'])->name('resultadoingresantes.excel');
// controlador prueba
route::get('/prueba',[prueba::class, 'index'])->middleware('auth')->name('prueba.index'); //muestra el ingreso de postulantes
//controlador padron
route::get('/padron',[padron::class, 'index'])->middleware('auth')->name('padron.index'); //muestra el ingreso de postulantes
route::post('/padron/generaraulas',[padron::class, 'generaraulas'])->middleware('auth')->name('padron.generaraulas'); //muestra vista de resultados
Route::delete('/padron/eliminar', [Padron::class, 'eliminar'])->name('padron.eliminar');
//controlador constancia
route::get('/constancia',[constancia::class, 'index'])->middleware('auth')->name('constancia.index'); //muestra el ingreso de postulantes

//controlador reportes
route::get('/reportes',[reportes::class, 'index'])->middleware('auth')->name('Reportes.index'); //muestra el ingreso de postulantes


//documetnospdf
Route::post('/pdf/fichainscritos',[pdfcontro::class, 'fichainscritos'])->name('pdf.fichainscritos'); //muestra pdf
//constancia de inscripcion
Route::post('/pdf/fichainscritosconstancia',[pdfcontro::class, 'fichainscritosconstancia'])->name('pdf.fichainscritosconstancia'); //muestra pdf
//ppdf de primeranota
Route::post('/pdf/fichaprimeranota',[pdfcontro::class, 'fichaprimeranota'])->name('pdf.fichaprimeranota'); //muestra pdf
//ppdf de primeranota
Route::post('/pdf/fichaingresantes',[pdfcontro::class, 'fichaingresantes'])->name('pdf.fichaingresantes'); //muestra pdf
//ppdf de padron
Route::post('/pdf/fichapadron',[pdfcontro::class, 'fichapadron'])->name('pdf.fichapadron'); //muestra pdf

//ppdf de constancia
Route::post('/pdf/fichaconstancia',[pdfcontro::class, 'fichaconstancia'])->name('pdf.fichaconstancia'); //muestra pdf


//Malla curricular
route::get('/malla',[malla_curri::class, 'index'])->middleware('auth')->name('malla.index'); //muestra el ingreso de postulantes
route::post('/csvmalla/malla',[malla_curri::class, 'archivocsv'])->middleware('auth')->name('malla.archivocsv'); //muestra el ingreso de postulantes
route::post('/cursosmodi/malla',[malla_curri::class, 'cursosmodi'])->middleware('auth')->name('malla.cursosmodi'); //muestra el ingreso de postulantes
Route::post('/eliminar/malla', [malla_curri::class, 'eliminar'])->middleware('auth')->name('malla.eliminar');
Route::post('/subircompe/malla', [malla_curri::class, 'subircompete'])->middleware('auth')->name('malla.subircompe');

//asignar curso docente
route::get('/asignarCurso',[asignarCurso::class, 'index'])->middleware('auth')->name('asignarCurso.index'); //muestra el ingreso de postulantes
// matricula_proceso
route::get('/matricula_proceso',[matricula_proceso::class, 'index'])->middleware('auth')->name('matricula_proceso.index'); //muestra el ingreso de postulantes
// route::post('/matricula_proceso/pdfsemestre',[matricula_proceso::class, 'semestrepdf'])->middleware('auth')->name('matricula_proceso.semestrepdf'); //muestra el ingreso de postulantes
route::post('/matricula_proceso/pdfsemestrenotas',[matricula_proceso::class, 'semestrenotaspdf'])->middleware('auth')->name('matricula_proceso.semestrenotaspdf'); //muestra el ingreso de postulantes
Route::post('/matricula_proceso/excelsemestrenotas', [matricula_proceso::class, 'semestrenotasexcel'])->middleware('auth')->name('matricula_proceso.semestrenotasexcel');

route::post('/matricula_proceso/pdfsemestre',[matricula_proceso::class, 'semestrepdf'])->middleware('auth')->name('matricula_proceso.semestrepdf'); //muestra el ingreso de postulantes
Route::post('/matricula_proceso/pdfconstancia/{idSemestre}', [matricula_proceso::class, 'generarPdfPorSemestre'])->middleware('auth')->name('ruta.pdf');
Route::post('/matricula_proceso/pdfconstanciasubsanacion/{idSemestre}', [matricula_proceso::class, 'generarPdfPorSemestresubsanacion'])->middleware('auth')->name('ruta.subsanacionpdf');
Route::post('/matricula_proceso/pdfconstanciasubsanaciondocente/memorando', [matricula_proceso::class, 'generarPdfMemorando'])->middleware('auth')->name('pdf.subsanacionmemorando');
route::get('/asignarCurso',[asignarCurso::class, 'index'])->middleware('auth')->name('asignarCurso.index');
route::get('/buscarDocente', [asignarCurso::class, 'buscarDocente'])->middleware('auth')->name('buscarDocente');
route::get('/historial_alumno',[matricula_proceso::class, 'historialalumnoindex'])->middleware('auth')->name('historialalumno.index'); //muestra el ingreso de postulantes
route::post('/historial_alumno/agregar',[matricula_proceso::class, 'agregaralumnohistorial'])->middleware('auth')->name('historialalumno.agregar'); //muestra el ingreso de postulantes

Route::get('/historial_alumno/agregarnotas', [matricula_proceso::class, 'generarPlantillaNotas'])->name('plantilla.notas');
Route::post('/historial_alumno/subirnotas', [matricula_proceso::class, 'procesarExcel'])->name('notas.carga');
route::get('/horario',[HorarioController::class, 'index'])->middleware('auth')->name('horario.index');
route::post('/horario/agreindex',[HorarioController::class, 'agreindex'])->middleware('auth')->name('agreindex.index');
route::post('/horario/agreindex/deleteHorario',[HorarioController::class, 'deleteHorario'])->middleware('auth')->name('deleteHorario.elimi');

route::post('/horario/agreindex/guardarHorario',[HorarioController::class, 'guardarHorario'])->middleware('auth')->name('guardarHorario');
Route::post('/horario/agreindex/horario/eliminarPor-id', [HorarioController::class, 'eliminarPorId'])->name('horario.eliminarPorId');
route::get('/horario/agreindex/listar',[HorarioController::class, 'listar'])->middleware('auth')->name('horario.listar');

//semestre academico
route::get('/semestre',[semesAcademicoController::class, 'index'])->middleware('auth')->name('semestre.index');
Route::post('/semestre/savesemestreac', [semesAcademicoController::class, 'savesemestreac'])->middleware('auth')->name('savesemestreac');
Route::get('/semestre/listsemes', [semesAcademicoController::class, 'listsemes'])->middleware('auth')->name('listsemes');
Route::post('/semestre/actuEstado/{id}', [semesAcademicoController::class, 'actuEstado'])->middleware('auth')->name('actuEstado');
Route::delete('/semestre/eliminarSemes/{id}', [semesAcademicoController::class, 'eliminarSemes'])->middleware('auth')->name('eliminarSemes');
Route::get('/semestre/verEditarSemes/{id}', [semesAcademicoController::class, 'verEditarSemes'])->middleware('auth')->name('verEditarSemes');
Route::post('/semestre/actualizarSemes', [semesAcademicoController::class, 'actualizarSemes'])->middleware('auth')->name('actualizarSemes');
Route::post('/semestre/actuEstadomatricu/{id}', [semesAcademicoController::class, 'actuEstadomatricu'])->middleware('auth')->name('actuEstadomatricu');

//reporte acta de evaluacion
route::get('/reporte/acta/evaluacion',[reporteActaEvaController::class, 'index'])->middleware('auth')->name('actaEvalu.index');
route::get('/reporte/acta/evaluacion/pdf/{iddocente_curso}/{tipo}',[reporteActaEvaController::class, 'pdfActaEvalu'])->middleware('auth')->name('pdf.pdfActaEvalu');
route::get('/reporte/calificaciones/cursos',[reporteActaEvaController::class, 'califiCursoindex'])->middleware('auth')->name('califiCurso.index');
route::get('/reporte/calificaciones/cursos/{id_alumno}/{idmatricula}',[reporteActaEvaController::class, 'pdfcalifiCurso'])->middleware('auth')->name('pdf.pdfcalifiCurso');
route::get('/reporte/nota/notas_general',[reporteActaEvaController::class, 'indexnotageneral'])->middleware('auth')->name('notageneral.index');




