<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\postulantescrontrolador;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\docente\docenteController;
use App\Http\Controllers\docente\calificacionesController;
use App\Http\Controllers\docente\asistencias;
use App\Http\Controllers\docente\datosController;
use App\Http\Controllers\docente\crearDocu;
use App\Http\Controllers\docente\bandejaDocu;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/index', [docenteController::class, 'index'])->name('index');
Route::get('/horario', [docenteController::class, 'horario'])->name('horario');
route::get('/listarHorario',[docenteController::class, 'listarHorario'])->name('listarHorario');
route::post('/verHorario',[docenteController::class, 'verHorario'])->name('verHorario');

Route::get('/calificaciones', [calificacionesController::class, 'calificaciones'])->name('calificaciones');
Route::get('/verAlumnos', [calificacionesController::class, 'verAlumnos'])->name('verAlumnos');
Route::post('/asignarCompetencias', [calificacionesController::class, 'asignarCompetencias'])->name('asignarCompetencias');
Route::post('/guardarAlumnos', [calificacionesController::class, 'guardarAlumnos'])->name('guardarAlumnos');

route::get('/verAlumnos/asistencia',[asistencias::class, 'index'])->name('asistencia.index');
route::get('/verAlumnos/Totalasistencia',[asistencias::class, 'totalAsist'])->name('totalAsist');
Route::post('/verAlumnos/asistencia/guardarAsistencia', [asistencias::class, 'guardarAsistencia'])->name('guardarAsistencia');
Route::post('/verAlumnos/asistencia/eliminarAsis', [asistencias::class, 'eliminarAsis'])->name('eliminarAsis');
Route::post('/verAlumnos/asistencia/actuAsis', [asistencias::class, 'actuAsis'])->name('actuAsis');

Route::get('/datos', [datosController::class, 'datos'])->name('datos');


Route::get('/exportar-Asistencia-pdf', [asistencias::class, 'exportarPDF'])->name('exportarPDF');
route::get('/reporte/notas/{iddocente_curso}/{idturno}',[calificacionesController::class, 'pdfActaEvalu'])->middleware('auth')->name('reporteNotas');

//tramite documentario docente
route::get('/Docu-docente/creardocu',[crearDocu::class, 'creardocu'])->name('creardocu');
route::get('/Docu-docente/emitidos/{idtipo_docu}/{emisor}', [crearDocu::class, 'emitidos'])->name('doce.emitidos');
route::get('/Bandeja-docente/bandeja-docente', [bandejaDocu::class, 'index'])->name('bandejaDoce');
route::get('/Bandeja-docente/{idtipo_estado}/{emisor}', [bandejaDocu::class, 'bandejaList'])->name('bandejaList');
route::get('/recepcionar-docus-docente/{idtipo_estado}/{iddocument}/{movimient}', [bandejaDocu::class, 'bandejaRecepcionar'])->name('bandejaRecepcionar');







