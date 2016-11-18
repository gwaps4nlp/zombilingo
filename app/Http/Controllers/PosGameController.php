<?php

namespace App\Http\Controllers;

use App\Models\Annotation;
use DB;
use Log;
use Auth;
use Illuminate\Http\Request;
use App\Models\AnnotationPosUser;
use App\Models\CatPos;
use App\Models\PosGame;
use debug;
use view;

class PosGameController extends Controller {

    /**
     * Instantiate a new GameController instance.
     */
    public function __construct() {
        $this->middleware('auth');
    }

    public function show($id) {
        return view('front.posgame.index');
    }

    public function getIndex(Request $request) {
        $pos_game = PosGame::all()->random(1);
        /* get 5 sentence according to arbitrary couple of tags */
        $request->session()->put('pos_game_id', $pos_game->id);
        $annotations = $this->getAnnotationsByPosgame($pos_game);
        /* TODO : en fonction de si l'utilisateur a fait la formation ou pas */
        $request->session()->put('annotations', $annotations);

        /* get alternative postags */
        $catPosPosGames = DB::table("cat_pos as cp")
                ->join('cat_pos_pos_game', 'cat_pos_id', '=', 'cp.id')
                ->where("pos_game_id", $pos_game->id)->orderBy('slug', 'asc')
                ->get();

        /* renvoyer un booléen en plus dans la vue pour si l'utilisateur est en formation */
        return view('front.posgame.index', ['annotations' => $annotations, 'catPosPosGames' => $catPosPosGames, 'pos_game' => $pos_game]);
    }

    public function postIndex(Request $request) {
        $user_slug_answers = array();

        if ($request->ajax()) {
            $pos_game_id = $request->session()->get('pos_game_id');
            $pos_game = PosGame::find($pos_game_id);
            $annotations = $request->session()->get('annotations');
//            debug("display annotations randomly selected");
//            debug($annotations);
//            debug($request->input("answer"));
            $pos_1 = CatPos::find($pos_game->pos1_id);
            $pos_2 = CatPos::find($pos_game->pos2_id);
//            debug($request->all());
            foreach ($request->input("answer") as $annotation_id => $answer) {
                $annotation = Annotation::find($annotation_id);
                $range_to_pos_confidence = array(1 => "$pos_1->slug" . "_2", 2 => "$pos_1->slug" . "_1", 4 => "$pos_2->slug" . "_1", 5 => "$pos_2->slug" . "_2");

                if ($answer == "3") {
                    debug("CAS answer 3");
                    debug("unk_" . $annotation_id);
                    debug($request->input("unk_" . $annotation_id));
                    if ($request->input("unk_" . $annotation_id)){
                        debug("CAS UNK");
//                        $answer = $request->input("unk_" . $annotation_id);
                    } else {
                        debug("CAS OTHER");
                        debug($request->input("other_" . $annotation_id));
                        $answer = $request->input("other_" . $annotation_id);
                        $is_user_tag = 1;
                    }
                } else {
                    $answer = $range_to_pos_confidence[$answer];
                    $is_user_tag = 0;
                }

                /* transform range value to correct tag and confidence */
                list($pos, $confidence) = explode('_', $answer);

                $cat_pos = CatPos::getBySlug($pos);

                AnnotationPosUser::create(['user_id' => Auth::user()->id, 'sentence_id' => $annotation->sentence_id, 'word_position' => $annotation->word_position, 'pos_game_id' => $pos_game_id, 'cat_pos_id' => $cat_pos->id, 'is_user_tag' => $is_user_tag, 'confidence' => $confidence]);

                array_push($user_slug_answers, $pos);
            }

            return $this->display_stats($pos_game_id, $annotations, $request, $user_slug_answers);
        }
    }

    private function display_stats($pos_game_id, $annotations, $request, $user_slug_answers) {
        debug("in display stats");

        $pos_game = PosGame::find($pos_game_id);
        $stats_array = array();
        $categories_array = array();
        foreach ($annotations as $annotation) {
            debug($this->getUserPOSAnnotationByPosGame($pos_game_id, $annotation));
            array_push($stats_array, $this->getUserPOSAnnotationByPosGame($pos_game_id, $annotation));
        }
        /* foreach ($annotations as $annotation) {
          $slugs = $this->getPOSCatSlugsByPosGameAnnotation($pos_game_id, $annotation);
          $categories_string = '[\'';
          $i = 0;
          $len = count($slugs);
          foreach ($slugs as $key => $value) {
          if ($i != $len - 1) {
          $categories_string = $categories_string . $value->slug . '\',\'';
          } else {
          $categories_string = $categories_string . $value->slug . '\']';
          }
          $i += 1;
          }
          array_push($categories_array,$categories_string);
          debug($categories_string);
          }
         */
        $answers_index = array();

        $figures_array = array();
        foreach ($stats_array as $key => $value) {
            /* for each sentence */

            foreach ($stats_array[$key] as $subkey => $subvalue) {
                $array_sentence = array();

                $user_slug = $user_slug_answers[$key];
                $user_pos = CatPos::where('slug', $user_slug)->first();
                $complementary_id = $pos_game->getComplementaryPostag($user_pos->id);
                /* for each bar */
                /* cast y value for chart display */
                $y_value = $stats_array[$key][$subkey]->y;
                $stats_array[$key][$subkey]->y = (int) $y_value;
                /* set right color for user chosen tag */
                if ($user_slug == $stats_array[$key][$subkey]->name) {
                    array_push($answers_index, $subkey);

                    $array_sentence = array_fill(0, count($stats_array[$key]), "{y: 0, marker: {enabled: false}}");

                    $array_sentence[$subkey] = "{y: 13, marker: {symbol: 'url(img/level/thumbs/z4.png)'}}";

                    array_push($figures_array, $array_sentence);
                    $stats_array[$key][$subkey]->color = '#87bfff';
                } else {
                    $stats_array[$key][$subkey]->color = '#acfcd9';
                }
            }
        }
        $figures_array_json = json_encode($figures_array);
        $figures_array_json = preg_replace('/"/', '', $figures_array_json);
        $stats_array_json = json_encode($stats_array);
//        $categories_json = json_encode($categories_array);




//array_splice( $original, 3, 0, $inserted );
//        foreach ($answers_index as $value) {
//             array_splice($figures_array[$key], $subkey, 0, "y: 13, marker: {symbol: 'url(img/level/thumbs/z4.png)'}");
//        }
        return view('front.posgame.charts')->with('annotations', $annotations)->with('stats_array', $stats_array_json)->with('answers_index', $answers_index)->with('figures_array', $figures_array_json); //->with('categories', $categories_json);
    }

