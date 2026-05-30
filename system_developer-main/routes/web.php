<?php

use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\ClienteConsultaDocumentoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisitaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas web del sistema
|--------------------------------------------------------------------------
|
| Aquí se registran las rutas principales del proyecto. El sistema redirige
| la raíz hacia el módulo de clientes y protege las operaciones internas con
| autenticación para evitar accesos no autorizados.
|
*/

// Pantalla inicial: envía directamente al listado de clientes.
Route::redirect('/', '/clientes');

Route::middleware('auth')->group(function () {
    // Vista vacía usada por Turbo para limpiar/cerrar modales dinámicos.
    Route::get('/_turbo/frame-modal-empty', fn () => view('turbo.modal_empty'))->name('turbo.modal-empty');

    // Rutas de visitas asociadas a un cliente específico.
    Route::get('/clientes/{cliente}/visitas/create', [VisitaController::class, 'create'])->name('clientes.visitas.create');
    Route::post('/clientes/{cliente}/visitas', [VisitaController::class, 'store'])->name('clientes.visitas.store');
    Route::get('/clientes/{cliente}/visitas', [VisitaController::class, 'index'])->name('clientes.visitas.index');
    Route::get('/clientes/{cliente}/visitas/{visita}/observaciones', [VisitaController::class, 'editObservaciones'])->name('clientes.visitas.observaciones.edit');
    Route::patch('/clientes/{cliente}/visitas/{visita}/observaciones', [VisitaController::class, 'updateObservaciones'])->name('clientes.visitas.observaciones.update');
    Route::get('/clientes/{cliente}/visitas/{visita}', [VisitaController::class, 'showModal'])->name('clientes.visitas.show');
    Route::patch('/clientes/{cliente}/visitas/{visita}/estado', [VisitaController::class, 'updateEstado'])->name('clientes.visitas.estado.update');

    // Calendario mensual de visitas registradas.
    Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario.index');

    // Rutas estándar para editar/eliminar el perfil del usuario autenticado.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Pantalla de confirmación personalizada antes de eliminar un cliente.
    Route::get('/clientes/{cliente}/delete', [ClienteController::class, 'delete'])->name('clientes.delete');

    // Consulta externa de DNI/RUC mediante ApiPeru para autocompletar clientes.
    Route::post('/clientes/consulta-documento', ClienteConsultaDocumentoController::class)
        ->name('clientes.consulta-documento');

    // CRUD principal de clientes. Se excluye show porque el flujo usa modales/listados.
    Route::resource('clientes', ClienteController::class)->except(['show']);
});

// Rutas de autenticación generadas por Laravel Breeze.
require __DIR__.'/auth.php';
