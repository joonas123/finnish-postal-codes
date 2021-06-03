<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\PostalCode;
use App\Models\Coordinates;

class SearchController extends Controller
{
    /**
     * @param Request $request
     * @return response
     */
    public function regions(Request $request)
    {
        $query = Region::resourceQuery();

        $query = $this->filter(Region::class, $query, $request);

        $regions = $query->get();

        return response($regions, 200);
    }

    public function postalCodes(Request $request)
    {
        $query = PostalCode::resourceQuery();

        $query = $this->filter(PostalCode::class, $query, $request);

        $postalCodes = $query->get();
        
        return response($postalCodes, 200);

    }

    public function townForPostalCode($code)
    {
        $code = PostalCode::where('postal_code', $code)->first();

        return response([
            'town_fi' => $code->town_fi ?? '',
            'town_se' => $code->town_se ?? '',
        ], 200);

    }

    public function postalCodeAsKey(Request $request)
    {
        $query = PostalCode::resourceQuery();

        $query = $this->filter(PostalCode::class, $query, $request);

        $postalCodes = $query->get();
        
        $valueColumn = $request->value_column ?? 'town_fi';

        $result = [];
        foreach($postalCodes as $code) {
            $result[$code->postal_code] = $code->$valueColumn;
        }

        return response($result, 200);
    }

    public function coordinates(Request $request)
    {
        $coords = Coordinates::search($request->search, $request->country);
        return response($coords, 200);
    }

    protected function filter($model, $query, Request $request)
    {
        foreach($model::$allowedQueries as $filter => $column) {
            if($request->input($filter)) {
                $query->whereIn($column, explode(',', $request->input($filter)));
            }
        }
        return $query;
    }

}
