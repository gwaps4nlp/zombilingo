<?php

use Flynsarmy\CsvSeeder\CsvSeeder;

class CatPosPosGameTableSeeder extends CsvSeeder {

    public function __construct()
    {
        $this->table = 'cat_pos_pos_game';
        $this->filename = base_path().'/database/seeds/csvs/CatPosPosGame.csv';
        $this->csv_delimiter = ";";
    }

    public function run()
    {
        // Recommended when importing larger CSVs
        DB::disableQueryLog();
        // Uncomment the below to wipe the table clean before populating
//        DB::table($this->table)->delete();
        parent::run();

        DB::update("update `annotations` set `category_id`='V', pos_id='VPR' where category_id = 'VPR'");
        DB::update("update `annotations` set `category_id`='V', pos_id='VS' where category_id = 'VS'");
        DB::update("update `annotations` set `category_id`='V', pos_id='VINF' where category_id = 'VINF'");
        DB::update("update `annotations` set `category_id`='V', pos_id='VPP' where category_id = 'VPP'");
        DB::update("update `annotations` set `category_id`='A', pos_id='ADJ' where category_id = 'ADJ'");
        DB::update("update `annotations` set `category_id`='PRO', pos_id='PROWH' where category_id = 'PROWH'");
        DB::update("update `annotations` set `category_id`='N', pos_id='NPP' where category_id = 'NPP'");
      
        DB::update('update annotations, cat_pos set annotations.category_id=cat_pos.id where cat_pos.slug=annotations.category_id and cat_pos.parent_id=0');        
        DB::update('update annotations, cat_pos set annotations.pos_id=cat_pos.id where replace(cat_pos.slug,"_pos","") =annotations.pos_id and cat_pos.parent_id!=0');
     
    }
}