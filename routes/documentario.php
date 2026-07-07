<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController; //para los roles

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;


use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\documentario\mesaPartes;
use App\Http\Controllers\documentario\bandeja;
use App\Http\Controllers\documentario\searchDocu;
use App\Http\Controllers\documentario\reportes;
use App\Http\Controllers\documentario\enviardocumentos;
use App\Http\Controllers\documentario\pagos;


Route::resource('users', UserController::class)->names('admin.users');
route::post('/admin/registerUser', [RegisterController::class, 'register'])->name('admin.registerUser');

route::get('/Docu', [mesaPartes::class, 'index'])->name('mesapar.index');
route::get('/Docu/num_tipo_documento_expe/{idtipo_docu}/{emisor}', [mesaPartes::class, 'num_tipo_documento_expe'])->name('num_tipo_documento_expe'); //trae el ultimo id del ducu, para sumarlo
route::get('/Docu/num_tipo_documento_expe_m/{idtipo_docu}/{emisor}', [mesaPartes::class, 'num_tipo_documento_expe_m'])->name('num_tipo_documento_expe_m'); //trae el ultimo id del ducu, PARA MESA DE PARTES
route::get('/buscarUsuario', [mesaPartes::class, 'buscarUsuario'])->name('buscarUsuario');
Route::post('/docu/generar-word', [mesaPartes::class, 'generarWordBorrador'])->name('mesaPartes.word');
route::post('/Docu/registrarDocu', [mesaPartes::class, 'registrarDocu'])->name('registrarDocu');
route::post('/Docu/registrarDocu_m', [mesaPartes::class, 'registrarDocu_m'])->name('registrarDocu_m');
route::get('/Docu/emitidos/{idtipo_docu}/{emisor}', [mesaPartes::class, 'emitidos'])->name('mesapar.emitidos');
route::get('/Docu/emitidos_m/{idtipo_docu}/{emisor}', [mesaPartes::class, 'emitidos_m'])->name('mesapar.emitidos_m');
route::get('/Docu/showEmitido/{id}', [mesaPartes::class, 'showEmitido'])->name('mesapar.showEmitido');
route::post('/Docu/updateDocuEmi', [mesaPartes::class, 'updateDocuEmi'])->name('mesapar.updateDocuEmi');
route::get('/traerDepen', [mesaPartes::class, 'traerDepen'])->name('traerDepen');
route::get('/buscarDocentes', [mesaPartes::class, 'buscarDocentes'])->name('buscarDocentes');
Route::get('/buscar-entidad', [mesaPartes::class, 'buscarEntidad'])->middleware('auth')->name('buscarEntidad');
route::post('/enviardocumentos/responder', [enviardocumentos::class, 'responder'])->name('enviardocumentos.responder');
route::post('/enviardocumentos/responderoficinas', [enviardocumentos::class, 'responderoficinas'])->name('enviardocumentos.responderoficinas');
route::get('/enviardocumentos/solucionar/{id}', [enviardocumentos::class, 'solucionar'])->name('enviardocumentos.solucionar');
Route::post('/enviardocumentos/derivar-director/{id}', [enviardocumentos::class, 'derivarDirector'])->name('enviardocumentos.derivarDirector');

route::get('/enviardocumentos/responder/{iddocument}', [enviardocumentos::class, 'responderDocumento'])->middleware('auth')->name('enviardocumentos.responderDocumento');
//pdf de director
Route::post('/enviardocumentos/generar-pdf', [enviardocumentos::class, 'generarPDF'])->name('emitirdirector.generarPDF');

route::get('/bandeja', [bandeja::class, 'index'])->name('mesapar.bandeja');
route::get('/bandeja/{idtipo_estado}/{emisor}', [bandeja::class, 'bandejaEstado'])->name('bandeja.bandejaEstado');
route::get('/bandeja/recibir/{idtipo_estado}/{iddocument}/{iddependencias_receptor}', [bandeja::class, 'bandejaEstado_upda'])->name('bandeja.recibir');

route::get('/searchDocu', [searchDocu::class, 'index'])->name('searchDocu.index');
route::get('/searchDocu/search', [searchDocu::class, 'search'])->name('searchDocu.numex');

route::get('/reporDepen', [reportes::class, 'index'])->name('reporDepen.index');
route::post('/reporDepen/chartLine/tasa', [reportes::class, 'mostrarTasaSla'])->name('chart.line.tasa');

Route::get('/preview-pdf/{filename}', function ($filename) {
    // Buscamos en el disco temporal de Livewire
    $path = 'livewire-tmp/' . $filename;

    if (!Storage::disk('local')->exists($path)) {
        abort(404);
    }

    $file = Storage::disk('local')->get($path);

    return response($file, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="'.$filename.'"'
    ]);
})->name('pdf.preview')->middleware('auth');
// pagos ver
route::get('/pagos', [pagos::class, 'index'])->name('pagos.index');


