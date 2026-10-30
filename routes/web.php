<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\HalloweenController;

if (request()->header('x-forwarded-proto') === 'https' || str_contains(request()->getHost(), 'ngrok')) {
    URL::forceScheme('https');
}

Route::get('/', [HalloweenController::class, 'index'])->name('halloween.index');
Route::get('/votar', [HalloweenController::class, 'votar'])->name('halloween.votar');
Route::post('/registrar-disfraz', [HalloweenController::class, 'registrarConcursante'])->name('halloween.registrar');
Route::post('/votar/{id}', [HalloweenController::class, 'emitirVoto'])->name('halloween.emitirVoto');

// Ruta Privada de Administrador
Route::get('/panel-secreto-admin', [HalloweenController::class, 'adminPanel'])->name('halloween.admin');
Route::post('/panel-secreto-admin/login', [HalloweenController::class, 'adminLogin'])->name('halloween.admin.login');
Route::post('/panel-secreto-admin/logout', [HalloweenController::class, 'adminLogout'])->name('halloween.admin.logout');
Route::post('/panel-secreto-admin/toggle-votaciones', [HalloweenController::class, 'toggleVotaciones'])->name('halloween.admin.toggleVotaciones');
Route::post('/panel-secreto-admin/toggle-registro', [HalloweenController::class, 'toggleRegistro'])->name('halloween.admin.toggleRegistro');
Route::post('/panel-secreto-admin/finalizar', [HalloweenController::class, 'finalizarResultados'])->name('halloween.admin.finalizar');
Route::post('/panel-secreto-admin/reabrir', [HalloweenController::class, 'reabrirVotaciones'])->name('halloween.admin.reabrir');
Route::post('/panel-secreto-admin/actualizar/{id}', [HalloweenController::class, 'actualizarConcursante'])->name('halloween.admin.actualizar');
Route::post('/panel-secreto-admin/eliminar/{id}', [HalloweenController::class, 'eliminarConcursante'])->name('halloween.admin.eliminar');
Route::post('/panel-secreto-admin/reiniciar', [HalloweenController::class, 'reiniciarVotos'])->name('halloween.admin.reiniciar');
