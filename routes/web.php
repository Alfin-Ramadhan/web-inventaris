<?php

declare(strict_types=1);

use App\Http\Controllers\ItemPdfController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function (): void {
    Route::get('/admin/items/{item:inventory_number}/pdf', ItemPdfController::class)
        ->name('items.pdf');
});
