<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Corpus;
use App\Models\Parser;
use Gwaps4nlp\Core\Models\Source;
use App\Models\Relation;
use DB;

class StatsParser extends Model
{
    
    protected $guarded = [];
    
    public $timestamps = false;

	public static function computeStats($corpus_evaluation, $date, $score_init=5, $weight_level=1, $weight_confidence_user=0){
		
        
        DB::statement('DELETE FROM stats_parsers where date = ? and score_init= ? and weight_level= ? and weight_confidence_user= ? ',[$date, $score_init, $weight_level, $weight_confidence_user]);
		
		self::updateStats('parsers',$corpus_evaluation, $date, $score_init, $weight_level, $weight_confidence_user);
		self::updateStats('union',$corpus_evaluation, $date, $score_init, $weight_level, $weight_confidence_user);
		self::updateStats('inter',$corpus_evaluation, $date, $score_init, $weight_level, $weight_confidence_user);
		self::updateStats('game',$corpus_evaluation, $date, $score_init, $weight_level, $weight_confidence_user);
		self::computeCovers($corpus_evaluation, $date);
	}

	public static function updateStats($type_stats,$corpus_evaluation, $date, $score_init=5, $weight_level=1, $weight_confidence_user=0){
		// $corpus_evaluation = $corpus_evaluated->evaluation_corpora->first();

		$corpus_evaluated = $corpus_evaluation->evaluated_corpus()->first();
		
		Annotation::computeScoreAtDate($corpus_evaluation, $date, $score_init, $weight_level, $weight_confidence_user);
		// $corpus_evaluation = $corpus_evaluated->evaluation_corpora()->first();
		// $corpus_control = $corpus_evaluation->getControlCorpus();

        $parser_ids = Annotation::select(DB::raw('DISTINCT parser_id'))->whereIn('corpus_id',array_merge([$corpus_evaluated->id],$corpus_evaluated->subcorpora->pluck('id')->toArray()))->join('annotation_parser','annotation_id','=','annotations.id')->lists('parser_id');

        $params = [$date, $score_init, $weight_level, $weight_confidence_user];

		$parser_ids = array_map('intval', $parser_ids->toArray());

		if($type_stats=='union')
	        $sqlStats = Annotation::select(DB::raw('count(*) as total_parser, 
					count(*) as correct, 
					annotations.relation_id, annotations.corpus_id as corpus_parser, annotations.corpus_id as corpus_control'))
			        ->where('annotations.corpus_id',$corpus_evaluation->id)
			        ->where('annotations.source_id',Source::getPreAnnotatedForEvaluation()->id)
		            ->whereExists(function ($query) use($corpus_evaluation) {
		                $query->select(DB::raw(1))
		                      ->from('annotations as annotations_parser')
		                      ->where('annotations_parser.source_id','=',Source::getPreAnnotated()->id)		                      
		                      ->whereRaw('annotations.sentence_id=annotations_parser.sentence_id')
		                      ->whereRaw('annotations.word_position=annotations_parser.word_position')
		                      ->whereRaw('annotations.governor_position=annotations_parser.governor_position')
		                      ->whereRaw('annotations.relation_id=annotations_parser.relation_id');
		            })
		        	->groupBy('annotations.relation_id');
		else
	        $sqlStats = Annotation::select(DB::raw('count(*) as total_parser, 
				sum(if(annotations.relation_id=annotations_control.relation_id and annotations.governor_position=annotations_control.governor_position,1,0)) as correct, 
				annotations.relation_id, annotations.corpus_id as corpus_parser, annotations_control.corpus_id as corpus_control'))
				->join('annotations as annotations_control', function($join) {
		            $join->on('annotations.sentence_id', '=', 'annotations_control.sentence_id')
		            	->on('annotations.word_position', '=', 'annotations_control.word_position');
		        })
		        ->where('annotations_control.corpus_id',$corpus_evaluation->id)
		        ->where('annotations_control.source_id',Source::getPreAnnotatedForEvaluation()->id)
	        	->groupBy('annotations.relation_id');

        if($type_stats == 'parsers'){
	        $sqlStats
	        	->addSelect('parser_id')
	        	->join('annotation_parser','annotations.id','=','annotation_parser.annotation_id')
	        	->where('annotations.source_id',Source::getPreAnnotated()->id)
	        	->groupBy('parser_id');
        }
        elseif($type_stats == 'game'){
	        $sqlStats->join('score_annotations','annotations.id','=','score_annotations.annotation_id')
	        	->where('score_annotations.best',1)
	        	->whereDate('score_annotations.date','=', $date);
        }
        elseif($type_stats == 'inter'){
	        $sqlStats->where('annotations.best',1)
	        	->where('annotations.playable',0)
	        	->where('annotations.source_id',Source::getPreAnnotated()->id);
        }

		//Cross join between table parsers and table relations
		if($type_stats == 'parsers')
			$sqlRelationParser = Parser::crossJoin('relations') 
				->select(DB::raw('parsers.id as parser_id,parsers.name as parser_name,relations.id as relation_id ,relations.slug as relation_name'))
	            ->whereIn('parsers.id',$parser_ids);
        else
			$sqlRelationParser = Relation::select(DB::raw("'$type_stats' as `parser_id`,'$type_stats' as `parser_name`,relations.id as relation_id ,relations.slug as relation_name"));

        $sqlStatsControl = Annotation::select(DB::raw('count(*) as total_control, relation_id, corpus_id'))
        	->where('source_id',Source::getPreAnnotatedForEvaluation()->id)
        	->where('corpus_id',$corpus_evaluation->id)
        	->groupBy('relation_id')
        	->groupBy('corpus_id');

        $request = DB::table(DB::raw("({$sqlRelationParser->toSql()}) as relation_parser"))
        	->mergeBindings($sqlRelationParser->getQuery())
			->join(DB::raw("({$sqlStatsControl->toSql()}) as stats_control"),'stats_control.relation_id','=','relation_parser.relation_id')
			->mergeBindings($sqlStatsControl->getQuery())
			->leftJoin(DB::raw("({$sqlStats->toSql()}) as stats"),function($join) use ($type_stats) {
				$join->on('stats.relation_id', '=', 'relation_parser.relation_id');
				if($type_stats=='parsers')
					$join->on('stats.parser_id', '=' ,'relation_parser.parser_id');
			})->mergeBindings($sqlStats->getQuery())
			->select(DB::raw("relation_parser.relation_id, relation_parser.relation_name, relation_parser.parser_id, relation_parser.parser_name, ifnull(total_parser,0) as total_parser, ifnull(correct,0) as correct, stats_control.total_control as total_control, 
		 		0.000 as `precision`, 0.000 as recall, 0.000 as fscore,
		 		corpus_parser, corpus_control"));

		foreach($request->get() as $row){
			$stats_parser = get_object_vars($row);
			$stats_parser['date'] = $date;
			$stats_parser['weight_level'] = $weight_level;
			$stats_parser['weight_confidence_user'] = $weight_confidence_user;
			$stats_parser['score_init'] = $score_init;
			$stats_parser['corpus_parser'] = $corpus_evaluated->id;
			$stats_parser['corpus_control'] = $corpus_evaluation->id;
			StatsParser::create($stats_parser);
		}
    	DB::update("update stats_parsers set `precision`= if(total_parser>0,correct/total_parser,0),recall=if(total_control>0,correct/total_control,0)");
    	DB::update("update stats_parsers set `fscore`= if(`precision`+recall>0,2*`precision`*recall/(`precision`+recall),0)");			


	}

	public static function computeCovers($corpus_evaluation, $date){
		$corpus_evaluation_id = $corpus_evaluation->id;
		DB::statement('DELETE FROM covers where date = ? and corpus_id = ? ',[$date,$corpus_evaluation_id]);
    	DB::insert('INSERT into covers 
    		SELECT  `name`, `level_id`, `slug`, `id`, `description`, `help_file`, `type`, 
			ifnull(annotations.total,0) AS total, ifnull(annotation_users.total,0) AS done, 
			ifnull(annotations.total,0)-ifnull(annotation_users.total,0) AS todo ,  ifnull(totaux.total,0) AS answers , ifnull(totaux.total,0)/ifnull(annotations.total,0) as answers_by_annot,
			? , ? 
			FROM `relations` 
			LEFT JOIN (
			    SELECT  `annotations`.`relation_id`, count(distinct annotations.sentence_id,if(type="trouverTete",annotations.word_position,annotations.governor_position)) AS total 
			    FROM `relations` INNER JOIN `annotations` ON `annotations`.`relation_id` = `relations`.`id` 
			    WHERE `annotations`.`source_id` = "3" AND `annotations`.`playable` = "1" 
				AND EXISTS (select 1 from annotations evaluation_annotations where evaluation_annotations.corpus_id=? and evaluation_annotations.sentence_id = annotations.sentence_id)
			    GROUP BY `annotations`.`relation_id`) 
			  AS annotations ON `annotations`.`relation_id` = `relations`.`id` 
			LEFT JOIN (
			    SELECT  `annotations`.`relation_id`, count(distinct annotations.sentence_id,if(type="trouverTete",annotations.word_position,annotations.governor_position)) AS total 
			    FROM `relations` INNER JOIN `annotations` on `annotations`.`relation_id` = `relations`.`id` 
			    INNER JOIN `annotation_users` on `annotation_users`.`annotation_id` = `annotations`.`id` and `annotation_users`.`user_id` != 0 and `annotation_users`.`created_at` < DATE_ADD(?,INTERVAL 1 DAY) 
			    WHERE `annotations`.`source_id` = "3" AND `annotations`.`playable` = "1" 
			    AND EXISTS (select 1 from annotations evaluation_annotations where evaluation_annotations.corpus_id=? and evaluation_annotations.sentence_id = annotations.sentence_id) 
			    GROUP BY `annotations`.`relation_id`) 
			  AS annotation_users on `annotation_users`.`relation_id` = `relations`.`id` 
			LEFT JOIN (
			    SELECT  `annotations`.`relation_id`, count(*) AS total 
			    FROM `relations` INNER JOIN `annotations` on `annotations`.`relation_id` = `relations`.`id` 
			    INNER JOIN `annotation_users` on `annotation_users`.`annotation_id` = `annotations`.`id` and `annotation_users`.`user_id` != 0 and `annotation_users`.`created_at` < DATE_ADD(?,INTERVAL 1 DAY) 
			    WHERE `annotations`.`source_id` = "3" AND `annotations`.`playable` = "1" 
			    AND EXISTS (select 1 from annotations evaluation_annotations where evaluation_annotations.corpus_id=? and evaluation_annotations.sentence_id = annotations.sentence_id) 
			    GROUP BY `annotations`.`relation_id`) 
			  AS totaux on `totaux`.`relation_id` = `relations`.`id` 
			having `total` > 0 order by `level_id` asc',[$date,$corpus_evaluation_id,$corpus_evaluation_id,$date,$corpus_evaluation_id,$date,$corpus_evaluation_id]);
	}

	public static function getStats($corpus_evaluation, $date, $score_init=5, $weight_level=1, $weight_confidence_user=0){
		return self::orderBy('relation_name')
			->join('relations','relations.id','=','stats_parsers.relation_id')
			->join('covers', function ($join){
				$join 	->on('covers.id','=','stats_parsers.relation_id')
						->on('covers.date','=','stats_parsers.date')
						->on('covers.corpus_id','=','stats_parsers.corpus_control');
			})
			->where('stats_parsers.date','=',$date)	
			->where('stats_parsers.corpus_control','=',$corpus_evaluation->id)
			->where('stats_parsers.score_init','=',$score_init)	
			->where('stats_parsers.weight_level','=',$weight_level)	
			->where('stats_parsers.weight_confidence_user','=',$weight_confidence_user)						
			->where('relations.level_id','<=',7)
			->orderBy('parser_id')->get();
	}

	public static function getStatsRelation($parser_id='best', $relation_id, $score_init=5, $weight_level=1, $weight_confidence_user=0){
		return self::join('relations','relations.id','=','stats_parsers.relation_id')
			->join('covers', function ($join){
				$join->on('covers.id','=','stats_parsers.relation_id')->on('covers.date','=','stats_parsers.date')->on('covers.corpus_id','=','stats_parsers.corpus_control');
			})
			->where('stats_parsers.parser_id','=',$parser_id)			
			->where('stats_parsers.relation_id','=',$relation_id)	
			->where('stats_parsers.score_init','=',$score_init)	
			->where('stats_parsers.weight_level','=',$weight_level)	
			->where('stats_parsers.weight_confidence_user','=',$weight_confidence_user)	
			->where('relations.level_id','<=',7)
			->orderBy('answers_by_annot')->get();
	}

	public static function getStatsTotal($corpus_evaluation, $date, $only_playable_relations=false, $except = array()){
		$query = self::selectRaw('sum(total_parser) total_parser,
				sum(correct) correct, 
				sum(total_control) total_control, 
				sum(total) total, 
				sum(done) done, 
				sum(answers) answers, 
				parser_id, 
				parser_name')
			->join('relations','relations.id','=','stats_parsers.relation_id')
			->join('covers', function ($join){
				$join->on('covers.id','=','stats_parsers.relation_id')->on('covers.date','=','stats_parsers.date')->on('covers.corpus_id','=','stats_parsers.corpus_control');
			})
			->where('stats_parsers.date','=',$date)
			->where('stats_parsers.corpus_control','=',$corpus_evaluation->id)
			->groupBy('parser_id')
			->orderBy('parser_id');
		if($only_playable_relations)
			$query->where('relations.level_id','<=',7);
		if($except)
			$query->whereNotIn('relations.slug',$except);
		$q = $query->get();
		return $q;
	}

	public static function getStatsRelationByRelation($corpus_id, $parser_id = 'best'){

		$sql = 'SELECT COUNT(*) as count, 
				annotations_control.relation_id control_relation_id, relations_control.slug as control_relation_name,
				annotations_parser.relation_id parser_relation_id,  relations_parser.slug as parser_relation_name
				FROM annotations as annotations_control 
			INNER JOIN relations relations_control ON (relations_control.id = annotations_control.relation_id)
			LEFT JOIN annotations as annotations_parser
			ON (annotations_control.sentence_id = annotations_parser.sentence_id AND annotations_control.word_position = annotations_parser.word_position)
			INNER JOIN relations relations_parser ON (relations_parser.id = annotations_parser.relation_id)';
		if($parser_id != 'best' && $parser_id != 'game')
			$sql .= ' INNER JOIN annotation_parser  
			ON (annotation_parser.annotation_id = annotations_parser.id)';
		$sql .= ' WHERE 1
			AND annotations_control.corpus_id = '.$corpus_id;
		if($parser_id != 'best' && $parser_id != 'game')
			$sql .= ' AND annotation_parser.parser_id = '.$parser_id;
		else
			$sql .= ' AND annotations_parser.best = 1';
		$sql .= ' GROUP BY annotations_control.relation_id, annotations_parser.relation_id
			ORDER BY control_relation_id, parser_relation_id';
		return DB::select($sql);
	}

	public static function getScores($corpus_evaluation){
		$corpus_evaluation_id = $corpus_evaluation->id;
		return DB::select('
				SELECT COUNT(*) as total, "game" as parser_id, 
				sum(if(annotations_control.governor_position=annotations_parser.governor_position,1,0))/count(*) as uas, 
				sum(if(annotations_control.governor_position=annotations_parser.governor_position and annotations_control.relation_id=annotations_parser.relation_id,1,0))/count(*) as las
				from annotations as annotations_control 
				inner join annotations as annotations_parser 
				on (annotations_control.sentence_id = annotations_parser.sentence_id and annotations_control.word_position = annotations_parser.word_position 
				and annotations_parser.best = 1 ) 
				inner join relations on (annotations_control.relation_id = relations.id and relations.level_id<=7 and relations.type in ("trouverDependant","trouverTete"))
				where 1
				and annotations_control.corpus_id = '.$corpus_evaluation_id.' 
			union
				select count(*) as total, annotation_parser.parser_id as parser_id, 
				sum(if(annotations_control.governor_position=annotations_parser.governor_position,1,0))/count(*) as uas,
				sum(if(annotations_control.governor_position=annotations_parser.governor_position and annotations_control.relation_id=annotations_parser.relation_id,1,0))/count(*) as las
				from annotations as annotations_control 
				inner join annotations as annotations_parser 
				on (annotations_control.sentence_id = annotations_parser.sentence_id and annotations_control.word_position = annotations_parser.word_position) 
				inner join annotation_parser on annotation_parser.annotation_id = annotations_parser.id 
				inner join relations on (annotations_control.relation_id = relations.id and relations.level_id<=7 and relations.type in ("trouverDependant","trouverTete"))
				where 1
				and annotations_control.corpus_id = '.$corpus_evaluation_id.'  

				group by annotation_parser.parser_id
			union
				select count(*) as total, "inter" as parser_id, 
				sum(if(annotations_control.governor_position=annotations_parser.governor_position,1,0))/count(*) as uas,
				sum(if(annotations_control.governor_position=annotations_parser.governor_position and annotations_control.relation_id=annotations_parser.relation_id,1,0))/count(*) as las
				from annotations as annotations_control 
				left join annotations as annotations_parser 
				on (annotations_control.sentence_id = annotations_parser.sentence_id and annotations_control.word_position = annotations_parser.word_position

				and annotations_parser.best = 1
				and annotations_parser.playable = 0) 
				inner join relations on (annotations_control.relation_id = relations.id and relations.level_id<=7 and relations.type in ("trouverDependant","trouverTete"))
				where 1
				and annotations_control.corpus_id = '.$corpus_evaluation_id.'  
			union
				select count(*) as total, "union" as parser_id, 
				sum(if(annotations_control.governor_position=annotations_parser.governor_position,1,0))/count(*) as uas,
				sum(if(annotations_control.governor_position=annotations_parser.governor_position and annotations_control.relation_id=annotations_parser.relation_id,1,0))/count(*) as las
				from annotations as annotations_control 
				left join annotations as annotations_parser 
				on (annotations_control.sentence_id = annotations_parser.sentence_id and annotations_control.word_position = annotations_parser.word_position

				and annotations_parser.best = 1
				and annotations_parser.playable = 0) 
				inner join relations on (annotations_control.relation_id = relations.id and relations.level_id<=7 and relations.type in ("trouverDependant","trouverTete"))
				where 1
				and annotations_control.corpus_id = '.$corpus_evaluation_id.'  
			');
	}

	public static function getRateCorrectAnswers($relation_type,$corpus_id=16){
		if ($relation_type=="trouverDependant")
			return DB::select('SELECT sum(total) as total, sum(correct) as correct, sum(wrong) as wrong 
				from (select sum(1) total, sum(if(ae.word_position=au.word_position,1,0)) as correct,sum(if(ae.word_position!=au.word_position,1,0)) as wrong, a.*  
					from annotations ae, annotations a, relations r, annotation_users au 
					where 1
					and ae.corpus_id=41
					and a.sentence_id = ae.sentence_id
					and ae.governor_position = a.governor_position
					and ae.relation_id = a.relation_id
					and ae.relation_id = r.id and r.type="trouverDependant"
					and a.playable = 1
					and a.id = au.annotation_id
					and au.user_id !=0 
				group by a.id) stats')[0];
		else
			return DB::select('SELECT sum(total) as total, sum(correct) as correct, sum(wrong) as wrong  
				from (
					select sum(1) total, sum(if(ae.governor_position=au.governor_position,1,0)) as correct,sum(if(ae.governor_position!=au.governor_position,1,0)) as wrong, a.*  
					from annotations ae, annotations a, relations r, annotation_users au 
					where 1
					and ae.corpus_id=41
					and a.sentence_id = ae.sentence_id
					and ae.word_position = a.word_position
					and ae.relation_id = a.relation_id
					and ae.relation_id = r.id and r.type="trouverTete"
					and a.playable = 1
					and a.id = au.annotation_id
					and au.user_id !=0 
					group by a.id
				) stats')[0];			
	}




	public static function getRateCorrectAnswersIncorrectRelation($relation_type,$corpus_id=16){
		if ($relation_type=="trouverDependant")
			return DB::select('SELECT sum(total) as total, sum(correct) as correct, sum(wrong) as wrong
				FROM (
				select sum(1) total, sum(if(au.word_position=99999,1,0)) as correct,sum(if(au.word_position!=99999,1,0)) as wrong, au.* 
					from annotations a, relations r, annotation_users au 
				where 1 

					and a.relation_id = r.id and r.type="trouverDependant"
					and a.playable = 1
					and a.id = au.annotation_id
					and au.user_id !=0 
					and a.sentence_id in (
						select sentence_id from annotations ae 
						where ae.corpus_id=41
					)
					and not exists (
						select 1 from annotations ae 
							where ae.corpus_id=41 
							and a.sentence_id = ae.sentence_id 
							and ae.governor_position = a.governor_position
							and ae.relation_id = a.relation_id
						)
					group by a.id
				) stats')[0];
		else
			return DB::select('SELECT sum(total) as total, sum(correct) as correct, sum(wrong) as wrong 
				FROM (
				select sum(1) total, sum(if(au.governor_position=99999,1,0)) as correct,sum(if(au.governor_position!=99999,1,0)) as wrong, au.* 
					from annotations a, relations r, annotation_users au 
				where 1 

					and a.relation_id = r.id and r.type="trouverTete"
					and a.playable = 1
					and a.id = au.annotation_id
					and au.user_id !=0 
					and a.sentence_id in (
						select sentence_id from annotations ae 
						where ae.corpus_id=41
					)
					and not exists (
						select 1 from annotations ae 
							where ae.corpus_id=41 
							and a.sentence_id = ae.sentence_id 
							and ae.word_position = a.word_position
							and ae.relation_id = a.relation_id
						)
					group by a.id
				) stats')[0];	
	}

	public static function getDiffByPos($corpus_id=14, $parser1_id = 1, $parser2_id = null){
    
			$result = Annotation::join('cat_pos as cat_pos1','cat_pos1.id','=','annotations.pos_id')
			->join("annotations as a2", function($join) {
                $join->on("annotations.sentence_id", "=", "a2.sentence_id")->on("annotations.word_position", "=", "a2.word_position");
            })
            ->join('cat_pos as cat_pos2','cat_pos2.id','=','a2.pos_id')
            ->select(DB::raw('COUNT(*) as count'), 'annotations.pos_id as pos1_id','a2.pos_id as pos2_id', 'cat_pos1.slug as pos1_slug','cat_pos2.slug as pos2_slug')
            ->groupBy('annotations.pos_id', 'a2.pos_id');
            
            if($parser1_id=='ref'){
            	$result->where('annotations.source_id',1);
            } else {
	            $result->join('annotation_parser as ap1','ap1.annotation_id','=','annotations.id')
	            		->where('annotations.source_id','!=',2)
	            		->where('ap1.parser_id',$parser1_id);
            }

            if($parser2_id=='ref'){
            	$result->where('a2.source_id',1);
            } else {
	            $result->join('annotation_parser as ap2','ap2.annotation_id','=','a2.id')
	            		->where('a2.source_id','!=',2)
	            		->where('ap2.parser_id',$parser2_id);
            }
            return $result->get();
    }

	public static function getDiffByCat($corpus_id=14, $parser1_id = 1, $parser2_id = 2){
    
			$result = Annotation::join('cat_pos as cat_pos1','cat_pos1.id','=','annotations.category_id')
			->join("annotations as a2", function($join) {
                $join->on("annotations.sentence_id", "=", "a2.sentence_id")->on("annotations.word_position", "=", "a2.word_position");
            })
            ->join('cat_pos as cat_pos2','cat_pos2.id','=','a2.category_id')
            ->select(DB::raw('COUNT(*) as count'), 'annotations.category_id as pos1_id','a2.category_id as pos2_id', 'cat_pos1.slug as pos1_slug','cat_pos2.slug as pos2_slug')
            ->groupBy('annotations.category_id', 'a2.category_id');
            
            if($parser1_id=='ref'){
            	$result->where('annotations.source_id',1);
            } else {
	            $result->join('annotation_parser as ap1','ap1.annotation_id','=','annotations.id')
	            		->where('annotations.source_id','!=',2)
	            		->where('ap1.parser_id',$parser1_id);
            }

            if($parser2_id=='ref'){
            	$result->where('a2.source_id',1);
            } else {
	            $result->join('annotation_parser as ap2','ap2.annotation_id','=','a2.id')
	            		->where('a2.source_id','!=',2)
	            		->where('ap2.parser_id',$parser2_id);
            }
            return $result->get();

    }
	public static function getDiffByRelation(Corpus $corpus, $parser1_id = 1, $parser2_id = 2){

			$corpora_ids = array_merge([$corpus->id],$corpus->subcorpora->pluck('id')->toArray());
		
			$result = Annotation::join('relations as relations1','relations1.id','=','annotations.relation_id')
			->join("annotations as a2", function($join) {
                $join->on("annotations.sentence_id", "=", "a2.sentence_id")->on("annotations.word_position", "=", "a2.word_position");
            })
            ->join('relations as relations2','relations2.id','=','a2.relation_id')
            ->select(DB::raw('COUNT(*) as count'), 'annotations.relation_id as relation1_id','a2.relation_id as relation2_id', 'relations1.slug as relation1_slug','relations2.slug as relation2_slug')
            ->whereIn('annotations.corpus_id', $corpora_ids)
            ->groupBy('annotations.relation_id', 'a2.relation_id');
            
            if($parser1_id=='ref'){
            	$result->where('annotations.source_id',1);
            } elseif($parser1_id=='game') {
	            $result->join('annotation_parser as ap1','ap1.annotation_id','=','annotations.id')
	            		->where('annotations.best','=',1);
            } else {
	            $result->join('annotation_parser as ap1','ap1.annotation_id','=','annotations.id')
	            		->where('annotations.source_id','!=',2)
	            		->where('ap1.parser_id',$parser1_id);
            }

            if($parser2_id=='ref'){
            	$result->where('a2.source_id',1);
            } elseif($parser2_id=='game') {
	            $result->join('annotation_parser as ap2','ap2.annotation_id','=','a2.id')
	            		->where('a2.best','=',1);
            } else {
	            $result->join('annotation_parser as ap2','ap2.annotation_id','=','a2.id')
	            		->where('a2.source_id','!=',2)
	            		->where('ap2.parser_id',$parser2_id);
            }
            return $result->get();

    }
 
}
