<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {
    Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', \App\Livewire\Index::class);
    Route::get('/inventario', \App\Livewire\Inventario::class)->lazy();

    Route::get('/logout', [\App\Livewire\Auth\Login::class, 'logout'])->name('logout');
});
