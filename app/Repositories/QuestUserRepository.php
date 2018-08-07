<?php
namespace App\Repositories;

use App\Models\Quest;
use App\Models\QuestUser;
use App\Repositories\RelationRepository;
use Gwaps4nlp\Core\Repositories\BaseRepository;
use Gwaps4nlp\Core\Models\TrophyUser;
use Gwaps4nlp\Core\Repositories\TrophyUserRepository;
use DB;

class QuestUserRepository extends BaseRepository
{

  /**
   * Create a new QuestRepository instance.
   *
   * @param  App\Models\QuestUser $questuser
   * @return void
   */
  public function __construct(
    QuestUser $questuser)
  {
    $this->model = $questuser;
  }

  public function getLast($user){
    $last = DB::table('quest_users')
      -> wheredate('created_at', '=' ,date('Y-m-d'))
      -> where('user_id','=',$user->id)
      ->first();
    return $last;
  }

  public function getLastDayBefore($user){
    $veille = date('Y-m-d', strtotime("1 day ago" ));
    $last = DB::table('quest_users')
      -> wheredate('created_at', '=' ,$veille)
      -> where('user_id','=',$user->id)
      ->latest()
      ->limit(1)
      ->value('quest_finished');
    return $last;

  }

  public function determineQuest($user){
    $quest=mt_rand(1,20);
    return $quest;
  }

  public function getRequiredValue($user){
    $required = DB::table('quests')
            ->join('quest_users', 'quests.id', '=', 'quest_users.quest_id')
            ->where('quest_users.user_id','=',$user->id)
            ->orderBy('quest_users.created_at','desc')
            ->value('quests.required_value');
    return $required;

  }

  public function getQuestKey($user){
    $key = DB::table('quests')
        ->join('quest_users', 'quests.id', '=', 'quest_users.quest_id')
        ->where('quest_users.user_id','=',$user->id)
        ->orderBy('quest_users.created_at','desc')
        ->value('quests.key');
    if($key=='game_rel'){
      $key=$this->getRandomRel($user->level_id);
    }

    return $key;
  }

  public function getRandomRel($level_id){
    $query = DB::table('relations')
      ->orderBy(DB::raw('Rand()'))
      ->whereIn('type',['trouverTete','TrouverDependant']);
    $query = $query->where('level_id','<=',$level_id);
    return $query->value('slug');
  }

  public function getPhenoName($phenoslug){
    $query= DB::table('relations')
      ->where('slug','=',$phenoslug);
    return $query->value('name');
  }

  public function getPhenoId($phenoslug){
    $query= DB::table('relations')
      ->where('slug','=',$phenoslug);
    return $query->value('id');
  }



  public function giveQuest($user){
    QuestUser::create([
        'quest_id'=>$this->determineQuest($user),
        'user_id'=>$user->id,
        'score'=>0,
        'required_value'=>5,
        'quest_finished'=>False
    ]);
    DB::table('quest_users')
       ->where('user_id', '=', $user->id)
       -> wheredate('created_at', '=' ,date('Y-m-d'))
       ->latest()
       ->limit(1)
       ->update(['required_value'=>$this->getRequiredValue($user)]);
    DB::table('quest_users')
       ->where('user_id', '=', $user->id)
       -> wheredate('created_at', '=' ,date('Y-m-d'))
       ->latest()
       ->limit(1)
       ->update(['key'=>$this->getQuestKey($user)]);
    $this->updateConsecutiveTrophy($user);
    return TRUE;
  }



  public function notCreated($user){
    $last=$this->getLast($user);
    if (count($last)==0){
      return True;
    }
    else{
      return False;
    }
  }

  public function getQuestName($user){
    $questname = DB::table('quests')
      ->join('quest_users', 'quests.id', '=', 'quest_users.quest_id')
      ->where('quest_users.user_id','=',$user->id)
      -> wheredate('quest_users.created_at', '=' ,date('Y-m-d'))
      ->orderBy('quest_users.created_at','desc')
      ->limit(1)
      ->value('quests.name');
    return $questname;
  }

  public function getQuestDescription($user){
    $questdescription = DB::table('quests')
      ->join('quest_users', 'quests.id', '=', 'quest_users.quest_id')
      ->where('quest_users.user_id','=',$user->id)
      -> wheredate('quest_users.created_at', '=' ,date('Y-m-d'))
      ->orderBy('quest_users.created_at','desc')
      ->limit(1)
      ->value('quests.description');
    return $questdescription;
  }

  public function getQuestScore($user){
    $questscore = DB::table('quest_users')
      -> wheredate('created_at', '=' ,date('Y-m-d'))
      -> where('user_id','=',$user->id)
      ->latest()
      ->limit(1)
      ->value('score');
    return $questscore;
  }

  public function getQuestId($user){
    $questid = DB::table('quest_users')
      -> wheredate('created_at', '=' ,date('Y-m-d'))
      -> where('user_id','=',$user->id)
      ->latest()
      ->limit(1)
      ->value('id');
    return $questid;
  }

  public function getQuestSlug($user){
    $questslug = DB::table('quests')
      ->join('quest_users', 'quests.id', '=', 'quest_users.quest_id')
      ->where('quest_users.user_id','=',$user->id)
      -> wheredate('quest_users.created_at', '=' ,date('Y-m-d'))
      ->orderBy('quest_users.created_at','desc')
      ->limit(1)
      ->value('quests.slug');
    return $questslug;
  }

