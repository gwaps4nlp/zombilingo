<?php

use Illuminate\Database\Seeder;
use App\Models\Relation;

class UdRelationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Relation::create([
            'slug' => 'nsubj',
            'name' => 'Sujet',
            'description' => 'Il faut retrouver le nom qui est modifié par l’argument principal de l’énoncé.',
            'help_file' => 'nsubj',
            'type' => 'trouverDependant',
            'level_id' => 1
        ]);
        Relation::create([
            'slug' => 'obj',
            'name' => 'Complément direct',
            'description' => 'Trouve le complément (objet direct) du verbe indiqué&#8239;!',
            'help_file' => 'obj',
            'type' => 'trouverDependant',
            'level_id' => 3
        ]);
        Relation::create([
            'slug' => 'dislocated',
            'name' => 'Sujet/Objet préposé/postposé',
            'description' => 'Il faut le retrouver l’élément qui ajoute une information au référent dont il est question et qui apparaît comme superflu',
            'help_file' => 'dislocated',
            'type' => 'trouverDependant',
            'level_id' => 2
        ]);

        Relation::create([
            'slug' => 'acl:relcl',
            'name' => 'Modifieur de proposition relative',
            'description' => 'Le nom/pronom/nom propre est modifié par le verbe dans la proposition relative qui le suit. Il faut le retrouver.',
            'help_file' => 'acl_relcl',
            'type' => 'trouverTête',
            'level_id' => 10
        ]);

        Relation::create([
            'slug' => 'aux',
            'name' => 'Auxiliaire',
            'description' => 'Trouve l\'auxiliaire ##',
            'help_file' => 'aux',
            'type' => 'trouverDependant',
            'level_id' => 1
        ]);

        Relation::create([
            'slug' => 'aux:pass',
            'name' => 'Auxiliaire à la voix passive',
            'description' => 'Trouve l\'auxiliaire de la voix passive',
            'help_file' => 'aux_pass',
            'type' => 'trouverDependant',
            'level_id' => 4
        ]);

        Relation::create([
            'slug' => 'conj',
            'name' => 'Conjonction',
            'description' => 'Il faut le retrouver la tête de la relation, en recherchant le premier item lexical coordonné.',
            'help_file' => 'conj',
            'type' => 'trouverTête',
            'level_id' => 10
        ]);

        Relation::create([
            'slug' => 'cc',
            'name' => 'Conjonction de coordination',
            'description' => 'Il faut retrouver la tête de la relation de conjonction de coordination celle qui arrive après le marqueur de coordination (mais, ou, et, donc, or, ni, car)',
            'help_file' => 'cc',
            'type' => 'trouverTête',
            'level_id' => 10
        ]);
    }
}
