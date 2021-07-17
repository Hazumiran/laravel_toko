<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('pesan/{id}', [App\Http\Controllers\PesanController::class, 'index'])->name('pesan');
Route::post('pesan/{id}', [App\Http\Controllers\PesanController::class, 'pesan'])->name('pesanpost');

Route::get('checkout', [\App\Http\Controllers\PesanController::class, 'checkout'])->name('checkout');
Route::delete('checkout/{id}', [\App\Http\Controllers\PesanController::class, 'checkout_delete'])->name('checkout_delete');
Route::get('confirm', [\App\Http\Controllers\PesanController::class, 'confirm'])->name('confirm');

Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
Route::post('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('updateprofile');

Route::get('history', [\App\Http\Controllers\HistoryController::class, 'index'])->name('history');
Route::get('history/{id}', [\App\Http\Controllers\HistoryController::class, 'detail'])->name('historydetail');
