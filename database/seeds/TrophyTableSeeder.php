<?php

use Illuminate\Database\Seeder;
use App\Models\Trophy;

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
            'id'    => 1,
            'name' => 'Doué pour l\'annotation',
            'slug' => 'trophy-gifted',
            'key' => 'perfect',
            'required_value' => '10',
            'description' => 'Jouer une partie sans aucune faute&nbsp;!',
            'points' => 5,
            'is_secret' => 1,
            'image' => 'as_annotation.png'
        ]);
        Trophy::create([
            'id'    => 2,            
            'name' => 'Bon élève',
            'slug' => 'trophy-good-student',
            'key' => 'training',            
            'required_value' => '1',
            'description' => 'Tu as fini une formation&nbsp;!',
            'points' => 5,
            'is_secret' => 1,
            'image' => 'bon_eleve.png'
        ]);
        Trophy::create([
            'id'    => 3,                
            'name' => 'Médecin légiste',
            'slug' => 'trophy-medical-examiner',
            'key' => 'number_mwes',         
            'required_value' => '10',
            'description' => 'Tu aimes jouer à Rigor Mortis',
            'points' => 10,
            'is_secret' => 1,
            'image' => 'medecin_legiste.png'
        ]);
        Trophy::create([
            'id'    => 4,               
            'name' => 'Spéléologue',
            'slug' => 'trophy-speleologist',
            'key' => 'number_objects',          
            'required_value' => '1',
            'description' => 'Tu as trouvé de beaux objets&nbsp;!',
            'points' => 5,
            'is_secret' => 1,
            'image' => 'speleologue.png'
        ]);
        Trophy::create([
            'name' => 'As de l\'annotation',
            'slug' => 'trophy-as',
            'key' => 'perfect',
            'required_value' => '50',
            'description' => 'Jouer une partie sans aucune faute&nbsp;!',
            'points' => 5,
            'is_secret' => 0,
            'image' => 'as_annotation.png'
        ]);
        Trophy::create([
            'name' => 'Expert de l\'annotation',
            'slug' => 'trophy-expert',
            'key' => 'perfect',
            'required_value' => '250',
            'description' => 'Jouer une partie sans aucune faute&nbsp;!',
            'points' => 5,
            'is_secret' => 0,
            'image' => 'as_annotation.png'
        ]);
        Trophy::create([
            'name' => 'Eleve assidu',
            'slug' => 'trophy-assiduous-student',
			'key' => 'training',			
            'required_value' => '20',
            'description' => 'Tu as fini une formation&nbsp;!',
            'points' => 5,
            'is_secret' => 0,
            'image' => 'bon_eleve.png'
        ]);	
        Trophy::create([
            'name' => 'Dr. Frankenperrier',
            'slug' => 'trophy-dr-frankenperrier',
			'key' => 'number_mwes',			
            'required_value' => '50',
            'description' => 'Tu aimes jouer à Rigor Mortis',
            'points' => 10,
            'is_secret' => 0,
            'image' => 'medecin_legiste.png'
        ]);
        Trophy::create([
            'name' => 'Capitaine du Styx',
            'slug' => 'trophy-capitan-styx',
			'key' => 'number_mwes',	
            'required_value' => '250',
            'description' => 'Tu aimes jouer à Rigor Mortis',
            'points' => 10,
            'is_secret' => 0,
            'image' => 'medecin_legiste.png'
        ]);
        Trophy::create([
            'name' => 'Chercheur d\'or',
            'slug' => 'trophy-gold-digger',
			'key' => 'number_objects',			
            'required_value' => '50',
            'description' => 'Tu as trouvé de beaux objets&nbsp;!',
            'points' => 5,
            'is_secret' => 0,
            'image' => 'speleologue.png'
        ]);
        Trophy::create([
            'name' => 'Super chercheur d\'or',
            'slug' => 'trophy-super-gold-digger',
			'key' => 'number_objects',			
            'required_value' => '250',
            'description' => 'Tu as trouvé de beaux objets&nbsp;!',
            'points' => 5,
            'is_secret' => 0,
            'image' => 'speleologue.png'
        ]);
        Trophy::create([
            'name' => 'Accro au cerveaux',
            'slug' => 'trophy-accro',
			'key' => 'won',
            'required_value' => '10',
            'description' => 'Tu as gagné des parties&nbsp;!',
            'points' => 5,
            'is_secret' => 0,
            'image' => 'speleologue.png'
        ]);
        Trophy::create([
            'name' => 'Zombie d\'or',
            'slug' => 'trophy-zombie',
			'key' => 'won',			
            'required_value' => '50',
            'description' => 'Tu as gagné de nombreuses parties&nbsp;!',
            'points' => 5,
            'is_secret' => 0,
            'image' => 'speleologue.png'
        ]);
    }

}
