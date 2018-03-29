@extends('back.master')

@section('main')
@if(isset($sentences))
    {{ $sentences->links() }}
@endif
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
        <div class="col-1">
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
<script>

</script>
{!! Html::script('js/graph-annotator.js') !!}
<script>

var all_annotations = {!! $annotations->makeVisible(['word','word_position','governor_position','relation_name','source_id','parsers','score','best','pos','lemma','features','user_id'])->toJson() !!};
var sentence = {!! $sentence->toJson() !!};
var parsers = {!! json_encode(array_values($parsers)) !!};
var data = {
    annotations : all_annotations,
    sentence : sentence,
    parsers : parsers
};
var graphs_container = new GraphSVGContainer(data,'diff','game');
@if($mode=='diff')
    // var graphs_container = new GraphSVGContainer(data,'diff','gold');
@else
    // var graphs_container = new GraphSVGContainer(data,'diff','game');
@endif
graphs_container.draw();

</script>
@stop
