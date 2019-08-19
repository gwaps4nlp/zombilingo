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
            'description' => 'Il faut retrouver le sujet du mot surligné en vert.',
            'help_file' => 'ud_nsubj',
            'type' => 'trouverDependant',
            'level_id' => 1
        ]);

        Relation::create([
            'slug' => 'aux',
            'name' => 'Auxiliaire',
            'description' => 'Il faut retrouver l’auxiliaire du mot surligné en vert',
            'help_file' => 'ud_aux',
            'type' => 'trouverDependant',
            'level_id' => 1
        ]);

        Relation::create([
            'slug' => 'aux:pass',
            'name' => 'Auxiliaire à la voix passive',
            'description' => 'Il faut retrouver l’auxiliaire passif associé au verbe surligné en vert',
            'help_file' => 'ud_aux_pass',
            'type' => 'trouverDependant',
            'level_id' => 2
        ]);

        Relation::create([
            'slug' => 'obj',
            'name' => 'Complément direct',
            'description' => 'Il faut retrouver le complément direct du mot surligné en vert',
            'help_file' => 'ud_obj',
            'type' => 'trouverDependant',
            'level_id' => 2
        ]);
        Relation::create([
            'slug' => 'dislocated',
            'name' => 'Sujet/Objet préposé/postposé',
            'description' => 'Il faut retrouver l’élement qui apparaît en périphérie et reprend un élément déjà présent dans la phrase. On doit pouvoir enlever cet élément sans changer le sens de la phrase.',
            'help_file' => 'ud_dislocated',
            'type' => 'trouverDependant',
            'level_id' => 2
        ]);

        Relation::create([
            'slug' => 'acl:relcl',
            'name' => 'Modifieur de proposition relative',
            'description' => 'Il faut retrouver le nom, pronom, ou nom propre qui est modifié par la proposition relative commençant par : qui, que, quoi, dont, où',
            'help_file' => 'ud_acl_relcl',
            'type' => 'trouverTete',
            'level_id' => 10
        ]);


        Relation::create([
            'slug' => 'aux:pass',
            'name' => 'Auxiliaire à la voix passive',
            'description' => 'Il faut retrouver l’auxiliaire passif associé au verbe surligné en vert',
            'help_file' => 'ud_aux_pass',
            'type' => 'trouverDependant',
            'level_id' => 4
        ]);

        Relation::create([
            'slug' => 'conj:coord', # Spoken uses conj:coord instead of conj
            'name' => 'Conjonction',
            'description' => 'Il faut retrouver le mot coordonné avec le mot surligné en vert.',
            'help_file' => 'ud_conj_coord',
            'type' => 'trouverTete',
            'level_id' => 10
        ]);

        Relation::create([
            'slug' => 'conj', # added for GSD
            'name' => 'Conjonction',
            'description' => 'Il faut retrouver le mot coordonné avec le mot surligné en vert.',
            'help_file' => 'ud_conj_coord',
            'type' => 'trouverTete',
            'level_id' => 10
        ]);
        Relation::create([
            'slug' => 'cc',
            'name' => 'Conjonction de coordination',
            'description' => 'Il faut retrouver le mot qui suit la conjonction de coordination (et, ou, mais, c’est-à-dire, ni…)',
            'help_file' => 'ud_cc',
            'type' => 'trouverTete',
            'level_id' => 10
        ]);

        Relation::create([
            'slug' => 'not-exists',
            'name' => "Relation inexistante",
            'description' => '',
            'help_file' => '',
            'type' => 'nonJouable',
            'level_id' => 10
        ]);

        $other_relations = array(
          "acl",
          "advcl",
          "advcl:cleft",
          "advcl:periph",
          "advmod",
          "advmod:periph",
          "amod",
          "appos:conj",
          "appos:nmod",
          "aux:caus",
          "case",
          "ccomp",
          "compound",
          "conj:dicto",
          "cop",
          "csubj",
          "csubj:pass",
          "dep",
          "dep:iobj",
          "dep:obj",
          "det",
          "discourse",
          "expl",
          "fixed",
          "flat",
          "iobj",
          "mark",
          "nmod",
          "nsubj:caus",
          "nsubj:expl",
          "nsubj:pass",
          "nummod",
          "obl",
          "obl:comp",
          "obl:mod",
          "obl:periph",
          "orphan",
          "parataxis:discourse",
          "parataxis:insert",
          "parataxis:obj",
          "parataxis:parenth",
          "punct",
          "root",
          "vocative",
          "xcomp",
          # add for GSD
          "appos",
          "expl:pass",
          "flat:foreign",
          "flat:name",
          "goeswith",
          "iobj:agent",
          "obj:agent",
          "obl:agent",
          "obl:arg",
          "parataxis",
        );

        foreach ($other_relations as $relation) {
                  Relation::create([
                      'slug' => $relation,
                      'name' => $relation,
                      'description' => '',
                      'help_file' => '',
                      'type' => 'nonJouable',
                      'level_id' => 10
                  ]);
        }
        Relation::create([
            'slug' => 'UNK',
            'name' => 'Unknown  ',
            'description' => '',
            'help_file' => '',
            'type' => 'nonJouable',
            'level_id' => 10
        ]);

    }
}
