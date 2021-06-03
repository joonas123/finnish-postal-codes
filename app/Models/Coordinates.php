<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coordinates extends Model
{
    use HasFactory;


    static function search(String $search, $country = 'FI') :array
    {
        if (!config('services.google_maps.secret')) {
            throw new \Exception('Google Maps API secret is not set!');
        }

        $result = self::select('id', 'lat', 'lng', 'search', 'type', 'updated_at')
            ->where('search', $search)
            ->where('country', $country)
            ->first();
            
        // if coordinates are found
        if ($result) {

            // if data is not older than 1 month
            if($result->updated_at->isAfter(now()->subMonth())) {
                return [ 
                    'lat' => $result->lat,
                    'lng' => $result->lng,
                    'type' => $result->type,
                ];
            }
        }

        // build json query
        $query = http_build_query([
            'address' => $search,
            'components' => 'country:' . $country,
            'key' => config('services.google_maps.secret'),
        ]);

        // fetch geo-data from Google
        $geoData = json_decode(file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?' . $query));

        // if geo data was not found!
        if ($geoData->status !== 'OK') {
            return [
                'lat' => null, 
                'lng' => null,
                'type' => null
            ];
        }

        // get some values from the result
        $coords = $geoData->results[0]->geometry->location;
        $type = $geoData->results[0]->geometry->location_type;

        // Create new entry if this doesn't exist already
        if (!$result) {
            $result = new Coordinates;
            $result->search = $search;
            $result->country = $country;
        }

        $result->lat = $coords->lat;
        $result->lng = $coords->lng;
        $result->type = $type;

        $result->save();

        return [ 
            'lat' => $coords->lat, 
            'lng' => $coords->lng,
            'type' => $type,
        ];
    }
}
