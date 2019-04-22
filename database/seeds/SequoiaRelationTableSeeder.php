<?php

use Illuminate\Database\Seeder;
use App\Models\Relation;

class SequoiaRelationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Relation::create([
            'slug' => 'mod',
            'name' => 'Modificateur',
            'description' => 'Trouve le mot modifié par le mot surligné&#8239;!',
            'help_file' => 'mod',
            'type' => 'trouverTete',
            'level_id' => 4
        ]);
        Relation::create([
            'slug' => 'root',
            'name' => 'Racine',
            'description' => '',
            'help_file' => '',
            'type' => 'nonJouable',
            'level_id' => 10
        ]);
        Relation::create([
            'slug' => 'suj',
            'name' => 'Sujet',
            'description' => 'Trouve le sujet du verbe indiqué&#8239;!',
            'help_file' => 'suj',
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
            'slug' => 'de_obj',
            'name' => 'Complément en ≪&#8239;de&#8239;≫',
            'description' => 'Trouve le complément (objet indirect introduit par "de") du mot surligné&#8239;!',
            'help_file' => 'de_obj',
            'type' => 'trouverDependant',
            'level_id' => 2
        ]);
        Relation::create([
            'slug' => 'a_obj',
            'name' => 'Complément en ≪&#8239;à&#8239;≫',
            'description' => 'Trouve le complément (objet indirect introduit par "à") du mot surligné&#8239;!',
            'help_file' => 'a_obj',
            'type' => 'trouverDependant',
            'level_id' => 2
        ]);
        Relation::create([
            'slug' => 'obj.p',
            'name' => '"Objet" de la préposition',
            'description' => 'Trouve la tête de ce qui est introduit par la préposition indiquée',
            'help_file' => 'obj_p',
            'type' => 'trouverDependant',
            'level_id' => 2
        ]);
        Relation::create([
            'slug' => 'ats',
            'name' => 'Attribut du sujet',
            'description' => 'Trouve l\'attribut du sujet du verbe surligné',
            'help_file' => 'ats',
            'type' => 'trouverDependant',
            'level_id' => 3
        ]);
        Relation::create([
            'slug' => 'ato',
            'name' => 'Attribut de l\'objet',
            'description' => 'Trouve l\'attribut du sujet du verbe surligné',
            'help_file' => 'ato',
            'type' => 'trouverDependant',
            'level_id' => 10
        ]);
        Relation::create([
            'slug' => 'aux.tps',
            'name' => 'Auxiliaire de temps',
            'description' => 'Trouve l\'auxiliaire de temps du verbe donné',
            'help_file' => 'aux_tps',
            'type' => 'trouverDependant',
            'level_id' => 2
        ]);
        Relation::create([
            'slug' => 'aux.pass',
            'name' => 'Auxiliaire passif',
            'description' => 'Trouve l\'auxiliaire de la construction passive du verbe donné',
            'help_file' => 'aux_pass',
            'type' => 'trouverDependant',
            'level_id' => 2
        ]);
        Relation::create([
            'slug' => 'aux.caus',
            'name' => 'Auxiliaire causatif',
            'description' => 'Trouve l\'auxiliaire causatif du verbe donné',
            'help_file' => 'aux_caus',
            'type' => 'trouverDependant',
            'level_id' => 1
        ]);
        Relation::create([
            'slug' => 'aff',
            'name' => 'Affixe',
            'description' => 'Trouve l\'affixe du verbe donné',
            'help_file' => 'aff',
            'type' => 'trouverDependant',
            'level_id' => 1
        ]);
        Relation::create([
            'slug' => 'mod.rel',
            'name' => 'Relatives',
            'description' => 'Trouve la tête de la proposition relative modifiant le nom (ou pronom) indiqué',
            'help_file' => 'mod_rel',
            'type' => 'trouverDependant',
            'level_id' => 4
        ]);
        Relation::create([
            'slug' => 'coord',
            'name' => 'Coordination à gauche',
            'description' => 'Trouve le premier conjoint de la coordination donnée',
            'help_file' => 'coord',
            'type' => 'trouverTete',
            'level_id' => 4
        ]);
        Relation::create([
            'slug' => 'arg',
            'name' => 'Arg',
            'description' => 'Débrouille-toi&#8239;!!!',
            'help_file' => 'arg',
            'type' => 'trouverDependant',
            'level_id' => 6
        ]);
        Relation::create([
            'slug' => 'dep.coord',
            'name' => 'Coordination à droite',
            'description' => 'Trouve le conjoint qui suit la coordination donnée',
            'help_file' => 'dep_coord',
            'type' => 'trouverDependant',
            'level_id' => 4
        ]);
        Relation::create([
            'slug' => 'det',
            'name' => 'Déterminant',
            'description' => 'Trouve le déterminant du nom indiqué',
            'help_file' => 'det',
            'type' => 'trouverDependant',
            'level_id' => 1
        ]);
        Relation::create([
            'slug' => 'ponct',
            'name' => 'Ponctuation',
            'description' => '',
            'help_file' => 'ponct',
            'type' => 'trouverDependant',
            'level_id' => 10
        ]);
        Relation::create([
            'slug' => 'dep',
            'name' => 'Dep',
            'description' => '',
            'help_file' => 'dep',
            'type' => 'trouverDependant',
            'level_id' => 10
        ]);
        Relation::create([
            'slug' => 'obj.cpl',
            'name' => '"Objet" du complémenteur',
            'description' => 'Trouve la tête de ce qui est introduit par le complémenteur indiqué',
            'help_file' => 'obj_cpl',
            'type' => 'trouverDependant',
            'level_id' => 4
        ]);
        Relation::create([
            'slug' => 'p_obj.agt',
            'name' => 'Pobj agt',
            'description' => 'Trouve la tête du complément d\'agent',
            'help_file' => 'p_obj_agt',
            'type' => 'trouverDependant',
            'level_id' => 10
        ]);
        Relation::create([
            'slug' => 'mod.cleft',
            'name' => 'Mod cleft',
            'description' => '',
            'help_file' => 'mod_cleft',
            'type' => 'trouverDependant',
            'level_id' => 10
        ]);
        Relation::create([
            'slug' => 'p_obj.o',
            'name' => 'Complément prépositionnel',
            'description' => 'Trouve la tête (une préposition ou exceptionellement un pronom) du complément indirect du verbe indiqué&#8239;!',
            'help_file' => 'p_obj_o',
            'type' => 'trouverDependant',
            'level_id' => 3
        ]);
        Relation::create([
            'slug' => 'aff.demsuj',
            'name' => 'Aff demsuj',
            'description' => 'Trouve la tête (une préposition ou exceptionellement un pronom) du complément indirect du verbe indiqué&#8239;!',
            'help_file' => 'aff_demsuj',
            'type' => 'trouverDependant',
            'level_id' => 10
        ]);
        Relation::create([
            'id'    => 27,
            'slug' => 'dis',
            'name' => 'Dis',
            'description' => '',
            'help_file' => 'dis',
            'type' => 'trouverDependant',
            'level_id' => 6
        ]);
        Relation::create([
            'slug' => 'mod.app',
            'name' => 'Mod app',
            'description' => '',
            'help_file' => 'mod_app',
            'type' => 'trouverDependant',
            'level_id' => 10
        ]);
        Relation::create([
            'slug' => 'mod.inc',
            'name' => 'Mod inc',
            'description' => '',
            'help_file' => 'mod_inc',
            'type' => 'trouverDependant',
            'level_id' => 10
        ]);
        Relation::create([
            'slug' => 'mod.voc',
            'name' => 'Mod voc',
            'description' => '',
            'help_file' => 'mod_voc',
            'type' => 'trouverDependant',
            'level_id' => 10
        ]);
        Relation::create([
            'slug' => '_',
            'name' => '',
            'description' => '',
            'help_file' => '',
            'type' => 'nonJouable',
            'level_id' => 10
        ]);
        Relation::create([
            'id' => 34,
            'slug' => 'UNK',
            'name' => '',
            'description' => '',
            'help_file' => '',
            'type' => 'nonJouable',
            'level_id' => 10
        ]);
        Relation::create([
            'slug' => 'aux.pass/aux.tps',
            'name' => 'Auxiliaire passif/Auxiliaire causatif',
            'description' => '',
            'help_file' => '',
            'type' => 'special',
            'level_id' => 3
        ]);
        Relation::create([
            'slug' => 'mod/p_obj.o',
            'name' => 'Modificateur/Complément prépositionnel',
            'description' => '',
            'help_file' => '',
            'type' => 'special',
            'level_id' => 4
        ]);
        Relation::create([
            'slug' => 'not-exists',
            'name' => "Relation inexistante",
            'description' => '',
            'help_file' => '',
            'type' => 'nonJouable',
            'level_id' => 10
        ]);
        Relation::create([
            'slug' => 'dep_cpd',
            'name' => "dep_cpd",
            'description' => '',
            'help_file' => '',
            'type' => 'nonJouable',
            'level_id' => 10
        ]);
    }
}
