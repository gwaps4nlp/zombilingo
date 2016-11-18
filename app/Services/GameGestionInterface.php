<?php

namespace App\Services;

interface GameGestionInterface 
{	
	public function begin($id);

	public function jsonContent();

	public function jsonAnswer();
		
	public function end();
	
	public function processAnswer();
	
}
