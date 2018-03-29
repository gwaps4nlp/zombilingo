@extends('back.master')

@section('main')

<div id="elements_html">
<select style="position:absolute;width:70px;" id="select_relations" class="select_relations">
@foreach($relations as $relation_id => $slug)
<option value="{{ $relation_id }}">{{ $slug }}</option>
@endforeach
</select>
<select style="position:absolute;width:70px;" id="select_pos" class="select_pos">
@foreach($pos as $pos_id => $_pos)
<option value="{{ $pos_id }}">{{ $_pos }}</option>
@endforeach
</select>
</div>
<div id="main">
<div id="content" style="position:relative;">

    <div class="row">
        <div class="col-12 d-flex justify-content-end">
            <button id="btn_save" class="btn btn-primary btn-sm m-1">Valider</button> 
            <i class="fa fa-cog fa-2x float-right" data-toggle="collapse" data-target="#sidebar-graph" aria-expanded="true" aria-controls="sidebar-graph" onclick="toggleNav()"></i>
        </div>
    </div>
</div>
<div id="mySidenav" class="sidenav">
  <a href="javascript:void(0)" class="closebtn" onclick="toggleNav()">&times;</a>
    @include('back.sentence.graph-module')
</div>
</div>
@stop
@section('scripts')
{!! Html::script('js/svg/jquery.svg.js') !!}
{!! Html::script('js/graph-annotator.js') !!}
<script>

var all_annotations = {!! $annotations->makeVisible(['word','word_position','governor_position','relation_name','source_id','parsers','score','best','pos','lemma','features','user_id'])->toJson() !!};
console.log(all_annotations);
var sentence = {!! $sentence->toJson() !!};
var parsers = {!! json_encode(array_values($parsers)) !!};
@if($config)
var config_user = {!! $config->config !!};
@endif
var data = {
    annotations : all_annotations,
    sentence : sentence,
    parsers : parsers
};
@if($sentence->isReference())
    var graphs_container = new GraphSVGContainer(data,'reference');
@else
    var graphs_container = new GraphSVGContainer(data,'correction');
@endif
graphs_container.draw();

</script>
@stop
