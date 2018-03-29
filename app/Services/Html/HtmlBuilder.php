<?php namespace App\Services\Html;

use View;

class HtmlBuilder extends \Collective\Html\HtmlBuilder {

	
	/**
	 * 
	 *
	 * @return void
	 */
	public function imageNotRelationHere(){
		return parent::image('/img/osEnCroixSeuls.png','',['style'=>'width:65px;height:65px;']);
	}
	
	/**
	 * 
	 *
	 * @return void
	 */
	public function imageLevel($level_id){
		return parent::image('/img/level/level-'.$level_id.'.gif','');
	}
	
	/**
	 * 
	 *
	 * @return void
	 */
	public function modalObjectWon($object_id){
		return view('partial.object.object-won');
	}	
	/**
	 * 
	 *
	 * @return void
	 */
	public function openModal($idModal){
		return view('partial.object.object-won');
	}

	/**
	 * 
	 *
	 * @return string
	 */
	public function formatScore($value){
		if(app()->getLocale()=='fr')
			return number_format($value,0,',','&#8239;');
		return number_format($value,0,'.',',');
	}

	/**
	 * 
	 *
	 * @return string
	 */
	public function formatRank($rank){
		if($rank>0){
			$string = '<span style="color:green;">+'.$rank.'</span>';
		} elseif($rank<0){
			$string = '<span style="color:red;">'.$rank.'</span>';
		} else {
			$string = '<span>=</span>';
		}

		return $string;
	}

}
