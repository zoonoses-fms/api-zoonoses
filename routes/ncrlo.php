<?php

use App\Http\Controllers\Api\V1\Ncrlo\CampaignController;
use App\Http\Controllers\Api\V1\Ncrlo\CampaignCycleController;
use App\Http\Controllers\Api\V1\Ncrlo\CampaingSupportController;
use App\Http\Controllers\Api\V1\Ncrlo\CampaingPointController;
use App\Http\Controllers\Api\V1\Ncrlo\VaccinationSupportController;
use App\Http\Controllers\Api\V1\Ncrlo\VaccinationPointController;
use App\Http\Controllers\Api\V1\Ncrlo\VaccinationWorkerController;
use Illuminate\Support\Facades\Route;

Route::get('campaign/cycle/payroll/csv/{id}', [CampaignCycleController::class, 'payrollCsv']);
Route::get('campaign/cycle/payroll/pdf/{id}', [CampaignCycleController::class, 'payroll']);
Route::get('campaign/cycle/report/{id}', [CampaignCycleController::class, 'report']);
Route::get('campaign/cycle/allocation/{id}', [CampaignCycleController::class, 'allocation']);
Route::get('campaign/cycle/frequency/{id}', [CampaignCycleController::class, 'frequency']);
Route::get('campaign/cycle/report/pdf/{id}', [CampaignCycleController::class, 'reportPdf']);
Route::get('campaign/cycle/public/map/{id}', [CampaignCycleController::class, 'publicMap']);

Route::apiResource('campaign/cycle', CampaignCycleController::class)
    ->middleware('auth:sanctum');

Route::get('campaign/support/frequency/{id}', [CampaingSupportController::class, 'frequency']);
Route::apiResource('campaign/support', CampaingSupportController::class)
    ->middleware('auth:sanctum');
Route::get('campaign/point/frequency/{id}', [CampaingPointController::class, 'frequency']);
Route::apiResource('campaign/point', CampaingPointController::class)
    ->middleware('auth:sanctum');

Route::get('campaign/report/{id}', [CampaignController::class, 'report']);
Route::get('campaign/report/pdf/{id}', [CampaignController::class, 'reportPdf']);

Route::apiResource('campaign', CampaignController::class);

Route::apiResource('vaccination/support', VaccinationSupportController::class)
->middleware('auth:sanctum');
Route::apiResource('vaccination/point', VaccinationPointController::class)
->middleware('auth:sanctum');

Route::get('vaccination/worker/search', [VaccinationWorkerController::class, 'search']);
Route::get('vaccination/worker/search/locations', [VaccinationWorkerController::class, 'searchLocations']);
Route::post('vaccination/worker/login', [VaccinationWorkerController::class, 'login']);
Route::apiResource('vaccination/worker', VaccinationWorkerController::class)
->middleware('auth:sanctum');
