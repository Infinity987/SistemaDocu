<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\postulantescrontrolador;
use Illuminate\Support\Facades\Auth;

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

Route::get('/home', function(){
    $user = auth()->user();

    if ($user->hasRole('admin')) {
        return redirect('admin/users');
    }

    if ($user->hasRole('docente')) {
        return redirect('/docente/index');
    }

    if ($user->hasRole('postulante')) {
        return redirect('/postulante/index');
    }
    if ($user->hasRole('alumno')) {
        return redirect('/alumno/index');
    }
    if ($user->hasRole('egresado')) {
        return redirect('/egresado/index');
    }

    return redirect('/login');
})->middleware('auth')->name('home');

