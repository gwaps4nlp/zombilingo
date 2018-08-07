<?php
use Illuminate\Database\Seeder;
use App\Models\Quest;

class QuestTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Quest::create([
          'name' => 'Annotateur',
          'slug' => 'annot10',
          'description' => 'Annoter 10 phrases ',
          'required_value' => 10,
          'key' => 'nb_annotations'
        ]);
        Quest::create([
          'name' => 'Annotateur',
          'slug' => 'annot20',
          'description' => 'Annoter 20 phrases ',
          'required_value' => 20,
          'key' => 'nb_annotations'
        ]);
        Quest::create([
          'name' => 'Annotateur',
          'slug' => 'annot30',
          'description' => 'Annoter 30 phrases ',
          'required_value' => 30,
          'key' => 'nb_annotations'
        ]);
        Quest::create([
          'name' => 'Annotateur',
          'slug' => 'annot40',
          'description' => 'Annoter 40 phrases ',
          'required_value' => 40,
          'key' => 'nb_annotations'
        ]);
        Quest::create([
          'name' => 'Annotateur',
          'slug' => 'annot50',
          'description' => 'Annoter 50 phrases ',
          'required_value' => 50,
          'key' => 'nb_annotations',
          'rare'=> 1
        ]);
        Quest::create([
          'name' => 'Terminator',
          'slug' => 'game1',
          'description' => 'Terminer 1 parties',
          'required_value' => 1,
          'key' => 'game'
        ]);
        Quest::create([
          'name' => 'Terminator',
          'slug' => 'game2',
          'description' => 'Terminer 2 parties',
          'required_value' => 2,
          'key' => 'game'
        ]);
        Quest::create([
          'name' => 'Terminator',
          'slug' => 'game3',
          'description' => 'Terminer 3 parties',
          'required_value' => 3,
          'key' => 'game'
        ]);
        Quest::create([
          'name' => 'Terminator',
          'slug' => 'game4',
          'description' => 'Terminer 4 parties',
          'required_value' => 4,
          'key' => 'game'
        ]);
        Quest::create([
          'name' => 'Terminator',
          'slug' => 'game5',
          'description' => 'Terminer 5 parties',
          'required_value' => 5,
          'key' => 'game',
          'rare'=> 1
        ]);
        Quest::create([
          'name' => 'Compétiteur',
          'slug' => 'week1',
          'description' => 'Passer 1er sur le classement de la semaine',
          'required_value' => 1,
          'key' => 'week',
          'rare'=> 1
        ]);
        Quest::create([
          'name' => 'Dueliste',
          'slug' => 'duel',
          'description' => 'Participer à ou créer un duel',
          'required_value' => 1,
          'key' => 'duel_game'
        ]);
        Quest::create([
          'name' => 'Acheteur',
          'slug' => 'obj1',
          'description' => 'Acheter ou obtenir un objet',
          'required_value' => 1,
          'key' => 'nb_objects'
        ]);
        Quest::create([
          'name' => 'Acheteur',
          'slug' => 'obj2',
          'description' => 'Acheter ou obtenir 2 objets',
          'required_value' => 2,
          'key' => 'nb_objects'
        ]);
        Quest::create([
          'name' => 'Acheteur',
          'slug' => 'obj3',
          'description' => 'Acheter ou obtenir 3 objets',
          'required_value' => 3,
          'key' => 'nb_objects'
        ]);
        Quest::create([
          'name' => 'Acheteur',
          'slug' => 'obj4',
          'description' => 'Acheter ou obtenir 4 objets',
          'required_value' => 4,
          'key' => 'nb_objects'
        ]);
        Quest::create([
          'name' => 'Collectionneur',
          'slug' => 'obj5',
          'description' => 'Acheter ou obtenir 5 objets',
          'required_value' => 5,
          'key' => 'nb_objects'
        ]);
        Quest::create([
          'name' => 'Polyvalent',
          'slug' => 'rel1',
          'description' => 'Finir 1 partie sur le phénomène',
          'required_value' => 1,
          'key' => 'game_rel'
        ]);
        Quest::create([
          'name' => 'Polyvakent',
          'slug' => 'rel2',
          'description' => 'Finir 2 partie sur le phénomène ',
          'required_value' => 2,
          'key' => 'game_rel'
        ]);
        Quest::create([
          'name' => 'Polyvakent',
          'slug' => 'rel3',
          'description' => 'Finir 3 partie sur le phénomène ',
          'required_value' => 3,
          'key' => 'game_rel',
          'rare' => 1
        ]);

    }
}
