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
	public function tokenize($text)
	{

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


        $this->command = 'export PATH=$PATH:'.$this->path.';'."cat $input_file | {$this->binary} -L > $output_file";
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

	public static function str_replace_nth($search, $replace, $subject, $nth)
	{
	    $found = preg_match_all('#'.preg_quote($search).'#', $subject, $matches, PREG_OFFSET_CAPTURE);
	    if (false !== $found && $found > $nth) {
	        return substr_replace($subject, $replace, $matches[0][$nth][1], strlen($search));
	    }
	    return $subject;
	}
	/**
	 * 
	 * 
	 * @param  
	 * @return void
	 */
	public function getVersion()
	{

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