<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ManageController;

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

Route::get('/', [ManageController::class, 'index'])->name('welcome');
Route::post('/', [ManageController::class, 'index'])->name('welcome');
Route::get('/process/{file?}', [ManageController::class, 'process']);
Route::resource('manage', ManageController::class);
