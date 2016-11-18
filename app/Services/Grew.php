<?php namespace App\Services;

use Storage, Config;

class Grew extends Parser {

	private $grs_file;
	protected $sentIds;

	public function __construct($sentence_filter='all'){
		$this->binary = Config::get('parser.grew.binary');
		$this->grs_file = Config::get('parser.grew.grs-file');
		$this->version = Config::get('parser.grew.version');
		$this->sentence_filter = $sentence_filter;		
	}


	/**
	 * 
	 * 
	 * @param  
	 * @return void
	 */
	public function parse($text,$text_id)
	{
		
		$melt = new Melt();
		$melt->posTag($text);
		$this->commands['pos-tag'] = $melt->command;
		$this->files['pos-tag'] = $melt->output_file;
        
        $input_file = storage_path()."/app/{$melt->output_file}";
        $output_file = storage_path()."/app/{$melt->md5}-grew.conll";

    	$command = "{$this->binary} -det -grs {$this->grs_file} -seq full -i {$input_file} -f {$output_file}";
    	exec($command,$output,$retour);
    	$this->commands['parse'] = $command;
        $conll = Storage::disk('local')->get("{$melt->md5}-grew.conll");
        $conll = $this->addCommentaries($conll,$text,$text_id);
        Storage::disk('local')->put("{$melt->md5}-grew.conll",$conll);
        $this->output_file = "{$melt->md5}-grew.conll";
        $this->files['parse'] = $this->output_file;
        return Storage::disk('local')->get("{$melt->md5}-grew.conll");
	}

	/**
	 * 
	 * 
	 * @param  
	 * @return void
	 */
	public function parseFromConll($filename)
	{
		$md5 = md5($filename);
		$this->md5 = $md5;
        $temp_file = storage_path()."/app/$md5-tmp.txt";

		$text = $this->preParse($filename,$temp_file);

		$melt = new Melt();
		$melt->posTag(null,$temp_file);
        $input_file = storage_path()."/app/{$melt->output_file}";

        $output_file = storage_path()."/app/{$melt->md5}-grew.conll";

		if(!Storage::has("{$melt->md5}-grew.conll")){
        	$command = "{$this->binary} -det -grs {$this->grs_file} -seq full -i {$input_file} -f {$output_file}";
        	exec($command,$output,$retour);
	        $conll = Storage::disk('local')->get("{$melt->md5}-grew.conll");
	        $conll = $this->addSentIds($conll);
	        Storage::disk('local')->put("{$melt->md5}-grew.conll",$conll);
        }
        $this->output_file = "{$melt->md5}-grew.conll";
        return Storage::disk('local')->get("{$melt->md5}-grew.conll");
	}


	/**
	 * 
	 * 
	 * @param  
	 * @return void
	 */
	public function preParse($filename,$temp_filename)
	{
		$columns = array('word_position','word','lemma','category_id','pos_id','features','governor_position','relation_id','projective_governor_position','projective_relation_id');
		$this->sentIds = array();
	    $file = fopen($filename,'rb');
	    $fp = fopen($temp_filename, 'w');
	    $result="";
	    if($file == null){
	      throw new \Exception('Cound not open '.$filename.' for read');
	    }
	    $addToFile = false;
	    while(!feof($file)){
			$line = fgets($file);
			$line_splitted = explode("\t",$line);
			if(count($line_splitted)==10){
				$line_splitted = array_combine($columns,$line_splitted);
				if($line_splitted['word_position']==1){
					preg_match('/\|?sentid=(?<sentid>[^|]+)\|?/', $line_splitted['features'], $matches);
					if(isset($matches['sentid'])) {
						$id = substr($matches['sentid'],-5);
			            if($this->sentence_filter=='1mod4'){
			                if($id%4==1){
								$addToFile = true;
								$this->sentIds[] = $matches['sentid'];			                	
			                }
			            }
			            elseif($this->sentence_filter=='3mod4'){
			                if($id%4==3){
								$addToFile = true;
								$this->sentIds[] = $matches['sentid'];	
			                }
			            } else {
							$addToFile = true;
							$this->sentIds[] = $matches['sentid'];
						}
					}
				}
				if($addToFile){
					fwrite($fp, $line_splitted['word']." ");
					$result.=$line_splitted['word']." ";
				}
			} else {
				$result.=" \n";
				fwrite($fp, "\n");
				$addToFile = false;
			}
	    }
	    
		fclose($fp);
	    fclose($file);
	    return $result;
	}

	/**
	 * 
	 * 
	 * @param  
	 * @return void
	 */
	public function tokenize($text)
	{

        $md5 = md5($text);
        $file = Storage::disk('local')->put("$md5.txt", $text);
        
        $input_file = storage_path()."/app/$md5.txt";
        $output_file = storage_path()."/app/$md5-tokenized.txt";

        if(!Storage::disk('local')->has("$md5-tokenized.txt")){
	        $command = "java -Xmx1G -jar {$this->binary} languagePack={$this->language_pack} command=analyse module=tokenise inFile=$input_file outFile=$output_file";
	        exec($command,$output,$retour);
    	}

        $result = Storage::disk('local')->get("$md5-tokenized.txt");
        $sentences="";
        foreach(explode("\n",$result) as $ligne){
            if($ligne)
                $sentences .= " ".explode(" ",$ligne)[1];
            else        
                $sentences .="\n";
        }
        Storage::disk('local')->put("$md5-tokenized-inline.txt", $sentences);
        $this->output_file = "$md5-tokenized-inline.txt";
        return $result;
	}
	/**
	 * 
	 * 
	 * @param  
	 * @return void
	 */
	public function posTag($text)
	{

        $md5 = md5($text);
        Storage::disk('local')->put("$md5-splitted.txt", $text);
        
        $input_file = storage_path()."/app/$md5-splitted.txt";
        $output_file = storage_path()."/app/$md5-postag-talismane.txt";

        if(!Storage::disk('local')->has("$md5-postag-talismane.txt")){
	        $command = "java -Xmx1G -jar {$this->binary} languagePack={$this->language_pack} command=analyse startModule=tokenise endModule=postag inFile=$input_file outFile=$output_file";
	        exec($command,$output,$retour);
    	}

        $result = Storage::disk('local')->get("$md5-postag-talismane.txt");

        return $result;
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
	 * @return void
	 */
	public function postParse()
	{

	}

}