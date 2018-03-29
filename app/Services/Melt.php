<?php namespace App\Services;

use Storage, Config;

class Melt extends Parser {

	private $path;

	public function __construct(){
		$this->binary = Config::get('parser.melt.binary');
		$this->path = Config::get('parser.melt.path');
		$this->version = Config::get('parser.melt.version');
		parent::__construct();
	}

	/**
	 * 
	 * 
	 * @param  
	 * @return void
	 */
	public function posTag($text,$filename=null)
	{

        $md5 = md5($text);
        $this->md5=$md5;
        if(!$filename){
        	$talismane = new Talismane();
	        $talismane->tokenize($text);
	        $input_file = storage_path()."/app/".$talismane->output_file;
    	} else {
    		$input_file = $filename;
    	}
        $output_file = storage_path()."/app/$md5-pos-melt.txt";


        $this->command = 'export PATH=$PATH:'.$this->path.';'."cat $input_file | {$this->binary} -Lr > $output_file";
        exec($this->command,$output,$retour);
        $result = Storage::disk('local')->get("$md5-pos-melt.txt");
        $sentences="";
        $patterns = array();
        $replacements = array();
        foreach($this->pos as $pos){
        	$patterns[]='#'.preg_quote("/$pos/").'#';
        	$replacements[]="\t$pos\t";
        }

        $result = preg_replace($patterns,$replacements,$result);
        foreach(explode("\n",$result) as $index_sentence=>$sentence){
            $index=1;
            if($sentence)  
            foreach(explode(" ",$sentence) as $word){

                list($word,$pos,$lemma) = explode("\t",$word);

                $sentences .= $index."\t".$word."\t".$lemma."\t_\t".$pos."\t_\t".($index-1)."\tSUC\t_\t_";
                $sentences .= "\n";
                $index++;
            }
            $sentences .= "\n";
        }
        $this->output_file = "$md5-pos-melt.conll";
        $sentences = preg_replace('/(\\n){3,}/',"\n\n",$sentences);
        Storage::disk('local')->put($this->output_file, $sentences);
        return $sentences;
	}

	public function splitSentences($text){
		throw new \Exception('Not implemented');
	}

	public function parse($text,$text_id){
		throw new \Exception('Not implemented');
	}

	public function parseFromConll($filename){
		throw new \Exception('Not implemented');
	}

	/**
	 * 
	 * 
	 * @param  
	 * @return void
	 */
	public function getVersion()
	{
        $this->command = 'export PATH=$PATH:'.$this->path."; {$this->binary} -v";
        exec($this->command,$output,$retour);
        return $output[0];
	}

	/**
	 * 
	 * 
	 * @param  
	 * @return void
	 */
	public function tokenize($text)
	{
		throw new \Exception('Not implemented');
	}

	/**
	 * 
	 * 
	 * @param  
	 * @return void
	 */
	public function preParse()
	{

	}

	/**
	 * 
	 * 
	 * @return void
	 */
	public function postParse()
	{

	}

}