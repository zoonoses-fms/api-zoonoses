<?php

namespace App\Models;

use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;
use Geocoder\StatefulGeocoder;
use Geocoder\ProviderAggregator;
use Geocoder\Dumper\GeoJson;
use Geocoder\Provider\Chain\Chain;
use Http\Adapter\Guzzle7\Client;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\Provider\GoogleMapsPlaces\GoogleMapsPlaces;
use Geocoder\Provider\Nominatim\Nominatim;
use App\Providers\NominatimServiceProvider;
use Geocoder\Location;
use Geocoder\Model\Address;

class Geocoder
{
    public static function searchGoogleByName($StringQuery)
    {
        $httpClient = new Client();
        $provider = new GoogleMaps($httpClient, null, env('GOOGLE_MAPS_API_KEY'));
        $geocoder = new StatefulGeocoder($provider, env('GOOGLE_MAPS_LOCALE', 'us'));
        return self::toArrayGeoJson(
            $geocoder->geocodeQuery(
                GeocodeQuery::create($StringQuery)
            )->all()
        );
    }

    public static function searchGooglePlaceByName($StringQuery)
    {
        $httpClient = new Client();
        $provider = new GoogleMapsPlaces($httpClient, env('GOOGLE_MAPS_API_KEY'));
        $geocoder = new StatefulGeocoder($provider, env('GOOGLE_MAPS_LOCALE', 'us'));
        return self::toArrayGeoJson(
            $geocoder->geocodeQuery(
                GeocodeQuery::create($StringQuery)
                ->withData('mode', GoogleMapsPlaces::GEOCODE_MODE_SEARCH)
            )->all()
        );
    }

    public static function searchNominatimByName($StringQuery, $email = '')
    {
        $httpClient = new Client();
        // $provider = Nominatim::withOpenStreetMapServer($httpClient, 'api-laravel');

        if (!empty($email)) {
            $provider = new NominatimServiceProvider(
                $httpClient,
                'https://nominatim.openstreetmap.org',
                'api-laravel',
                '',
                ['email' => $email]
            );
        } else {
            $provider = new NominatimServiceProvider(
                $httpClient,
                'https://nominatim.openstreetmap.org',
                'api-laravel',
                ''
            );
        }


        $geocoder = new StatefulGeocoder($provider, env('GOOGLE_MAPS_LOCALE', 'us'));
        return self::toArrayGeoJson(
            $geocoder->geocodeQuery(
                GeocodeQuery::create($StringQuery)
            )->all()
        );
    }

    public static function searchByName($StringQuery)
    {
        $httpClient = new Client();
        $geocoder = new ProviderAggregator();

        $chain = new Chain([
            new GoogleMaps($httpClient, null, env('GOOGLE_MAPS_API_KEY')),
            new GoogleMapsPlaces($httpClient, env('GOOGLE_MAPS_API_KEY')),
            new NominatimServiceProvider(
                $httpClient,
                'https://nominatim.openstreetmap.org',
                'api-laravel',
                ''
            )
        ]);

        $geocoder->registerProvider($chain);

        return self::toArrayGeoJson(
            $geocoder->geocodeQuery(
                GeocodeQuery::create($StringQuery)
            )->all()
        );
    }

    public static function toArrayGeoJson($locations)
    {
        $list = array();
        foreach ($locations as $location) {
            $list[] = self::getArray($location);
        }
        return $list;
    }

    public static function toGeoJson($location)
    {
        $dumper = new GeoJson();
        $geoJson = $dumper->dump($location);

        return $geoJson;
    }

    /**
     * @param Location $location
     *
     * @return array
     */
    public static function getArray($location)
    {
        $properties = array_filter($location->toArray(), function ($value) {
            return !empty($value);
        });
        $properties['formattedAddress'] = $location->getFormattedAddress();

        unset(
            $properties['latitude'],
            $properties['longitude'],
            $properties['bounds']
        );

        if ([] === $properties) {
            $properties = null;
        }

        $lat = 0;
        $lon = 0;
        if (null !== $coordinates = $location->getCoordinates()) {
            $lat = $coordinates->getLatitude();
            $lon = $coordinates->getLongitude();
        }

        $array = [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [$lon, $lat],
            ],
            'properties' => $properties,
        ];

        if (null !== $bounds = $location->getBounds()) {
            $array['bounds'] = $bounds->toArray();
        }

        return $array;
    }
}
