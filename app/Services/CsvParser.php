<?php  namespace App\Services;

/**
 * Classe pour gérer les traitements en csv
 */
abstract class CsvParser {

	/**
	* Liste des colonnes du fichier csv
	*/
	protected $_columns = array();

	/**
	* Est-ce que l'on doit ignorer la première ligne (si elle contient le nom des colonnes par exemple)
	*/
	protected $_ignoreFirstLine = true;

	/**
	* Le séparateur pour les colonnes ';' par défaut, mais ça peut être ','
	*/
	protected $_separator = "\t";

	public $lines_done = 0;
	public $number_of_lines = 0;

	protected $_filename;
  
  public function __construct($filename){
  	$this->_filename = $filename;
  } 
  

  public function parse() {
    $this->preParse();

    
    $this->lines_done = 0;    
    if($this->_ignoreFirstLine) {
      fgets($f,1024);
      $this->lines_done++;
    }


    while($line = fgets($this->file,1024)) {
      $line = $this->clean($line);
      $line = explode($this->_separator,$line);
	    $this->lines_done++;
      $this->checkNumberColumns($line);
      if(count($line) != count($this->_columns)){

        $this->parseBlankLine($line);
        continue;
      }
	  
      $line = array_combine($this->_columns,$line);

      array_walk($line,array($this,'clean'));


      $this->parseLine($line);
    }

    $this->postParse();
  }

  /**
   * Méthode qui est executer pour chaque ligne du fichier csv
   * @param $line stdClass La ligne a traiter
   * @param $i Le numéro de la ligne
   */
  // abstract public function parseLine(\stdClass $line);
  abstract public function parseLine($line);

  /**
   * Méthode qui est executer pour chaque ligne du fichier csv
   * @param $line stdClass La ligne a traiter
   * @param $i Le numéro de la ligne
   */
  public function parseBlankLine($line){

  }

  public function checkNumberColumns($line){

  }

  /**
   * Méthode executer avant que le fichier ne soit parser
   */
  public function preParse() {
    if(!file_exists($this->_filename)) {
      throw new \Exception('File not found: '.$this->_filename);
    }
    $this->file = fopen($this->_filename,'rb');
    if($this->file == null){
      throw new \Exception('Cound not open '.$this->_filename.' for read');
    }
    while(!feof($this->file)){
      $line = fgets($this->file);
      $this->number_of_lines++;
    }	
    rewind ( $this->file );
	// $this->number_of_lines = count($this->file);
  }

  /**
   * Méthode executer après que tous le fichier ai été parsé
   */
  public function postParse() {

  }

  /**
   * Méthode pour nettoyer le fichier csv
   */
  public function clean($value) {
    return trim(stripslashes($value));
  }
}

if(!function_exists('str_getcsv')){
  function str_getcsv($input, $delimiter = '\t', $enclosure = '', $escape = '\\'){
    $tmp = tmpfile();
    fwrite($tmp, $input);
    rewind($tmp);
    $data = fgetcsv($tmp, null, $delimiter, $enclosure);
    fclose($tmp);
    return $data;
  }
}