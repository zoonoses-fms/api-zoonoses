<?php

use App\Http\Controllers\Api\V1\Ncrlo\CampaignController;
use App\Http\Controllers\Api\V1\Ncrlo\CampaignCycleController;
use App\Http\Controllers\Api\V1\Ncrlo\CampaingSupportController;
use App\Http\Controllers\Api\V1\Ncrlo\CampaingPointController;
use App\Http\Controllers\Api\V1\Ncrlo\VaccinationSupportController;
use App\Http\Controllers\Api\V1\Ncrlo\VaccinationPointController;
use App\Http\Controllers\Api\V1\Ncrlo\VaccinationWorkerController;
use Illuminate\Support\Facades\Route;

Route::get('campaign/cycle/report/{id}', [CampaignCycleController::class, 'report']);
Route::apiResource('campaign/cycle', CampaignCycleController::class)
    ->middleware('auth:sanctum');
Route::apiResource('campaign/support', CampaingSupportController::class)
    ->middleware('auth:sanctum');
Route::apiResource('campaign/point', CampaingPointController::class)
    ->middleware('auth:sanctum');
Route::apiResource('campaign', CampaignController::class)
    ->middleware('auth:sanctum');

Route::apiResource('vaccination/support', VaccinationSupportController::class)
->middleware('auth:sanctum');
Route::apiResource('vaccination/point', VaccinationPointController::class)
->middleware('auth:sanctum');

Route::get('vaccination/worker/search', [VaccinationWorkerController::class, 'search']);
Route::apiResource('vaccination/worker', VaccinationWorkerController::class)
->middleware('auth:sanctum');
