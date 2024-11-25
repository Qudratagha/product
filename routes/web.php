<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

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

Route::get('/', [ProductController::class, 'index'])->name('products.index');
Route::get('/products', [ProductController::class, 'fetch'])->name('products.fetch'); // Fetch all products
Route::post('/store', [ProductController::class, 'store'])->name('products.store');  // Store product
Route::get('/edit/{index}', [ProductController::class, 'edit'])->name('products.edit'); // Fetch product for editing
Route::post('/update/{index}', [ProductController::class, 'update'])->name('products.update'); // Update product
