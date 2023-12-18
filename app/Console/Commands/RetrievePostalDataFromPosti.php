<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use ZipArchive;
use GuzzleHttp\Client;

class RetrievePostalDataFromPosti extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retrieve_postal_data_from_posti {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the file containing postal codes, regions and towns';

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
        $date = $this->date();
        $storePath = storage_path("app/posti-zips/$date.zip");

        try {
            
            $client = new Client();
            $response = $client->request('GET', "https://www.posti.fi/webpcode/PCF_$date.zip");
            $fileContents = $response->getBody()->getContents();
            file_put_contents($storePath, $fileContents);

            $this->unzipFile($storePath);
        } catch (\Exception $e) {
            //
        }

        return 0;
    }

    protected function date()
    {
        return $this->argument('date')
            ? $this->argument('date')
            : Carbon::now()->format('Ymd');
    }
    
    protected function unzipFile($file)
    {
        $zip = new ZipArchive;
        $fileOpened = $zip->open($file);
        if ($fileOpened === TRUE) {
            $zip->extractTo(storage_path('app/posti-data'));
            $zip->close();
            return true;
        }
        return false;
    }
}
