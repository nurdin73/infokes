<?php

use App\Http\Controllers\PasienController;
use App\Http\Controllers\RegisterPasienController;
use Illuminate\Support\Facades\Auth;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false
]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('pasien/export', [PasienController::class, 'export'])->middleware('auth')->name('pasien.export');
Route::get('pasien/search', [PasienController::class, 'getPasiens'])->middleware('auth')->name('pasien.search');
Route::resource('pasien', PasienController::class)->middleware('auth');
Route::get('register-pasien', [RegisterPasienController::class, 'index'])->middleware('auth')->name('register-pasien');
Route::get('register-pasien/export', [RegisterPasienController::class, 'export'])->middleware('auth')->name('register-pasien.export');
Route::get('register-pasien/create', [RegisterPasienController::class, 'create'])->middleware('auth')->name('register-pasien.create');
Route::post('register-pasien/store', [RegisterPasienController::class, 'store'])->middleware('auth')->name('register-pasien.store');
Route::post('register-pasien/payment', [RegisterPasienController::class, 'storePayment'])->middleware('auth')->name('register-pasien.storePayment');
Route::get('register-pasien/{patient_id}/medic', [RegisterPasienController::class, 'registerMedic'])->middleware('auth')->name('register-pasien.registerMedic');
Route::post('register-pasien', [RegisterPasienController::class, 'poliRegistration'])->middleware('auth')->name('register-pasien.poliRegistration');
Route::get('register-pasien/{id}/payment', [RegisterPasienController::class, 'payment'])->middleware('auth')->name('register-pasien.payment');
Route::get('register-pasien/{id}', [RegisterPasienController::class, 'show'])->middleware('auth')->name('register-pasien.show');
Route::put('register-pasien/{id}', [RegisterPasienController::class, 'cancel'])->middleware('auth')->name('register-pasien.cancel');
