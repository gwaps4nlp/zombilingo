<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Repositories\AnnotationUserRepository;
use App\Repositories\AnnotationRepository;
use App\Repositories\RelationRepository;
use App\Repositories\LevelRepository;
use App\Repositories\CorpusRepository;
use App\Models\Discussion;
use App\Models\Annotation;
use App\Models\Number;
use App\Models\Locution;
use App\Models\User;
use DB;

class TestController extends Controller
{
	/**
     * Create a new admin controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('admin');
    }
    
     /**
     * Display a listing of the connected users.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTestFactory()
    {
        $user = factory(User::class)->create();
        var_dump($user);
    }  
     /**
     * Display a listing of the connected users.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTestNextLevel()
    {
        $expression = "')' ; drop table 'dela';﻿ - - ";
        $expression2 = "')' ; drop table 'dela';﻿ - - ";
        // $expression = "Action catholique";
        // $expression2 = "Action catholique";
        // $expression = '"'.$expression.'"';
        // $expression2 = '"'.$expression2.'"';
        $expression3 = "";
// $results = DB::select( DB::raw("SELECT * FROM some_table WHERE some_col = :somevariable"), array(
//    'somevariable' => $someVariable,
//  ));  
        $query = 'SELECT expression as expression, length(expression), lemma FROM `dela-fr` 
            WHERE 
            `expression` LIKE ?  
            AND MATCH (`expression`) AGAINST ( ? IN BOOLEAN MODE ) 
             ';
        $result = DB::select($query, array( $expression, $expression2));      
        // $result = DB::table('dela-fr')->selectRaw('words as expression, length(words), lemmas')
        //     ->whereRaw(DB::raw('MATCH (`words`) AGAINST ( ? IN BOOLEAN MODE ) '),[$expression])
        //     ->where('words','LIKE',$expression) 
        //     ->first();

// $my_query = "select *, MATCH (name) AGAINST (?) from users
//         where MATCH (hobbies) AGAINST (? IN BOOLEAN MODE) limit 10 OFFSET ?"
//     $hobbies = DB::select($my_query, array($search_term, $search_term, (($page-1)*10)));

        // $result = DB::table('dela-fr')->selectRaw('SELECT MATCH(`words`) AGAINST ( ? IN BOOLEAN MODE) as Relevance, words as expression, length(words), lemmas FROM `dela-fr` 
        //     WHERE 
        //     `words` like ? 
        //     AND MATCH (`words`) AGAINST ( ? IN BOOLEAN MODE ) 
        //      ', [$expression,$expression,$expression]);
        // $result = DB::select('select * from users where username = ? ',[$expression]);
        print_r($result);
        return 'test';        
        // return view('back.test.test-next-level');
    }     
    private function cleanText($text){
        $text=preg_replace('/\[([0-9]+|[a-zA-Z]\s[0-9]+|Note\s[0-9]+)\]/','',$text);
        $text=preg_replace('/(,)+\./','.',$text);
        $text=preg_replace('/(,)+/',',',$text);

        $tab = ['/’/','/—/','/«\xC2\xA0/','/\xC2\xA0»/','/« /','/ »/','/«/','/»/'];
        $tabr = ['\'','-','"','"','"','"','"','"'];
        $text=preg_replace($tab,$tabr,$text);
   
        $text=preg_replace('/\{\{(.+)\}\}/','',$text);
        return $text;
    }     
    private function getTest(){
        // $expression = 'c\'est-à -';
        $result = DB::select('SELECT MATCH(`words`) AGAINST ( :exp1 IN BOOLEAN MODE) as Relevance, words as expression, length(words), lemmas FROM `dela-fr` WHERE  MATCH (`words`) AGAINST ( :exp2 IN BOOLEAN MODE ) 
            AND `words` like :exp3 ', ['exp1'=>$expression,'exp2'=>$expression,'exp3'=>$expression]);
        return 'test';
    }
    private function extractLocutions($url){
        if(!preg_match('/http/',$url))
            $url = "https://fr.wiktionary.org".$url;
       $url = str_replace("'","%27",$url);
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $text = curl_exec ($ch);

        $dom = new \DomDocument();

        foreach(Range(0,3) as $attempt){
            try {
                $dom -> loadHTML($text);
                continue;
            } catch (\Exception $Ex) {
                $message = $Ex->getMessage();
                if(preg_match_all('/Tag ([a-z]+) invalid/',$message,$match,PREG_SET_ORDER)){
                    $tag = $match[0][1];
                    $text = preg_replace('#</?'.$tag.'[^>]*>#is', '', $text);
                }

            }
        }
        $content = $dom->getElementById('bodyContent');
        $locutions='';
        $finder = new \DomXPath($dom);
        $classname="mw-category-group";
        $nodes = $finder->query("//*[contains(@class, '$classname')]");
        // echo count($nodes);
        // $node =   (count($nodes)>0)? $nodes[0]:$content; 
        foreach($nodes as $node){
            foreach($node->getElementsByTagName('li') as $paragraph){
                $text = strip_tags($paragraph->textContent);
                $text = str_replace(['’'],["'"],$text);
                $locution = Locution::firstOrCreate(['expression'=>$text]);
                $locution->description = "Forme_Locution-phrase";
                $locution->save();
                $locutions.=strip_tags($paragraph->textContent)."<br/>";
            }
        }

        $locutions = $this->cleanText($locutions);
        echo $locutions;
    $links = $dom->getElementsByTagName('a');
     // return null;
    /*** loop over the links ***/
    foreach ($links as $tag)
    {
        if($tag->nodeValue=="page suivante")
            return $tag->getAttribute('href');
    }
        return null;
    }    
     /**
     * Display a listing of the connected users.
     *
     * @return \Illuminate\Http\Response
     */
    public function getImportLocutions()
    {
        $url = "https://fr.wiktionary.org/wiki/Cat%C3%A9gorie:Formes_de_locutions-phrases_en_fran%C3%A7ais";

        while($url_next_page = $this->extractLocutions($url)){
            if(!$url_next_page) break;
            $url = $url_next_page;
        }

    }    
     /**
     * Display a listing of the connected users.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTestProba()
    {
        $result=1;
        $i=0;
        // while($i++<=10000){
        while($i++<234){
            $number = Number::select('base10')->where('prime','=',1)->where('id','=',$i)->first();
            if($number){
                $result *= ($number->base10-1)/$number->base10;
            }
        }
        echo $result;
    }   
     /**
     * Display a listing of the connected users.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTestNumber()
    {
        $i=0;
        while($i++<1000000){
            $test = Number::max('id');
            $new = new Number;
            $next = $test+1;
            $new->id = $next;
            $new->base10 = $next;
            $diviseur = Number::select('base10')->where('prime','=',1)->where('base10','<=',$next/2)->whereRaw($next.' mod base10 = 0')->first();
            if($diviseur)
                $new->prime = 0;
            else {
                $new->prime = 1;
                //echo $new->id."<br/>";
            }
            $new->save();
        }
    }
    public function getTestFacto()
    {
        $P = 10670950881557;
        // 946593709 * 11273 
        //$P = 102587377889;
        //$P = 3094031;
        $found = false;
        $sqrt = sqrt($P);
        $min = ceil(sqrt($P));
        echo $min;
        // 473302491
        for($i=$min;$i<=946593709;$i++){
            $b_2 = pow($i,2)-$P;
            if($b_2<=0){
                echo "rien trouvé !!";
                break;
            }
            $b = sqrt($b_2);
            if($i==946593709) echo "hahah";


            //echo $i." => ".$b."<br/>";
            if(($b-floor($b))<=0 && ($b*$i == $P)){
                echo "$i et $b bingo !!<br/>";
                $found = true;
                break;
            }
        }
        if($found){
            $p1 = $i - $b;
            $p2 = $i + $b;
            echo "$P = $p1 x $p2";
        }
        // echo $min;


    }
    public function getTestNumber2()
    {
        $i=0;
        while($i++<1000000){
            $test = Number::max('id');
            $new = new Number;
            $next = $test+1;
            $new->id = $next;
            $new->base10 = $next;
            /*

            update numbers set base16 = CONV(base16,10,16);

            83211479297
            SELECT EXP(SUM(LN((base10-1)/base10))) AS proba FROM numbers where prime=1 ;
            SELECT EXP(SUM(LN((number-1)/number))) AS proba FROM numbers4 
            // 0.026314531626895277
            SELECT EXP(SUM(LN((number-1)/number))) AS proba FROM numbers4 
            
            where number < 10000000 ;

            select base10 from numbers where prime =1 and 997013421249292547 mod base10 = 0  and base10 < sqrt(997013421249292547) limit 1;
            
            select base10 from numbers where prime =1 and 186869456647 mod base10 = 0  and base10 < sqrt(186869456647) limit 1;

            select number from numbers4 where 997013421249292547 mod number = 0  and number < sqrt(997013421249292547) limit 1

            select number from numbers4 where 999999822000007597 mod number = 0  and number < sqrt(999999822000007597) limit 1

            select number from numbers4 where 961796711 mod number = 0  and number < 961796711/2 limit 1

            select std(np.base10-n1.base10),avg(np.base10-n1.base10),count(*) from  numbers np, numbers n1, numbers n2 
            where np.base10 > 2 
            and np.base10 = 150000  
            and np.base10 mod 2 = 0 
            and n1.prime=1 
            and n1.base10 < np.base10 
            and n2.base10 > np.base10 
            and n2.base10 < (2*np.base10 - 1)
            and n2.prime=1 
            and (n1.base10+n2.base10) = 2*np.base10

            select np.base10-n1.base10 from  numbers2 np, numbers2 n1,
            numbers2 n2 
            where np.base10 = 214747970  
            and np.base10 mod 2 = 0 
            and n1.prime=1 
            and n1.base10 < np.base10 
            and n2.base10 > np.base10 
            and n2.base10 < (2*np.base10 - 1)
            and n2.prime=1 
            and (n1.base10+n2.base10) = 2*np.base10

            SELECT * FROM `numbers` n1, numbers n2 where pow(n1.base10,2)-pow(n2.base10,2) = 102587377889 and n1.base10>=sqrt(102587377889) and n1.base10<=2*sqrt(102587377889) and n2.base10<=sqrt(102587377889) limit 0,1;

            select base10 from numbers where 482241311 mod base10 = 0  and base10 < sqrt(482241311) limit 1            

            insert ignore into numbers2 (id,prime)
            select np1.id*np2.id, 0 from numbers np1, numbers np2 
            where np1.id between 2 and 10000 and np1.prime = 1 
            and np2.id between 3 and 10000 and np2.prime = 1
            and np2.id >= np1.id 

            insert ignore into numbers2 (id,prime)
            select np1.id*np2.id, 0 from numbers np1, numbers np2 
            where np1.id > 2 and np1.prime = 1 
            and np2.id > 2 and np2.prime = 1
            and np2.id >= np1.id             

            432287 * 432281 = 186869456647

            update numbers set last2digits = base10 - 100*(base10 div 100);

insert into  prime_products (p1,p2,p1p2, last2digits)
SELECT t1.last2digits,t2.last2digits, t1.last2digits*t2.last2digits, 0 FROM `tmp_products` t1, `tmp_products` t2;
update prime_products set last2digits = p1p2 - 100*(p1p2 div 100);
            */

            $diviseur = Number::select('base10')->where('base10','!=',1)->whereRaw($next.' mod base10 = 0')->first();
            if($diviseur)
                $new->prime = 0;
            else {
                $new->prime = 1;
                //echo $new->id."<br/>";
            }
            $new->save();
        }
    }
    public function getTestNumber3()
    {
        $i=0;
        while($i++<1000000){
            $test = Number::max('id');
            $new = new Number;
            $next = $test+1;
            $new->id = $next;
            $new->base10 = $next;

            $diviseur = Number::select('base10')->where('base10','!=',1)->whereRaw($next.' mod base10 = 0')->first();
            if($diviseur)
                $new->prime = 0;
            else {
                $new->prime = 1;
                //echo $new->id."<br/>";
            }
            $new->save();
        }
    }

     /**
     * Display a listing of the connected users.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTestDiscussion(AnnotationRepository $annotations)
    {

        $annotation = $annotations->getById(513847);
        $discussion = $annotation->discussion;

        foreach($discussion->messages as $message){
            echo $message->user->id;
        }

        echo "||".$discussion->messages()->count()."|||||||";
        // return view('back.test.test-next-level');
    }
     /**
     * Display a listing of the connected users.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTestBetUpl()
    {

        return view('back.test.bet-upl');
    }
     /**
     * Display a listing of the connected users.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTable()
    {

        return view('back.test.table');
    }
     /**
     * Display a listing of the connected users.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAudio()
    {

        return view('back.test.audio');
    }

}