    public function getAnnotationsByPosgame($pos_game) {
        return Annotation::join("annotations as a2", function($join) {
                            $join->on("annotations.sentence_id", "=", "a2.sentence_id")->on("annotations.word_position", "=", "a2.word_position");
                        })
                        ->join("cat_pos as c1", "annotations.category_id", "=", "c1.id")
                        ->join("cat_pos as c2", "a2.category_id", "=", "c2.id")
                        ->where("annotations.category_id", "!=", "a2.category_id")
                        ->where("c1.slug", "=", $pos_game->pos1->slug)
                        ->where("c2.slug", "=", $pos_game->pos2->slug)->with('sentence')
//                        ->where("annotations.sentence_id", "=", "15293")
                        ->select("annotations.*", "c1.slug as pos1", "c2.slug as pos2")
                        ->orderBy(DB::raw('RAND()'))->distinct()->take(5)->get();
    }

    public function getUserPOSAnnotationByPosGame($pos_game_id, $annotation) {
        $total = AnnotationPosUser::select(DB::raw('count(*) as count'))
                ->join("cat_pos", "cat_pos_id", "=", "cat_pos.id")
                ->where("sentence_id", $annotation->sentence_id)
                ->where("word_position", $annotation->word_position)
                ->where("pos_game_id", $pos_game_id)
                ->first();
//        $confidence_1 = AnnotationPosUser::select(DB::raw('count(*) as total, slug as name, 100.0 * count(annotation_pos_users.cat_pos_id)/? as y')->where("annotation_pos_users.sentence_id", $annotation->sentence_id)
//                ->where("annotation_pos_users.word_position", $annotation->word_position)
//                ->where("annotation_pos_users.pos_game_id", $pos_game_id)
//                ->where("annotation_pos_users.confidence", "=", "2")
//                )->setBindings([$total['count']])
//                ->join("annotation_pos_users as a2", function($join) {
//                    $join->on("annotation_pos_users.sentence_id", "=", "a2.sentence_id");
//                    $join->on("annotation_pos_users.word_position", "=", "a2.word_position");
//                    $join->on("annotation_pos_users.pos_game_id", "=", "a2.pos_game_id");
//                })
//                ->join("cat_pos", "annotation_pos_users.cat_pos_id", "=", "cat_pos.id")
//
////                ->select(DB::raw('count(*) as total, slug as name, count(annotation_pos_users.cat_pos_id) as y1, count(a2.cat_pos_id) as y2 ')//,  ['?' => $total['count']]))
//                
////                ->where("a2.confidence", "=", "2")
//                ->groupBy("annotation_pos_users.cat_pos_id")
//                ->get();
//        debug("CONFIDENCE 1");
//        debug($confidence_1);

        return AnnotationPosUser::select(DB::raw('count(*) as total, slug as name, cast(100.0 * count(cat_pos_id)/? as unsigned) as y')
                        )->setBindings([$total['count']])
                        ->join("cat_pos", "cat_pos_id", "=", "cat_pos.id")
                        ->where("sentence_id", $annotation->sentence_id)
                        ->where("word_position", $annotation->word_position)
                        ->where("pos_game_id", $pos_game_id)
                        ->groupBy("cat_pos_id")->get()
        ;
    }

    public function getPOSCatSlugsByPosGameAnnotation($pos_game_id, $annotation) {
        return AnnotationPosUser::join("cat_pos", "cat_pos_id", "=", "cat_pos.id")
                        ->where("sentence_id", $annotation->sentence_id)
                        ->where("word_position", $annotation->word_position)
                        ->where("pos_game_id", $pos_game_id)
                        ->select("slug")
                        ->groupBy("cat_pos_id")->get()
        ;
    }

    public function postIndexTraining(Request $request) {
        /* compare answers with reference */
    }

}
