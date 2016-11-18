  <!-- Modal -->
  <div class="modal fade" id="modalRegister" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-body" style="padding:40px 50px;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h1><span class="glyphicon glyphicon-lock"></span> {{ trans('site.register') }}</h1>        
          {!! Form::open(['url' => 'auth/register', 'method' => 'post', 'role' => 'form']) !!} 
            <div class="form-group {{ $errors->has('username') ? 'has-error' : '' }}">
              <label for="username"><span class="glyphicon glyphicon-user"></span> {{ trans('site.pseudo') }}</label>
              <input type="text" class="form-control" name="username" id="username" placeholder="Enter email">
              {{ $errors->first('username', '<small class="help-block">:message</small>') }}
            </div>
            <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
              <label for="password"><span class="glyphicon glyphicon-eye-open"></span> {{ trans('site.password') }}</label>
              <input type="password" class="form-control" name="password" id="password" placeholder="Enter password">
              {{ $errors->first('password', '<small class="help-block">:message</small>') }}
            </div>
            <div class="form-group {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
              <label for="password_confirmation"><span class="glyphicon glyphicon-eye-open"></span> {{ trans('site.confirm-password') }}</label>
              <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Enter password">
              {{ $errors->first('password_confirmation', '<small class="help-block">:message</small>') }}
            </div>
            <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
              <label for="email"><span class="glyphicon glyphicon-eye-open"></span> {{ trans('site.email').'*' }}</label>
              <input type="text" class="form-control" name="email" id="email" placeholder="Enter password">
              {{ $errors->first('email', '<small class="help-block">:message</small>') }}
            </div>
            <!-- <div class="checkbox">
              <label><input type="checkbox" value="" checked>Remember me</label>
            </div>-->
              <button type="submit" class="btn btn-success btn-block"><span class="glyphicon glyphicon-off"></span> {{ trans('site.submit-login') }}</button>
          {!! Form::close() !!}
          <div class="modal-footer">
      {!! trans('site.asterisk-register') !!}
            <button type="submit" class="btn btn-danger btn-default pull-left" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
            <p>Not a member? <a href="#">Sign Up</a></p>
            <p>Forgot <a href="#">Password?</a></p>
          </div>          
        </div>

      </div>
      
    </div>
  </div> 
