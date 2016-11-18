<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Role, App\Models\User, App\Models\Contact, App\Models\Language, App\Models\Object, App\Models\Level;
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Role::create([
            'label' => 'User',
            'slug' => 'user'
        ]);

        Role::create([
            'label' => 'Administrator',
            'slug' => 'admin'
        ]);

        Role::create([
            'label' => 'Guest',
            'slug' => 'guest'
        ]);

        Language::create([
            'label' => 'French',
            'slug' => 'fr'
        ]);

        Language::create([
            'label' => 'English',
            'slug' => 'en'
        ]);
        
        Object::create([
            'name' => 'Main de Midas',
            'slug' => 'midas',
            'price' => 100,
            'description' => 'Transforme les cerveaux gagnÃ©s en or.',
            'image' => 'main_midas.png'
        ]);
        
        Object::create([
            'name' => 'Lunettes',
            'slug' => 'glasses',
            'price' => 10,
            'description' => 'Permet de revoir les phrases effacÃ©es.',
            'image' => 'lunettes.png'
        ]);
        
        Object::create([
            'name' => 'Extracteur',
            'slug' => 'extractor',
            'price' => 200,
            'description' => 'Permet de rÃ©cupÃ©rer plus de cerveaux lors de l\'annotation d\'une phrase',
            'image' => 'extracteur.png'
        ]);

        Object::create([
            'name' => 'Longue vue',
            'slug' => 'telescope',
            'price' => 10,
            'description' => 'Permet de faire rÃ©apparaÃ®tre une phrase qui s\'est rapetissÃ©e',
            'image' => 'longue_vue.png'
        ]);

        Level::create([
            'id' => 1,
            'name' => 'LÃ©gÃ¨rement infectÃ©',
            'slug' => 'level1',
            'image' => 'z1.png',
            'required_score' => '0'
        ]);

        Level::create([
            'id' => 2,
            'name' => 'ZombifiÃ©!',
            'slug' => 'level2',
            'image' => 'z2.png',
            'required_score' => '5000'
        ]);

        Level::create([
            'id' => 3,
            'name' => 'Totalement zombi',
            'slug' => 'level3',
            'image' => 'z3.png',
            'required_score' => '50000'
        ]);

        Level::create([
            'id' => 4,
            'name' => 'DÃ©voreur de cerveaux',
            'slug' => 'level4',
            'image' => 'z4.png',
            'required_score' => '120000'
        ]);

        Level::create([
            'id' => 5,
            'name' => 'Ã‰tat de dÃ©composition avancÃ©',
            'slug' => 'level5',
            'image' => 'z5.png',
            'required_score' => '250000'
        ]);

        Level::create([
            'id' => 6,
            'name' => 'Accro au cerveaux',
            'slug' => 'level6',
            'image' => 'z6.png',
            'required_score' => '620000'
        ]);

        Level::create([
            'id' => 7,
            'name' => 'PutrÃ©faction absolue',
            'slug' => 'level7',
            'image' => 'z7.png',
            'required_score' => '1045000'
        ]);
        
        $this->call(RelationTableSeeder::class);
        $this->call(SourceTableSeeder::class);
        $this->call(BonusTableSeeder::class);
        $this->call(TrophyTableSeeder::class);
        $this->call(ConstantGameTableSeeder::class);
        $this->call(CatPosTableSeeder::class);
        $this->call(PosGameTableSeeder::class);
        $this->call(CatPosPosGameTableSeeder::class);

        Model::reguard();
    }
}
