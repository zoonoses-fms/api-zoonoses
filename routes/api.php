<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\GeocoderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
 */

Route::prefix('v1')->group(
    function () {
        require __DIR__ . '/auth.php';

        Route::prefix('map')->group(
            function () {
                require __DIR__ . '/location.php';
            }
        );

        Route::prefix('ncrlo')->group(
            function () {
                require __DIR__ . '/ncrlo.php';
            }
        );

        Route::get('/geocoder', GeocoderController::class);
    }
);
