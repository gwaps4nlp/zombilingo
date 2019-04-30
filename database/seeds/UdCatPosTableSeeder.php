<?php

use Illuminate\Database\Seeder;
use App\Models\CatPos;

class UdCatPosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CatPos::create(['slug' => 'ADJ', 'parent_id' => '0']);
        CatPos::create(['slug' => 'ADP', 'parent_id' => '0']);
        CatPos::create(['slug' => 'ADV', 'parent_id' => '0']);
        CatPos::create(['slug' => 'AUX', 'parent_id' => '0']);
        CatPos::create(['slug' => 'CCONJ', 'parent_id' => '0']);
        CatPos::create(['slug' => 'DET', 'parent_id' => '0']);
        CatPos::create(['slug' => 'INTJ', 'parent_id' => '0']);
        CatPos::create(['slug' => 'NOUN', 'parent_id' => '0']);
        CatPos::create(['slug' => 'NUM', 'parent_id' => '0']);
        CatPos::create(['slug' => 'PART', 'parent_id' => '0']);
        CatPos::create(['slug' => 'PRON', 'parent_id' => '0']);
        CatPos::create(['slug' => 'PROPN', 'parent_id' => '0']);
        CatPos::create(['slug' => 'PUNCT', 'parent_id' => '0']);
        CatPos::create(['slug' => 'SCONJ', 'parent_id' => '0']);
        CatPos::create(['slug' => 'SYM', 'parent_id' => '0']);
        CatPos::create(['slug' => 'VERB', 'parent_id' => '0']);
        CatPos::create(['slug' => 'X', 'parent_id' => '0']);
        CatPos::create(['slug' => 'UNK', 'parent_id' => '0']);
    }
}


