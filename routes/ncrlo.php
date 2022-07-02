<?php

use App\Http\Controllers\Api\V1\Ncrlo\VaccinationCampaignController;
use App\Http\Controllers\Api\V1\Ncrlo\VaccinationCampaingSupportController;
use App\Http\Controllers\Api\V1\Ncrlo\VaccinationSupportController;
use Illuminate\Support\Facades\Route;

Route::apiResource('campaign', VaccinationCampaignController::class)->middleware('auth:sanctum');
Route::apiResource('campaign/support', VaccinationCampaingSupportController::class)->middleware('auth:sanctum');
Route::apiResource('vaccination/support', VaccinationSupportController::class)->middleware('auth:sanctum');