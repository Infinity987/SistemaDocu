<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\postulantescrontrolador;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\egresado\egresadoController;

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
Route::get('/index', [egresadoController::class, 'index'])->name('index');
