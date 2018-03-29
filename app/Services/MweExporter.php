<?php  namespace App\Services;

use App\Models\Mwe;
use App\Models\User;
use App\Models\ExportedCorpus;
use DB;

class MweExporter {

protected $_separator = ";";
protected $user;
public $url_file = null;
protected $lines = array();
public $nb_mwes;
public $error;
protected $file;

public function __construct(User $user){
    $this->nb_mwes = 0;
    $this->url_file = "";
	$this->user = $user;
}

public function export($file = null){

	try {

		if(!$file)
			$file = 'mwe-'.date('Ymd_His').'.csv';

		$this->file = fopen(storage_path('export/'.$file),"w");

		$this->nb_mwes = Mwe::count();

		if(!$this->nb_mwes) {
			$this->error = "Aucun mwe à exporter !";
			return;
		}

		$index=0;
		fputs($this->file, "expression;percentage-frozen;percentage-non-frozen;number-of-answers;number-skipped\r\n");
		
		Mwe::chunk(200, function ($mwes) use ($index) {
		    foreach ($mwes as $mwe) {
			    $total = $mwe->frozen + $mwe->unfrozen;

			    if($total != 0){
			        fputs($this->file, $mwe->content . ';' . round(($mwe->frozen / $total) * 100, 2) . ';' . round(($mwe->unfrozen / $total) * 100,2) . ';' . $total . ';'.$mwe->skipped."\r\n");
			    }else{
			        fputs($this->file, $mwe->content . ';' . $mwe->frozen . ';' . $mwe->unfrozen . ';' . $total . ';'.$mwe->skipped . "\r\n");
			    }
		    }
		});	


		if($this->nb_mwes>0){
			$exported_corpus = ExportedCorpus::create(['file'=>$file,'user_id'=>$this->user->id]);
			$exported_corpus->nb_sentences = $this->nb_mwes;
			$exported_corpus->type = 'mwe';
			$exported_corpus->save();
			$this->url_file = 'asset/conll?exported_corpus_id='.$exported_corpus->id;		
		} else {
			$this->error = "Aucun mwe exporté.";
		}
		
		fclose($this->file);	
	} catch (Exception $Ex){
		$this->error = $Ex->getMessage();
	}
}

}
