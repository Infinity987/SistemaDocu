<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\postulantescrontrolador;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\alumno\alumnoController;

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
Route::get('/index', [alumnoController::class, 'index'])->name('index');
Route::get('/matriActual', [alumnoController::class, 'matriActual'])->name('matriActual.index');
Route::get('/matriPorCurri', [alumnoController::class, 'matriPorCurri'])->name('matriPorCurri.index');
Route::get('/matriReali', [alumnoController::class, 'matriReali'])->name('matriReali.index');
Route::get('/horarioAlumno', [alumnoController::class, 'horarioAlumno'])->name('horarioAlumno.index');


