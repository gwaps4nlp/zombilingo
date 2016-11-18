<?php namespace App\Services;

use Storage, Config;

class Talismane extends Parser {

	private $language_pack;
	protected $sentIds;

	public function __construct($sentence_filter='all'){
		$this->binary = Config::get('parser.talismane.binary');
		$this->language_pack = Config::get('parser.talismane.language-pack');
		$this->version = Config::get('parser.talismane.version');
		$this->sentence_filter = $sentence_filter;
	}

	/**
	 * 
	 * 
	 * @param  
	 * @return void
	 */
	public function splitSentences($text)
	{
        
        $text = str_replace(array("\r\n","\r","\n"),array(" "," "," "),$text);
        $md5 = md5($text);

        $input_file = storage_path()."/app/$md5-raw.txt";
        $output_file = storage_path()."/app/$md5-splitted.txt";

        Storage::disk('local')->put("$md5-raw.txt", $text);
        $this->command = "java -Xmx1G -jar -Dconfig.file={$this->language_pack} {$this->binary} command=analyse module=sentenceDetector inFile=$input_file outFile=$output_file encoding=UTF-8";
        exec($this->command,$output,$retour);
    	$this->output_file = "$md5-splitted.txt";
        $sentences_splitted = Storage::disk('local')->get($this->output_file);
        return $sentences_splitted;
	}

	/**
	 * 
	 * 
	 * @param  
	 * @return void
	 */
	public function parse($text,$text_id)
	{
		$md5 = md5($text);
		$this->md5 = $md5;
		if(!Storage::disk('local')->has("$md5-splitted.txt"))
        	Storage::disk('local')->put("$md5-splitted.txt", $text);
        
        $input_file = storage_path()."/app/$md5-splitted.txt";
        $output_file = storage_path()."/app/$md5-tmp.conll";

        $this->command = "java -Xmx1G -jar -Dconfig.file={$this->language_pack} {$this->binary} command=analyse startModule=tokenise inFile=$input_file outFile=$output_file encoding=UTF-8";
    	exec($this->command,$output,$retour);
        $conll = Storage::disk('local')->get("$md5-tmp.conll");
        $conll = $this->addCommentaries($conll,$text,$text_id);
        Storage::disk('local')->put("$md5-tmp.conll",$conll);

        $this->output_file = "$md5-tmp.conll";
        $this->postParse();
        return Storage::disk('local')->get($this->output_file);
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
        $input_file = storage_path()."/app/$md5-tmp-reduce.txt";	
		$this->preParse($filename,$temp_file);
        $command = "cat $temp_file | cut -f1,2 | sed '/^#/d' > $input_file";
        exec($command);

        $output_file = storage_path()."/app/$md5-tmp-tal.conll";
        if(!Storage::disk('local')->has("$md5-tmp.conll")){
            $command = "java -Xmx1G -jar -Dconfig.file={$this->language_pack} {$this->binary} command=analyse startModule=postag inFile=$input_file outFile=$output_file encoding=UTF-8";

        	exec($command,$output,$retour);
	        $conll = Storage::disk('local')->get("$md5-tmp-tal.conll");
	        $conll = $this->addSentIds($conll);
	        Storage::disk('local')->put("$md5-tmp-tal.conll",$conll);
        }
        $this->output_file = "$md5-tmp-tal.conll";
        $this->postParse();
        return Storage::disk('local')->get($this->output_file);
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
	        $this->command = "java -Xmx1G -jar -Dconfig.file={$this->language_pack} {$this->binary} command=analyse module=tokenise inFile=$input_file outFile=$output_file encoding=UTF-8";
	        exec($this->command,$output,$retour);
    	}

        $result = Storage::disk('local')->get("$md5-tokenized.txt");
        $sentences="";
        foreach(explode("\n",$result) as $ligne){
            if($ligne)
                $sentences .= " ".str_replace(' ','_',explode(" ",$ligne,2)[1]);
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
        $this->output_file = storage_path()."/app/$md5-postag-talismane.txt";

        if(!Storage::disk('local')->has("$md5-postag-talismane.txt")){
	        $this->command = "java -Xmx1G -jar -Dconfig.file={$this->language_pack} {$this->binary} command=analyse startModule=tokenise endModule=postag inFile=$input_file outFile=$this->output_file encoding=UTF-8";
	        exec($this->command,$output,$retour);
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
	 * @param  
	 * @return void
	 */
	public function preParse($filename,$temp_filename)
	{
		$columns = array('word_position','word','lemma','category_id','pos_id','features','governor_position','relation_id','projective_governor_position','projective_relation_id');
		$this->sentIds = array();
	    $file = fopen($filename,'rb');
	    $fp = fopen($temp_filename, 'w');
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
				if($addToFile)
					fwrite($fp, $line);
			} else {
				fwrite($fp, "\n");
				$addToFile = false;
			}
	    }
	    
		fclose($fp);
	    fclose($file);
	}

	/**
	 * 
	 * 
	 * @return void
	 */
	public function postParse()
	{
		$script_sed = base_path('corpora/tools')."/tal2seq.sed";
		$input_file = storage_path()."/app/".$this->output_file;
		$output_file = storage_path()."/app/".$this->md5."-talismane.conll";
		$command = "sed -f $script_sed {$input_file} > {$output_file}";
		$this->output_file = $this->md5."-talismane.conll";
		exec($command,$output,$retour);
	}

}