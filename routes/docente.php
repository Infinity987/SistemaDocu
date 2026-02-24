<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\postulantescrontrolador;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\docente\docenteController;
use App\Http\Controllers\docente\calificacionesController;
use App\Http\Controllers\docente\asistencias;
use App\Http\Controllers\docente\datosController;

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