  public function isFinished($user){
    DB::table('quest_users')
      ->wheredate('created_at','=',date('Y-m-d'))
      ->where('user_id', '=', $user->id)
      ->latest()
      ->limit(1)
      ->update(['quest_finished' => True]);
    if($this->getQuestFinished($user)){
      $id=$this->getQuestId($user);
      $this->updateTrophy($user,2);
      if($id==5||$id==10||$id==15){
        $this->updateTrophy($user,3);
      }
    }  
  } 

  public function finishQuest($user){
    $score=$this->getQuestScore($user);
    $requiredvalue=$this->getRequiredValue($user);
    if($score==$requiredvalue){
      $this->isFinished($user);
    }
  }

  public function getQuestFinished($user){
    $questfinished=DB::table('quest_users')
      ->wheredate('created_at','=',date('Y-m-d'))
      ->where('user_id', '=', $user->id)
      ->latest()
      ->limit(1)
      ->value('quest_finished');
    return $questfinished;
  }

  public function updateTrophy($user,$trophyid){
    if($this->trophyCreated($user)){
      DB::table('trophy_user')
        ->where('user_id','=',$user->id)
        ->where('trophy_id','=',$trophyid)
        ->increment('score',1 );
      $nextfloor=$this->checkFloor($user,$trophyid);
      $score=DB::table('trophy_user')
        ->where('user_id','=',$user->id)
        ->where('trophy_id','=',$trophyid)
        ->value('score');
      if($score==$nextfloor){
        DB::table('trophy_user')
          ->where('user_id','=',$user->id)
          ->where('trophy_id','=',$trophyid)
          ->increment('actual_floor',1 );
        $this->floorMax($user,$trophyid);
      }
    }
  }

  public function updateConsecutiveTrophy($user){
    $daybefore=$this->getLastDayBefore($user);
    if(!$daybefore){
      $score=DB::table('trophy_user')
        ->where('user_id','=',$user->id)
        ->where('trophy_id','=',1)
        ->update(['score'=>0]);
    }
    else{
      $this->updateTrophy($user,1);
    }
  }

  public function floorMax($user,$trophyid){
        $actualfloor=DB::table('trophy_user')
          ->where('user_id','=',$user->id)
          ->where('trophy_id','=',$trophyid)
          ->value('actual_floor');
        $maxfloor=DB::table('trophies')
          ->where('id','=',$trophyid)
          ->value('maximum_floor');
        if($actualfloor==$maxfloor){
          $nbrfloormax=DB::table('trophy_user')
            ->where('user_id','=',$user->id)
            ->where('trophy_id','=',$trophyid)
            ->value('number_maximum_floor');
          $score=DB::table('trophy_user')
            ->where('user_id','=',$user->id)
            ->where('trophy_id','=',$trophyid)
            ->update(['actual_floor'=>0,'score'=>0,'number_maximum_floor'=>$nbrfloormax+1]);
        }

  }

  public function checkFloor($user,$trophyid){
    $nextfloor=$this->getActualFloor($user,$trophyid);
    $nextfloor=$nextfloor+1;
    $floor=DB::table('floors')
      ->where('trophy_id','=',$trophyid)
      ->where('floor','=',$nextfloor)
      ->value('score_to_reach');
    return $floor;
  }

  public function createTrophy($user){
    for ($i=1; $i < 6; $i++) { 
      TrophyUser::create([
        'user_id'=>$user->id,
        'trophy_id'=>$i,
        'score'=>0,
        'actual_floor'=>0,
        'number_maximum_floor'=>6
      ]);
    }
  }

    public function getActualFloor($user,$trophyid){
      $actualfloor=DB::table('trophy_user')
        ->where('trophy_id','=',$trophyid)
        ->where('user_id','=',$user->id)
        ->value('actual_floor');
      return $actualfloor;
    }

    public function trophyCreated($user){
      $created=DB::table('trophy_user')
        ->where('user_id','=',$user->id)
        ->get();
      if (count($created)==0){
        $this->createTrophy($user);
      }
      return True;
    
    }

  public function updateScore($user){
    $score=$this->getQuestScore($user);
    $requiredvalue=$this->getRequiredValue($user);
    if(!($score==$requiredvalue)){
      DB::table('quest_users')
        ->wheredate('created_at','=',date('Y-m-d'))
        ->where('user_id', '=', $user->id)
        ->increment('score',1 );
      $this->finishQuest($user);
    }
  }

  public function updateWeekQuestTrophy($user){
    $scores = DB::table('score_weeks')->join('users',"score_weeks.user_id",'=','users.id')
      ->whereNull('users.deleted_at')
      ->groupBy('user_id')
      ->orderBy('score', 'desc')
      ->whereRaw("yearweek = YEARWEEK(NOW())")
      ->value('user_id');
    if(($scores==$user->id)&&(strpos($this->getQuestSlug($user),'week')!==FALSE)){
      $this->updateScore($user);
    }
    if(($scores==$user->id)&&($this->trophyNotUpdated($user))){
      $this->updateTrophy($user,4);
      }
  }

  public function trophyNotUpdated($user){
    $lastupdate=DB::table('trophy_user')
      ->where('trophy_id','=',4)
      ->where('user_id','=',$user->id)
      ->value('updated_at');
    return !(DATE($lastupdate)==DATE('Y-m-d'));  
  }

  public function getKey($user){
    $key = DB::table('quest_users')
      -> wheredate('created_at', '=' ,date('Y-m-d'))
      -> where('user_id','=',$user->id)
      ->latest()
      ->limit(1)
      ->value('key');
    return $key;
  }

  public function returnKey($user){
    $slug=$this->getQuestSlug($user);
    if(strpos($slug,'rel')!==FALSE){
      return $this->getPhenoName($this->getKey($user));
    }
    else{
      return ' ';
    }
  }
}
?>
