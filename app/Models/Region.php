<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_fi', 'name_se'
    ];

    public static $allowedQueries = [
        'name_fi' => 'name_fi', 
        'name_se' => 'name_se'
    ];

    public function postalCodes()
    {
        return $this->hasMany(PostalCode::class);
    }

    static function resourceQuery()
    {
        return self::select('name_fi', 'name_se');
    }
}
