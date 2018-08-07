<?php

use Illuminate\Database\Seeder;
use Gwaps4nlp\Core\Models\Trophy;

class TrophyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Trophy::create([
            'name'=>'Mangeur de cerveaux régulier',
            'slug'=>'consecutive_quests_trophy',
            'maximum_floor'=>6,
            'description'=>' quêtes consécutives réalisées',
        ]);

        Trophy::create([
            'name'=>'Grand mangeur de cerveaux',
            'slug'=>'quests_trophy',
            'maximum_floor'=>6,
            'description'=>' quêtes réalisées',
        ]);

        Trophy::create([
            'name'=>'Mangeur de cerveaux rares',
            'slug'=>'rre_quest_trophy',
            'maximum_floor'=>6,
            'description'=>' quêtes rares réalisées',
        ]);

        Trophy::create([
            'name'=>'Mangeur de cerveaux émérite',
            'slug'=>'weekly_first_trophy',
            'maximum_floor'=>6,
            'description'=>' jours premier au classement des annotations de la semaine',
        ]);

        Trophy::create([
            'name'=>'Mangeur de cerveaux occasionnel',
            'slug'=>'challenge_annotation_trophy',
            'maximum_floor'=>6,
            'description'=>' annotations réalisées pendant un événement',        
        ]);
    }
}
