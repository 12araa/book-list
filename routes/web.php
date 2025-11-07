<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\RatingController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [BookController::class, 'index'])->name('books.list');

Route::get('/api/categories/search', [BookController::class, 'search']);

Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');

Route::get('/books/{id}/rate', [RatingController::class,'rateForm']);
Route::post('/books/{id}/rate', [RatingController::class,'rateStore']);

