<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostalCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id', 'town_fi', 'town_se', 'postal_code', 'lat', 'lng'
    ];

    public static $allowedQueries = [
        'town_fi' => 'town_fi', 
        'town_se' => 'town_se', 
        'postal_code' => 'postal_code', 
        'region_name_fi' => 'regions.name_fi',
        'region_name_se' => 'regions.name_se'
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    static function resourceQuery()
    {
        return self::select('town_fi', 'town_se', 'postal_code', 'lat', 'lng', 'regions.name_fi as region_name_fi', 'regions.name_se as region_name_se')
            ->leftJoin('regions', 'regions.id', 'postal_codes.region_id');
    }
}
