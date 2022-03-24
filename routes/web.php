<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', [\App\Http\Controllers\HomeController::class, 'show'])->name('home');
Route::get('/person/{person}', [\App\Http\Controllers\PeopleController::class, 'show'])->name('people.show');
Route::get('/reports/people', [\App\Http\Controllers\PeopleReportController::class, 'show'])->name('reports.people');

Route::get('mail', function () {
    return new \App\Mail\ArrivalsAndDeparturesMail(\App\Models\People::take(10)->get(), \App\Models\People::take(10)->get());
});
