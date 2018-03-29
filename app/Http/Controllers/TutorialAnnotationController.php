<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Relation;
use App\Models\TutorialAnnotation;
use App\Repositories\RelationRepository;
use App\Repositories\AnnotationRepository;
use App\Repositories\TutorialAnnotationRepository;
use App\Services\TutorialAnnotationParser;
use Illuminate\Http\RedirectResponse;
use Response;

class TutorialAnnotationController extends Controller
{

    /**
     * Show a listing of the tutorial items.
     *
     * @param  App\Repositories\RelationRepository $relation
     * @param  App\Repositories\AnnotationRepository $annotations
     * @param  App\Repositories\TutorialAnnotationRepository $tutorial_annotation
     * @return Illuminate\Http\Response
     */
    public function getIndex(RelationRepository $relation, AnnotationRepository $annotations, TutorialAnnotationRepository $tutorial_annotation, Request $request, $relation_id=null)
    {
        $current_relation = new Relation();
        if($relation_id){
            $current_relation = $relation->getById($relation_id);
            $tutorial_annotations = $annotations->getTutorial($current_relation);
        }
		else
            $tutorial_annotations = array();
		
        $relations = $tutorial_annotation->getByRelation();
		return view('back.tutorial-annotation.index',compact('tutorial_annotations','relations','current_relation'));
    }
    
    /**
     * Change the visibility of a tutorial annotation
     *
     * @param  Illuminate\Http\Request $request
     * @param  App\Repositories\TutorialAnnotationRepository $tutorial_annotations
     * @return Illuminate\Http\Response
     */
    public function getChangeVisibility(Request $request, TutorialAnnotationRepository $tutorial_annotations)
    {
        $tutorial_annotation = $tutorial_annotations->getById($request->input('id'));
        $tutorial_annotation->visible = $request->input('visible');
        $tutorial_annotation->save();
        return Response::json($tutorial_annotation);
    }

    /**
     * Delete all the tutorial annotations of a given relation.
     *
     * @param  int $relation_id
     * @return Illuminate\Http\Response
     */
    public function getDeleteByRelation($relation_id)
    {
        TutorialAnnotation::where('relation_id', $relation_id)->delete();
        return new RedirectResponse(url('/tutorial-annotation/index'));
    }

    /**
     * Display the form to import a tutorial items file .
     *
     * @return Illuminate\Http\Response
     */
    public function getImport()
    {
        return view('back.tutorial-annotation.import');
    }

    /**
     * Launch the process to import a tutorial items file.
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
        $parser = new TutorialAnnotationParser($filePath);
		$parser->parse();
		return view('back.tutorial-annotation.post-import',compact('parser'));
    }

}
