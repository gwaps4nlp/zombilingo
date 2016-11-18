{!! Form::open(['url' => 'sentence/index', 'method' => 'post', 'role' => 'form']) !!}
<div class="form-group" style="margin-top:25px;">
	<input class="form-control" value='id, sentid, words...' placeholder="" selected="" name="search" type="text" onfocus="this.value='';" onblur="if(this.value=='') this.value='id, sentid, words...';">
</div>	
<input type="submit" value="Search" class="btn btn-success" />
{!! Form::close() !!}