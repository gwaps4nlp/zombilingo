<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\CorpusExportRequest;
use App\Http\Controllers\Controller;
use App\Models\Relation;
use App\Models\NegativeAnnotation;
use App\Repositories\RelationRepository;
use App\Repositories\AnnotationRepository;
use App\Repositories\NegativeAnnotationRepository;
use App\Services\NegativeAnnotationParser;
use Illuminate\Http\RedirectResponse;
use Response;

class NegativeAnnotationController extends Controller
{
	/**
     * Create a new NegativeAnnotationController instance.
     *
     * @param NegativeAnnotationRepository $negative_annotation
     * @return void
     */
    public function __construct(NegativeAnnotationRepository $negative_annotations)
    {
        $this->negative_annotations=$negative_annotations;
    }
	
    /**
     * Display a listing of the negative items.
     *
     * @param App\Repositories\RelationRepository $relation
     * @param App\Repositories\AnnotationRepository $annotations
     * @param Illuminate\Http\Request $request
     * @param int $relation_id
     * @return Illuminate\Http\Response
     */
    public function getIndex(RelationRepository $relation, AnnotationRepository $annotations, Request $request, $relation_id=null)
    {
        $current_relation = new Relation();
        if($relation_id){
            $current_relation = $relation->getById($relation_id);
            $negative_annotations = $annotations->getNegatives($current_relation);
        }
		else
            $negative_annotations = array();

		$relations = $this->negative_annotations->getByRelation();

		return view('back.negative-annotation.index',compact('negative_annotations','relations','current_relation'));
    }
    
    /**
     * Change the visibility of a negative item
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getChangeVisibility(Request $request)
    {
        $negative_annotation = $this->negative_annotations->getById($request->input('id'));
        $negative_annotation->visible = $request->input('visible');
        $negative_annotation->save();
        return Response::json($negative_annotation);
    }

    /**
     * Delete a negative item.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getDelete(Request $request)
    {
        $this->negative_annotations->destroy($request->input('id'));
        return new RedirectResponse(url('/negative-annotation/index'));
    }

    /**
     * Delete negative items by relation.
     *
     * @param  Illuminate\Http\Request $request
     * @param  int $relation_id
     * @return Illuminate\Http\Response
     */
    public function getDeleteByRelation($relation_id)
    {
        NegativeAnnotation::where('relation_id', $relation_id)->delete();
		return new RedirectResponse(url('/negative-annotation/index'));
    }

    /**
     * Show the form to import a negative items file .
     *
     * @return Illuminate\Http\Response
     */
    public function getImport()
    {
        return view('back.negative-annotation.import');
    }

    /**
     * Launch the process to import a negative items file.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function postImport(Request $request)
    {

        $destinationPath= storage_path()."/import/";
        $fileName=$request->file('file')->getClientOriginalName();
        
        $request->file('file')->move($destinationPath,$fileName);
        $filePath = $destinationPath.$fileName;
        $parser = new NegativeAnnotationParser($filePath);
		$parser->parse();
		return view('back.negative-annotation.post-import',compact('parser'));
    }


    /**
     * Delete a negative item.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function postDelete(CorpusRequest $request)
    {
        $this->negative_annotations->destroy($request->input('id'));
        return $this->getIndex();
    }

}
