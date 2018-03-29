<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExportedCorpus;
use Response, View;

use App\Http\Controllers\Controller;

class AssetController extends Controller
{
	
    /**
     * Send a conll file.
     *
     * @param  Illuminate\Http\Request $request     
     * @return Illuminate\Http\Response
     */
    public function getConll(Request $request)
    {
        $exported_corpus_id = $request->input('exported_corpus_id');
        $exported_corpus = ExportedCorpus::where('id', '=', $exported_corpus_id)->firstOrFail();
		$headers=['Content-Type'=> "text/css"];
		$response = Response::download(storage_path('export/'.$exported_corpus->file), $exported_corpus->file, $headers);

		return $response;

    }
}
