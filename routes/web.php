<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserDetailController;

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

// Route::get('/', function () {
//     return view('welcome');
// });



Route::get('/', [UserDetailController::class, 'index'])->name('home');
Route::get('/create', [UserDetailController::class, 'create'])->name('create');
Route::post('/store', [UserDetailController::class, 'store'])->name('store');
Route::get('/export-csv', [UserDetailController::class, 'exportCSV'])->name('export.csv');



Route::get('/get-countries', [UserDetailController::class, 'getCountries'])->name('get.countries');
Route::get('/get-states/{country}', [UserDetailController::class, 'getStates'])->name('get.states');

