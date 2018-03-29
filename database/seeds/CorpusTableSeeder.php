<?php

use Illuminate\Database\Seeder;
use App\Models\Corpus;
use App\Models\User;
use App\Models\Role;
use App\Models\Source;
use App\Services\ConllParser;

class CorpusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $corpus = Corpus::create([
            'name' => 'Sequoia',
            'language_id' => 1,
            'playable' => 1,
            'source_id' => 1,
        ]);

        $role_admin = Role::where('slug','admin')->first();
        User::create([
            'username' => 'admin',
            'email' => 'admin@admin.fr',
            'password' => bcrypt('admin'),
            'remember_token' => str_random(10),
            'role_id' => $role_admin->id
        ]);

        $corpus_reference = Corpus::create([
            'name' => 'Sequoia',
            'language_id' => 1,
            'playable' => 1,
            'source_id' => 1,
        ]);
        // $file= base_path().'/database/seeds/csvs/sequoia.surf.conll';
        // $parser = new ConllParser($corpus_reference,$file);
        // $parser->parse();

        $source = Source::getPreAnnotated();
        $corpus_playable = Corpus::create([
            'name' => 'Test',
            'language_id' => 1,
            'playable' => 1,
            'source_id' => $source->id,
        ]);
        $file= base_path().'/database/seeds/csvs/corpus-mini-talismane.conll';
        // $file= base_path().'/database/seeds/csvs/corpus-test-talismane.conll';
        $parser = new ConllParser($corpus_playable,$file);
        $parser->parse(); 
        $file= base_path().'/database/seeds/csvs/corpus-mini-grew.conll';
        // $file= base_path().'/database/seeds/csvs/corpus-test-grew.conll';
        $parser = new ConllParser($corpus_playable,$file);
        $parser->parse();        
    }

}
