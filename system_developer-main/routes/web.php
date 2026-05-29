<?php

use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\ClienteConsultaDocumentoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisitaController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/clientes');

Route::middleware('auth')->group(function () {
    Route::get('/_turbo/frame-modal-empty', fn () => view('turbo.modal_empty'))->name('turbo.modal-empty');

    Route::get('/clientes/{cliente}/visitas/create', [VisitaController::class, 'create'])->name('clientes.visitas.create');
    Route::post('/clientes/{cliente}/visitas', [VisitaController::class, 'store'])->name('clientes.visitas.store');
    Route::get('/clientes/{cliente}/visitas', [VisitaController::class, 'index'])->name('clientes.visitas.index');
    Route::get('/clientes/{cliente}/visitas/{visita}/observaciones', [VisitaController::class, 'editObservaciones'])->name('clientes.visitas.observaciones.edit');
    Route::patch('/clientes/{cliente}/visitas/{visita}/observaciones', [VisitaController::class, 'updateObservaciones'])->name('clientes.visitas.observaciones.update');
    Route::get('/clientes/{cliente}/visitas/{visita}', [VisitaController::class, 'showModal'])->name('clientes.visitas.show');
    Route::patch('/clientes/{cliente}/visitas/{visita}/estado', [VisitaController::class, 'updateEstado'])->name('clientes.visitas.estado.update');

    Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/clientes/{cliente}/delete', [ClienteController::class, 'delete'])->name('clientes.delete');

    Route::post('/clientes/consulta-documento', ClienteConsultaDocumentoController::class)
        ->name('clientes.consulta-documento');

    Route::resource('clientes', ClienteController::class)->except(['show']);
});

require __DIR__.'/auth.php';
