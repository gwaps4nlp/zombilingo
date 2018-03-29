<?php namespace App\Services;


abstract class Parser  {

	private $binary;
    public $output_file;
	public $command;
	public $md5;
	public $version;
	public $sentence_filter;
	public $map_cat_pos;
	public $map_pos_cat;
	public $pos;

	public function __construct(){
		$this->map_cat_pos = Array(
			'A' => Array('ADJ','ADJWH'),
			'ADV' => Array('ADV','ADVWH'),
			'C' => Array('CC','CS'),
			'CL' => Array('CLS','CLR','CLO'),
			'D' => Array('DET','DETWH'),
			'ET' => Array('ET'),
			'I' => Array('I'),
			'N' => Array('NC','NPP'),
			'P' => Array('P'),
			'P+D' => Array('P+D'),
			'P+PRO' => Array('P+PRO'),
			'PONCT' => Array('PONCT'),
			'PREF' => Array('PREF'),
			'PRO' => Array('PRO','PROREL','PROWH'),
			'V' => Array('V','VPP','VINF','VS','VPR','VIMP'),
			);
		$this->map_pos_cat = array();
		$this->pos = array();
		foreach($this->map_cat_pos as $cat=>$poss){
			foreach($poss as $pos){
				$this->map_pos_cat[$pos]=$cat;
				$this->pos[]=$pos;
			}
		}	
	}


	/**
	 * 
	 * 
	 * @return void
	 */
	public function postParse()
	{

	}

    protected function addCommentaries($conll,$sentences,$text_id){
    	$sentences = preg_replace('/(\\n+)/',"\n",trim($sentences));
        $sentences = explode("\n",$sentences);
        $sentences_splitted = "";
        $index = 1;
        $index_sentence = 0;
        $array_sentences = [];
        foreach($sentences as $sentence){
        	$sentence = trim($sentence);
            if(!$sentence) continue;
            if(mb_strlen($sentence)>1){
                $array_sentences[$index_sentence]=[];
                $array_sentences[$index_sentence]['sentid']= $index."_".($index+mb_strlen($sentence));
                $array_sentences[$index_sentence]['sentence-text']= $sentence;
                $sentences_splitted .= $sentence."\n\n";
                $index = $index+mb_strlen($sentence)+1;
                $index_sentence++;
            }
        }
        $conll = preg_replace('/(\\n){3,}/',"\n\n",trim($conll));
        $lines = explode("\n",$conll);
        $index_sentence = 0;
        $result = "";
        $result.= "# sentid: ".$text_id."_".$array_sentences[$index_sentence]['sentid']."\n";
        $result.= "# sentence-text: ".$array_sentences[$index_sentence]['sentence-text']."\n";
        $result.= "# ".$this->version."\n";
        $done = array();
        foreach($lines as $line){
            if(mb_strlen(trim($line))>3){
                $result.= $line."\n";
            } else {
                if(!isset($done[$index_sentence+1])&&isset($array_sentences[$index_sentence+1])){
                    $index_sentence++;
                    $done[$index_sentence]=true;
                    $result.= "\n";
                    $result.= "# sentid: ".$text_id."_".$array_sentences[$index_sentence]['sentid']."\n";
                    $result.= "# sentence-text: ".$array_sentences[$index_sentence]['sentence-text']."\n";
                    $result.= "# ".$this->version."\n";
                }

            }
        }
        return $result;
    }
    
    protected function addSentIds($conll){

        $index = 1;
        $index_sentence = 0;

        $conll = preg_replace('/(\\n){3,}/',"\n\n",$conll);
        $lines = explode("\n",$conll);
        $index_sentence = 0;
        $result = "";
        $result.= "# sentid: ".$this->sentIds[$index_sentence]."\n";
        $result.= "# ".$this->version."\n";
        $done = array();
        foreach($lines as $line){
            if(mb_strlen($line)>3){
                $result.= $line."\n";
            } else {

                if(!isset($done[$index_sentence+1])&&isset($this->sentIds[$index_sentence+1])){
                    $index_sentence++;
                    $done[$index_sentence]=true;
                    $result.= "\n";
                    $result.= "# sentid: ".$this->sentIds[$index_sentence]."\n";
                    $result.= "# ".$this->version."\n";
                }

            }
        }
        return $result;
    }
}