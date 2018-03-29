<h1 class="text-center">Connexion</h1>
		
		{!! Form::open(['url' => 'auth/login', 'method' => 'post', 'role' => 'form']) !!}

    	{!! Form::control('text', 0, 'log', $errors, trans('site.log')) !!}

    	{!! Form::control('password', 0, 'password', $errors, trans('site.password')) !!}
	
    <input type="submit" value="{{ trans('site.send') }}" class="btn btn-success" />
{!! Form::close() !!}
 <h1 class="text-center">trans('site.forgotten-password')</h1>
<?php
    //echo form_open('compte/resetPass');
?>
{!! Form::open(['url' => 'auth/login', 'method' => 'post', 'role' => 'form']) !!}
    <div class="form-group">
        <label for="pseudo">Pseudonyme</label>
        <input type="text" name="pseudo" class="form-control"/>
    </div>
    <input type="submit" value="{{ trans('site.reset') }}" class="btn btn-success" />
</form>