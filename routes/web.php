<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\RoleSelectionController;
use App\Http\Controllers\HomeController;
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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::redirect('/', 'login', 301);
Auth::routes();



Route::middleware(['auth'])->group(function () {
    Route::get('/seleccionar-rol', [RoleSelectionController::class, 'index'])->name('selector.roles');
    Route::post('/seleccionar-rol', [RoleSelectionController::class, 'store'])->name('set.active.role');
    Route::get('/home', [HomeController::class, 'index'])->name('home');
});

// Route::get('/home', function(){
//     $user = auth()->user();

//     if ($user->hasRole('admin')) {
//         return redirect('admin/users');
//     }

//     if ($user->hasRole('docente')) {
//         return redirect('/docente/index');
//     }

//     if ($user->hasRole('postulante')) {
//         return redirect('/postulante/index');
//     }
//     if ($user->hasRole('alumno')) {
//         return redirect('/alumno/index');
//     }
//     if ($user->hasRole('egresado')) {
//         return redirect('/egresado/index');
//     }

//     return redirect('/login');
// })->middleware('auth')->name('home');

