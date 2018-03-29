<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MiniGameController extends Controller
{
    public function __construct()
    {

    }
    /**
     * Display the game
     *
     * @return Illuminate\Http\Response
     */
    public function getOrigin()
    {
        $words = [
            ['word'=>'Chafouin','definition'=>'Rusé, sournois.','country_id'=>'fr'],
            ['word'=>'Champagné','definition'=>'Personne d’influence, aux nombreuses relations.','country_id'=>'cg'],
            ['word'=>'Dépanneur','definition'=>"Petit commerce, aux heures d'ouverture étendues, où l'on vend des aliments et une gamme d'articles de consommation courante.",'country_id'=>'ca-qc'],
            ['word'=>'Dracher','definition'=>'','country_id'=>'be'],
            ['word'=>'Ristrette','definition'=>'','country_id'=>'ch'],
            ['word'=>'Vigousse','definition'=>'','country_id'=>'ch'],
            ['word'=>'Tap-tap','definition'=>'','country_id'=>'ht'],
            ['word'=>'Fada','definition'=>'','country_id'=>'fr'],
            ['word'=>'Lumerotte','definition'=>'','country_id'=>'be'],
            ['word'=>'Poudrerie','definition'=>'','country_id'=>'ca-qc'],
        ];
        $countries = [
            'fr'=> ['name'=>'France','image'=>''],
            'cg'=> ['name'=>'Congo','image'=>''],
            'ca-qc'=> ['name'=>'Québec','image'=>''],
            'be'=> ['name'=>'Belgique','image'=>''],
            'ch'=> ['name'=>'Suisse','image'=>''],
            'ht'=> ['name'=>'Haïti','image'=>''],
        ];
        return view('front.minigame.origin',compact('words','countries'));
    }
    /**
     * Display the game
     *
     * @return Illuminate\Http\Response
     */
    public function getOriginProto()
    {
        $words = [
            ['id'=>1,'word'=>'Chafouin','definition'=>'Rusé, sournois.','country_id'=>'fr'],
            ['id'=>2,'word'=>'Champagné','definition'=>'Personne d’influence, aux nombreuses relations.','country_id'=>'cg'],
            ['id'=>3,'word'=>'Dépanneur','definition'=>"Petit commerce aux heures d'ouverture étendues.",'country_id'=>'ca-qc'],
            ['id'=>4,'word'=>'Dracher','definition'=>'Pleuvoir à verse.','country_id'=>'be'],
            ['id'=>5,'word'=>'Ristrette','definition'=>'Petit café très fort, fait à la vapeur au percolateur.','country_id'=>'ch'],
            ['id'=>6,'word'=>'Vigousse','definition'=>"Vif, plein de vie (d'une personne), fort, robuste, résistant (d'un animal, d'une plante)",'country_id'=>'ch'],
            ['id'=>7,'word'=>'Tap-tap','definition'=>'Camionnette servant au transport en commun.','country_id'=>'ht'],
            ['id'=>8,'word'=>'Fada','definition'=>'Un peu fou.','country_id'=>'fr'],
            ['id'=>9,'word'=>'Lumerotte','definition'=>'Source de lumière de faible intensité.','country_id'=>'be'],
            ['id'=>10,'word'=>'Poudrerie','definition'=>"Neige poussée par le vent pendant qu'elle tombe.",'country_id'=>'ca-qc'],
        ];
        $countries = [
            'fr'=> ['name'=>'France','image'=>''],
            'cg'=> ['name'=>'Congo','image'=>''],
            'ca-qc'=> ['name'=>'Québec','image'=>''],
            'be'=> ['name'=>'Belgique','image'=>''],
            'ch'=> ['name'=>'Suisse','image'=>''],
            'ht'=> ['name'=>'Haïti','image'=>''],
        ];
        return view('front.minigame.proto-borne',compact('words','countries'));
    }
    /**
     * Display the game "Definition"
     *
     * @return Illuminate\Http\Response
     */
    public function getDefinition()
    {
        $words = [
            ['id'=>1,'word'=>'Chafouin','definition'=>'Rusé, sournois.','country_id'=>'fr'],
            ['id'=>2,'word'=>'Champagné','definition'=>'Personne d’influence, aux nombreuses relations.','country_id'=>'cg'],
            ['id'=>3,'word'=>'Dépanneur','definition'=>"Petit commerce, aux heures d'ouverture étendues, où l'on vend des aliments et une gamme d'articles de consommation courante.",'country_id'=>'ca-qc'],
            ['id'=>4,'word'=>'Dracher','definition'=>'Pleuvoir à verse.','country_id'=>'be'],
            ['id'=>5,'word'=>'Ristrette','definition'=>'Petit café très fort, fait à la vapeur au percolateur.','country_id'=>'ch'],
            ['id'=>6,'word'=>'Vigousse','definition'=>"Vif, plein de vie (d'une personne), fort, robuste, résistant (d'un animal, d'une plante)",'country_id'=>'ch'],
            ['id'=>7,'word'=>'Tap-tap','definition'=>'Camionnette servant au transport en commun.','country_id'=>'ht'],
            ['id'=>8,'word'=>'Fada','definition'=>'Un peu fou.','country_id'=>'fr'],
            ['id'=>9,'word'=>'Lumerotte','definition'=>'Source de lumière de faible intensité.','country_id'=>'be'],
            ['id'=>10,'word'=>'Poudrerie','definition'=>"Neige poussée par le vent pendant qu'elle tombe.",'country_id'=>'ca-qc'],
        ];
        $countries = [
            'fr'=> ['name'=>'France','image'=>''],
            'cg'=> ['name'=>'Congo','image'=>''],
            'ca-qc'=> ['name'=>'Québec','image'=>''],
            'be'=> ['name'=>'Belgique','image'=>''],
            'ch'=> ['name'=>'Suisse','image'=>''],
            'ht'=> ['name'=>'Haïti','image'=>''],
        ];
        return view('front.minigame.definition',compact('words','countries'));
    }

}
