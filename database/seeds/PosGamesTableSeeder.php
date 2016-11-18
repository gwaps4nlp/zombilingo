<?php

use Flynsarmy\CsvSeeder\CsvSeeder;

class PosGamesTableSeeder extends CsvSeeder {

    public function __construct()
    {
        $this->table = 'pos_games';
        $this->filename = base_path().'/database/seeds/csvs/PosGame.csv';
        $this->csv_delimiter = ";";
    }

    public function run()
    {
        // Recommended when importing larger CSVs
        DB::disableQueryLog();
        // Uncomment the below to wipe the table clean before populating
        DB::table($this->table)->delete();
        parent::run();
    }
}