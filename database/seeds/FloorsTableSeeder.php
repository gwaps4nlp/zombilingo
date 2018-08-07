<?php
use Illuminate\Database\Seeder;
use App\Models\Floor;


class FloorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Floor::create([
          'trophy_id'=>1,
          'score_to_reach'=>2,
          'floor'=>1,
          'image'=>'savant.jpg',
        ]);

          Floor::create([
          'trophy_id'=>1,
          'score_to_reach'=>5,
          'floor'=>2,
          'image'=>'savant.jpg',
        ]);

          Floor::create([
          'trophy_id'=>1,
          'score_to_reach'=>10,
          'floor'=>3,
          'image'=>'savant.jpg',
        ]);

          Floor::create([
          'trophy_id'=>1,
          'score_to_reach'=>15,
          'floor'=>4,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>1,
          'score_to_reach'=>20,
          'floor'=>5,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>1,
          'score_to_reach'=>30,
          'floor'=>6,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>2,
          'score_to_reach'=>1,
          'floor'=>1,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>2,
          'score_to_reach'=>5,
          'floor'=>2,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>2,
          'score_to_reach'=>10,
          'floor'=>3,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>2,
          'score_to_reach'=>25,
          'floor'=>4,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>2,
          'score_to_reach'=>50,
          'floor'=>5,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>2,
          'score_to_reach'=>100,
          'floor'=>6,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>3,
          'score_to_reach'=>1,
          'floor'=>1,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>3,
          'score_to_reach'=>3,
          'floor'=>2,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>3,
          'score_to_reach'=>5,
          'floor'=>3,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>3,
          'score_to_reach'=>10,
          'floor'=>4,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>3,
          'score_to_reach'=>15,
          'floor'=>5,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>3,
          'score_to_reach'=>30,
          'floor'=>6,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>4,
          'score_to_reach'=>1,
          'floor'=>1,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>4,
          'score_to_reach'=>3,
          'floor'=>2,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>4,
          'score_to_reach'=>5,
          'floor'=>3,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>4,
          'score_to_reach'=>10,
          'floor'=>4,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>4,
          'score_to_reach'=>15,
          'floor'=>5,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>4,
          'score_to_reach'=>30,
          'floor'=>6,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>5,
          'score_to_reach'=>10,
          'floor'=>1,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>5,
          'score_to_reach'=>50,
          'floor'=>2,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>5,
          'score_to_reach'=>100,
          'floor'=>3,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>5,
          'score_to_reach'=>200,
          'floor'=>4,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>5,
          'score_to_reach'=>500,
          'floor'=>5,
          'image'=>'savant.jpg',
        ]);

        Floor::create([
          'trophy_id'=>5,
          'score_to_reach'=>1000,
          'floor'=>6,
          'image'=>'savant.jpg',
        ]);
    }
}

