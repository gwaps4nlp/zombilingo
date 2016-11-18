@extends('back.template')

@section('content')
<h1>Import a wikipedia's page</h1>
<div class="row">
{!! Form::open(['url' => 'corpus/import-from-url', 'method' => 'post', 'role' => 'form', 'files'=>true]) !!}
{!! Form::control('text', 0, 'url', $errors, 'Url de la page') !!}
{!! Form::control('file',0,'conll',$errors,'Fichier') !!}
<div class="form-group  ">
  <label for="sentence_filter" class="control-label">Parse</label>
  <input type="radio" name="sentence_filter" value="all" checked="checked" /> All 
  <input type="radio" name="sentence_filter" value="1mod4"/> only id=1[4] 
  <input type="radio" name="sentence_filter" value="1mod4"/> only id=3[4]
</div>
{!! Form::submit('Import (test, just print)', null,['class' => 'btn btn-success']) !!}
{!! Form::close() !!}
{!! Form::open(['url' => 'corpus/sentences-splitter', 'method' => 'post', 'role' => 'form', 'target'=>'sentences_splitted']) !!}
    <div class="col-md-10 col-md-offset-1">
		<textarea style="width:100%;height:500px" name="raw-text">{!! $sentences !!}</textarea>
    </div>
    @if(isset($url))
      <input type="hidden" value="{{ $url }}" name="url" />
    @endif
{!! Form::submit('Split sentences', null,['class' => 'btn btn-success']) !!}
{!! Form::close() !!}
    <script>
      function resizeIframe(obj) {
        obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
        var arrFrames = parent.parent.document.getElementsByTagName("IFRAME");
        for (var i = 0; i < arrFrames.length; i++) {

          if (arrFrames[i].name != obj.name) {
            //alert("1"+arrFrames[i].name);
            resizeIframe(arrFrames[i]);

            }
        }        
      }
      function resizeIframeAgain(obj) {
        obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
        var arrFrames = parent.document.getElementsByTagName("IFRAME");
        for (var i = 0; i < arrFrames.length; i++) {

          if (arrFrames[i].name != obj.name) {
            //alert("1"+arrFrames[i].name);
            resizeIframeAgain(arrFrames[i]);

            }
        }        
      }
    </script>
<iframe name="sentences_splitted" style="width:100%;" frameborder="0" scrolling="no" onload="resizeIframe(this);resizeIframeAgain(this);" />
@stop
