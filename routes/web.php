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
// auth()->loginUsingId(1);
Route::get('/', [\App\Http\Controllers\HomeController::class, 'show'])->name('home');

// Route::get('/ttt', function () {
//     // test email
//     event(new \App\Events\PersonIsLeaving(\App\Models\People::inRandomOrder()->first()));
//     return 'hello';
// });
Route::get('/alter-leaving-date/{supervisee}/{supervisor}', [\App\Http\Controllers\AlterLeavingDateController::class, 'edit'])->name('supervisor.edit_leaving_date_supervisee');
Route::post('/alter-leaving-date/{supervisee}/{supervisor}', [\App\Http\Controllers\AlterLeavingDateController::class, 'update'])->name('supervisor.update_leaving_date_supervisee');

Route::post('/person/{person}/task', [\App\Http\Controllers\PersonTaskController::class, 'update'])->name('person.task.update');
Route::get('/admin/units', [\App\Http\Controllers\UnitController::class, 'index'])->name('units.index');
Route::post('/admin/units', [\App\Http\Controllers\UnitController::class, 'store'])->name('unit.store');
Route::post('/admin/units/{unit}', [\App\Http\Controllers\UnitController::class, 'update'])->name('unit.update');

Route::get('/person/{person}', [\App\Http\Controllers\PeopleController::class, 'show'])->name('people.show');
Route::get('/reports/people', [\App\Http\Controllers\PeopleReportController::class, 'show'])->name('reports.people');
Route::get('/reports/outstanding-tasks', [\App\Http\Controllers\OutstandingTaskReportController::class, 'show'])->name('reports.tasks.outstanding');
Route::get('/reports/outstanding-tasks/export', [\App\Http\Controllers\OutstandingTaskReportController::class, 'export'])->name('reports.tasks.outstanding.export');
Route::get('/reports/outstanding-tasks-by-task', [\App\Http\Controllers\OutstandingTaskReportController::class, 'showByTask'])->name('reports.tasks.outstanding_by_task');
Route::get('/reports/outstanding-tasks-by-task/export', [\App\Http\Controllers\OutstandingTaskReportController::class, 'exportByTask'])->name('reports.tasks.outstanding_by_task.export');
Route::get('/export/arrivals-departures', [\App\Http\Controllers\ExportController::class, 'arrivalsDepartures'])->name('export.arrivals_departures');
Route::get('/reports/stats', [\App\Http\Controllers\StatsReportController::class, 'show'])->name('reports.stats');
Route::get('/options', [\App\Http\Controllers\OptionsController::class, 'edit'])->name('options.edit');
Route::post('/options', [\App\Http\Controllers\OptionsController::class, 'update'])->name('options.update');
