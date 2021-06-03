<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Region;
use App\Models\PostalCode;
use App\Models\Coordinates;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = collect(Storage::files('posti-data'))
            ->filter(function($filePath) {

                // Only dat files
                return Str::endsWith($filePath, 'dat') && Str::contains($filePath, Carbon::now()->format('Ymd'));

            })
            ->last();

        if(!$file) return 0;



        // Open up the file
        $contents = Storage::get($file);

        // Explode rows
        $rows = explode(PHP_EOL, $contents);

        $data = [];
        $regions = [];

        foreach($rows as $row) {
            $zip = substr($row, 13, 5);
            $cityFi = trim(mb_substr(utf8_encode($row), 179, 20));
            $citySe = trim(mb_substr(utf8_encode($row), 199, 20));
            $areaFi = trim(mb_substr(utf8_encode($row), 116, 30));

            if(!$areaFi) {
                continue;
            }

            if(empty($regions[$areaFi])) {
                $regions[$areaFi] = [
                    'fi' => $areaFi,
                    'se' => trim(mb_substr(utf8_encode($row), 146, 30))
                ];
            }

            $data[$zip] = [
                'area_fi' => $areaFi,
                'city_fi' => $cityFi,
                'city_se' => $citySe
            ];
        }

        Region::truncate();
        PostalCode::truncate();

        DB::beginTransaction();
        foreach($regions as $area) {
            Region::create([
                'name_fi' => $area['fi'],
                'name_se' => $area['se']
            ]);
        }
        DB::commit();

        $regions = Region::get();

        DB::beginTransaction();
        foreach($data as $zip => $info) {

            $coords = Coordinates::search($info['city_fi'], 'FI');
            
            PostalCode::create([
                'region_id' => $regions->where('name_fi', $info['area_fi'])->first()->id,
                'postal_code' => $zip,
                'town_fi' => $info['city_fi'],
                'town_se' => $info['city_se'],
                'lat' => $coords['lat'],
                'lng' => $coords['lng']
            ]);
        }
        DB::commit();

        return 0;
    }
}
