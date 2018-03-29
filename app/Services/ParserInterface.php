<?php

namespace App\Services;

use Illuminate\Http\Request;

interface ParserInterface 
{	
	public function splitSentences($text);

	public function parse($text,$text_id);

	public function parseFromConll($filename);
		
	public function tokenize($text);
	
	public function posTag($text);

	public function getVersion();
	
	public function preParse($filename,$temp_filename);

	public function postParse();
	
}