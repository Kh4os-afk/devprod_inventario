<?php

use Illuminate\Support\Facades\Route;

Route::get('/',\App\Livewire\Index::class);
Route::get('/inventario',\App\Livewire\Inventario::class);