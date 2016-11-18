  <!-- Modal -->
  <div class="modal fade" id="modalLogin" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-body" style="padding:40px 50px;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h1><span class="glyphicon glyphicon-lock"></span> {{ trans('site.connection') }}</h1>
          {!! Form::open(['url' => 'auth/login', 'method' => 'post', 'role' => 'form', 'id' => 'form-login']) !!}
            <div class="form-group {{ $errors->has('log') ? 'has-error' : '' }}">
              <label for="log" class="control-label"><span class="glyphicon glyphicon-user"></span> {{ trans('site.pseudo') }}</label>
              <input type="text" class="form-control" name="log" id="log" placeholder="Enter email">
              {{ $errors->first('log', '<small class="help-block">:message</small>') }}
            </div>
            <div class="form-group {{ $errors->has('password_log') ? 'has-error' : '' }}">
              <label for="password_log" class="control-label"><span class="glyphicon glyphicon-eye-open"></span> {{ trans('site.password') }}</label>
              <input type="password" class="form-control" name="password_log" id="password_log" placeholder="Enter password">
              {{ $errors->first('password_log', '<small class="help-block">:message</small>') }}
            </div>
            <!-- <div class="checkbox">
              <label><input type="checkbox" value="" checked>Remember me</label>
            </div>-->
              <input type="hidden" value="{{ Session::get('url.intended', url('/')) }}" />
              <button type="submit" class="btn btn-success btn-block"><span class="glyphicon glyphicon-off"></span> {{ trans('site.submit-login') }}</button>
          {!! Form::close() !!}
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger btn-default pull-left" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
          <p>Not a member? <a href="#" id="register">Sign Up</a></p>
          <p>Forgot <a href="#">Password?</a></p>
        </div>          
        </div>

      </div>
      
    </div>
  </div> 