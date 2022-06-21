<?php

use App\Http\Controllers\Api\V1\Location\TheSaadController;
use App\Http\Controllers\Api\V1\Location\TheNeighborhoodController;
use App\Http\Controllers\Api\V1\Location\TheBlockController;
use App\Http\Controllers\Api\V1\Location\TheSubLoacationController;

use Illuminate\Support\Facades\Route;

Route::apiResource('saad', TheSaadController::class);
Route::apiResource('neighborhood', TheNeighborhoodController::class);
Route::apiResource('block', TheBlockController::class);
Route::apiResource('sub-loacation', TheSubLoacationController::class);
