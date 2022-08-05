<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Geocoder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class GeocoderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $response = [];

        if ($request->has('keyword')) {
            if ($request->has('provider')) {
                if (strcmp($request->provider, 'google') == 0) {
                    $response = Geocoder::searchGoogleByName($request->keyword);
                } elseif (strcmp($request->provider, 'google_place') == 0) {
                    $response = Geocoder::searchGooglePlaceByName($request->keyword);
                } elseif (strcmp($request->provider, 'nominatim') == 0) {
                    if (Auth::check()) {
                        $email = Auth::user()->email;
                        $response = Geocoder::searchNominatimByName($request->keyword, $email);
                    }
                    $response = Geocoder::searchNominatimByName($request->keyword);
                }
            } else {
                $response = Geocoder::searchByName($request->keyword);
            }
        }

        return $this->success($response);
    }
}
