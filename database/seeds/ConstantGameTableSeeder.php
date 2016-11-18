<?php

use Illuminate\Database\Seeder;
use App\Models\ConstantGame;

class ConstantGameTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ConstantGame::create([
            'key' => 'user-per-page',
            'value' => '10',
            'description' => "Nombre de joueurs par pages dans le classement"
        ]);
        ConstantGame::create([
            'key' => 'turns-game',
            'value' => '10',
            'description' => "Nombre de phases dans le jeu"
        ]);
        ConstantGame::create([
            'key' => 'turns-admin-game',
            'value' => '10',
            'description' => "Nombre de phases en mode test"
        ]);
        ConstantGame::create([
            'key' => 'turns-special',
            'value' => '10',
            'description' => "Nombre de phases du mode spécial"
        ]);
        ConstantGame::create([
            'key' => 'turns-training',
            'value' => '10',
            'description' => "Nombre de phases maximum dans le tutoriel"
        ]);
        ConstantGame::create([
            'key' => 'turns-demo',
            'value' => '10',
            'description' => "Nombre de phases maximum dans la demo"
        ]);
        ConstantGame::create([
            'key' => 'bonus-perfect',
            'value' => '20',
            'description' => "Nombre de cerveaux gagnés grâce à une partie parfaite"
        ]);
        ConstantGame::create([
            'key' => 'multiplier-price-ingame',
            'value' => '1.5',
            'description' => "Multiplicateur de prix si le joueur achète l'objet lors du jeu"
        ]);
        ConstantGame::create([
            'key' => 'multiplier-boss',
            'value' => '10',
            'description' => "Multiplicateur de gains pour le boss"
        ]);
        ConstantGame::create([
            'key' => 'multiplier-gain',
            'value' => '10',
            'description' => "Multiplicateur pour le gain du jeu normal"
        ]);
        ConstantGame::create([
            'key' => 'multiplier-extractor',
            'value' => '4',
            'description' => "Multiplicateur appliqué lors de l'utilisation de l'extracteur de cerveaux"
        ]);
        ConstantGame::create([
            'key' => 'relation-demo',
            'value' => 'suj',
            'description' => "Relation utilisée pour la démo"
        ]);
        ConstantGame::create([
            'key' => 'proba-object',
            'value' => '5',
            'description' => "Probabilité d'obtenir un objet"
        ]);
        ConstantGame::create([
            'key' => 'proba-mwe',
            'value' => '10',
            'description' => "de voir apparaitre rigor mortis"
        ]);
        ConstantGame::create([
            'key' => 'time-mwe',
            'value' => '1',
            'description' => "Temps en secondes avant de lancer voir si rigor mortis apparait"
        ]);

        ConstantGame::create([
            'key' => 'length-mwe',
            'value' => '60',
            'description' => "Temps de jeu en secondes autorisé pour rigor mortis"
        ]);
        ConstantGame::create([
            'key' => 'proba-meat',
            'value' => '5',
            'description' => "Probabilité de voir apparaitre un bout de viande renfermant un objet"
        ]);
        ConstantGame::create([
            'key' => 'proba-bat',
            'value' => '5',
            'description' => "Probabilité de voir apparaitre la chauve-souris en pourcentage"
        ]);
        ConstantGame::create([
            'key' => 'proba-shrink',
            'value' => '5',
            'description' => "Probabilité que la phrase rapetisse de plus en plus!"
        ]);
        ConstantGame::create([
            'key' => 'proba-vanishing',
            'value' => '5',
            'description' => "Probabilité que la phrase disparaisse petit à petit"
        ]);
        ConstantGame::create([
            'key' => 'level-hard',
            'value' => '10',
            'description' => "Niveau à partir du quel le mode difficile est activé"
        ]);
        ConstantGame::create([
            'key' => 'gain-sentence',
            'value' => '1',
            'description' => "Argent gagné à chaque phrase finie"
        ]);
        ConstantGame::create([
            'key' => 'gain-mwe',
            'value' => '1',
            'description' => "Gain pour chaque réponse à rigor mortis"
        ]);
        ConstantGame::create([
            'key' => 'proba-negative-item-training',
            'value' => '33',
            'description' => "Probabilité d'avoir un item négatif pendant le tutorial"
        ]);
        ConstantGame::create([
            'key' => 'proba-negative-item-game',
            'value' => '33',
            'description' => "Probabilité, sachant que c'est une phrase de référence, d'avoir un item négatif pendant le jeu"
        ]);
        ConstantGame::create([
            'key' => 'default-corpus',
            'value' => '5',
            'description' => "Corpus pré-annoté par défaut"
        ]);
    }

}
