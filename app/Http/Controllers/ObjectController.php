<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Gwaps4nlp\Core\Models\ConstantGame;
use App\Repositories\ObjectRepository;
use App\Repositories\QuestUserRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Response, App, Auth;

class ObjectController extends Controller
{
	

    protected $game;

    /**
     * Create a new ObjectController instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {

        $this->middleware('auth');
        $this->middleware('ajax', ['except' => ['index']]);
        $this->game = App::make('Gwaps4nlp\Core\GameGestionInterface');

    }

    /**
     * Show the shop of objects
     *  
     * @return Illuminate\Http\Response
     */
    public function index(Request $request){
        $this->game->loadSession($request);
        $games_in_progress = $this->game->getInProgress();
        return view('front.shop.index',array('game'=>$this->game,'games_in_progress'=>$games_in_progress));
    }

    /**
     * Return the inventory of a player in JSON
     *  
     * @return Illuminate\Http\Response
     */
    public function inventaire($response=array()){
        return Response::json(array_merge($response,array('inventaire'=>$this->game->inventaire())));
    }

    /**
     * Check the help on a object as seen
     * 
     * @param int $id the identifier of the object to check 
     * @return void
     */    
    public function checkHelpAsSeen($id){
        Auth::user()->objects()->updateExistingPivot($id, ['help_seen'=>1]);
    }

    /**
     * Use an object
     * 
     * @param string $mode the mode of the current game 
     * @param int $id the identifier of the object used 
     * @return lluminate\Http\Response
     */    
    public function useObject(Request $request, $mode, $id){

        //Current effect
        $this->game->loadSession($request);
        $effet = $this->game->effect;

        $response = array();

        $object = Auth::user()->inventaire()->find($id);

        if(!$object){
            $response['message'] = trans('shop.error-unknown-object');
            return $this->inventaire($response);            
        }
		
        if(!$object->quantity){
            $response['message'] = trans('shop.error-not-in-inventory');
            return $this->inventaire($response);         
        }

        //If the object is glasses
        if($object->id == 3){
            //Si la phrase n'a pas disparue
            if(!$this->game->hasSpell('vanish')){
                //On réaffiche l'inventaire
                $response['message'] = trans('shop.error-sentence-not-disappeared');
                return $this->inventaire($response);
            }
            $this->game->cancelSpell();

            //On changera l'affichage sur le client
            $response['reappear_sentence'] = 1;
            $response['message'] = trans('shop.use-glasses');
        }elseif($object->id == 5){

            //Si la phrase n'a pas rapetissée
            if(!$this->game->hasSpell('shrink')){
                //On réaffiche l'inventaire
                 $response['message'] = trans('shop.error-sentence-not-shrinked');
                return $this->inventaire($response);
            }
            //On réindique que la phrase n'a pas rapetissée
            $this->game->cancelSpell();

            $response['increase_sentence'] = 1;
            $response['message'] = trans('shop.use-telescope');
        }elseif($object->id == 4){
            
            if ($effet != 0) {
                    
                //On regarde si l'extracteur n'est pas actif
                if($effet == 4){
                     $response['message'] = trans('shop.error-extractor-already-active');
                    return $this->inventaire($response);
                }

                if ($effet == 2 ) {
                    $response['message'] = trans('shop.error-midas-hand-already-active');
                    return $this->inventaire($response);
                }
            }else{
               
                $this->game->set('effect', $object->id);

            }

            $gain = $this->game->gain;

            $gain *= ConstantGame::get('multiplier-extractor');
            
            $this->game->set('gain',$gain);

            $response['gain'] = $gain;
            $response['message'] = trans('shop.won-more-points');
        }elseif($object->id == 2){

			//On regarde si la main de midas n'est pas active
            if($effet == 2){
                $response['message'] = trans('shop.error-midas-hand-already-active');
                return $this->inventaire($response);
            }

            if ($effet == 4 ) {
               $response['message'] = trans('shop.error-extractor-already-active');
                return $this->inventaire($response);
            }
            
            $this->game->set('effect', $object->id);
            
            $this->game->set('type_gain','money');
            $response['message'] = trans('shop.points-to-money');
            $response['midas'] = 1;
        }

        $this->game->user->objects()->updateExistingPivot($object->id, ['quantity'=>$object->quantity-1]);
    
		return $this->inventaire($response);
    }

    /**
     * Buy an object
     * 
     * @param int $id the identifier of the object bought 
     * @return lluminate\Http\Response
     */    
    public function buyObject(Request $request, $id){

        $this->game->loadSession($request);
        $response = array();

        $object = Auth::user()->inventaire()->find($id);
		
        if(!$object){
            $response['message'] = trans('shop.error-unknown-object');
            return $this->inventaire($response);            
        }
		if($this->game->isInProgress())
			$price = $object->price_ingame;
		else
			$price = $object->price;
		
        if($price > $this->game->user->money){
            $response['message'] = trans('shop.not-enough-money');
            return $this->inventaire($response);         
        }
		if($object->object_user_id)
			Auth::user()->objects()->updateExistingPivot($object->id, ['quantity'=>$object->quantity+1]);
		else
			Auth::user()->objects()->save($object, ['quantity'=>1]);
		
		$this->game->decrementMoney($price);
    $questuser= App::make('App\Repositories\QuestUserRepository');
    $questslug=$questuser->getQuestSlug(Auth::user());
    if(strpos($questslug,'obj')!==FALSE){
      $test=$questuser->updateScore(Auth::user());
    }

    
		return $this->inventaire(['money'=>Auth::user()->money,"spell"=>$this->game->get('spell')]);
		
    }

    /**
     * Won an object
     * 
     * @return lluminate\Http\Response
     */   
    public function objectWon(){
		
		$object_id = ObjectRepository::getRandomId();
	
		if(!session()->has('object_won') || !session()->get('object_won'))
			throw new NotFoundHttpException("");
		
		session()->put('object_won',0);
		
        $object = Auth::user()->inventaire()->find($object_id);
		
		if($object->object_user_id)
			Auth::user()->objects()->updateExistingPivot($object->id, ['quantity'=>$object->quantity+1]);
		else
			Auth::user()->objects()->save($object, ['quantity'=>1]);
    
		return Response::json($object);
		
    }
	
}
