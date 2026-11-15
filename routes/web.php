<?php

use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogueController::class, 'index'])->name('catalogue.index');
Route::get('/catalogue/{id}', [CatalogueController::class, 'show'])->whereNumber('id')->name('catalogue.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store'])->middleware('throttle:10,1');
});

Route::pattern('product', '[0-9]+');

Route::middleware(['auth', 'cache.headers:private;no_store'])->group(function () {
    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');
    Route::resource('workspace', WorkspaceController::class)
        ->parameters(['workspace' => 'product'])
        ->except('destroy');
});
