<?php

use Illuminate\Database\Seeder;
use Gwaps4nlp\Core\Models\Source;

class SourceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Source::create([
            'id' => 1,
            'slug' => 'reference',
            'description' => 'annotations de référence'
        ]);
        Source::create([
            'id' => 2,
            'slug' => 'user',
            'description' => 'annotations produites par un joueur'
        ]);
        Source::create([
            'id' => 3,
            'slug' => 'preannotated',
            'description' => 'annotations brutes préannotées'
        ]);
        Source::create([
            'id' => 4,
            'slug' => 'expert',
            'description' => 'annotations produites par un expert'
        ]);
        Source::create([
            'id' => 5,
            'slug' => 'evaluation',
            'description' => 'Pre-annotated corpus for evaluation'
        ]); 
        Source::create([
            'id' => 6,
            'slug' => 'training',
            'description' => 'annotations produites par un joueur lors sèquences d entrainement'
        ]);        
    }
  
}
