<?php

use Illuminate\Database\Seeder;
use Gwaps4nlp\Core\Models\Bonus;

class BonusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bonus::create([
            'name' => 'Talisman des morts',
            'slug' => 'increase-proba-mwe',
            'condition' => 'trophy-medical-examiner',
            'description' => 'Augmente la chance de pouvoir jouer à Rigor Mortis',
            'image' => 'talisman_morts.png',
            'multiplier' => 1.5
        ]);
        Bonus::create([
            'name' => 'Amulette de loot',
            'slug' => 'increase-proba-object',
            'condition' => 'trophy-speleologist',
            'description' => 'Augmente les chances d\'obtenir un objet',
            'image' => 'amulette_loot.png',
            'multiplier' => 1.5
        ]);
        Bonus::create([
            'name' => 'Cerveau entrainé',
            'slug' => 'choice-corpus',
            'condition' => 'level-3',
            'description' => 'Permet de choisir le corpus de jeu',
            'image' => 'cerveau_entraine.png'
        ]);
    }

}
