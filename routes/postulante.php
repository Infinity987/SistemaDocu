<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\postulante\postulanteController;

route::get('/index', [postulanteController::class, 'index'])->name('index');
Route::post('/postulantes/agregarpostulante', [postulanteController::class, 'agregarpostulante'])->middleware('auth')->name('agregar'); //agregar un bien
