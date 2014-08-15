@section('scripts')
@parent<script type="text/javascript" src="{{ asset('packages/l4mod/sentryuser/sentry-scripts.js') }}"></script>
@stop
@section('content')
<div class="row">
    <div class="col-md-12">
        <h1>Add new User</h1>
    </div>

    <div class="col-md-5">
        {{ Form::open(array('url' => 'user/save', 'role' => 'form')) }}
        <div class="form-group">
            <label for="firstName">First name:</label>
            <input type="text" class="form-control" id="firstName" placeholder="Enter first name" name="fname" value="{{Input::old('fname')}}">
            <span class="error-display">{{$errors->first('fname')}}</span>
        </div>

        <div class="form-group">
            <label for="lastName">Last name:</label>
            <input type="text" class="form-control" id="lastName" placeholder="Enter last name" name="lname" value="{{Input::old('lname')}}">
            <span class="error-display">{{$errors->first('lname')}}</span>
        </div>

        <div class="form-group">
            <label for="emailadress">Email address:</label>
            <input type="email" class="form-control" id="emailadress" placeholder="Enter email address" name="emailadress" value="{{Input::old('emailadress')}}">
            <span class="error-display">{{$errors->first('emailadress')}}</span>
        </div>

        <div class="form-group">
            <label for="newPassword">Password</label>
            <input type="password" class="form-control" id="newPassword" placeholder="Enter your password" name="password">
            <p class="help-block">Minimum 8 characters</p>
            <span class="error-display">{{$errors->first('password')}}</span>
            <span class="glyphicon glyphicon-warning-sign form-control-feedback" id="warning-for-new-pass"></span>
            <span class="glyphicon glyphicon-ok form-control-feedback" id="success-for-new-pass"></span>
        </div>

        <div class="form-group">
            <label for="confPassword">Confirm password</label>
            <input type="password" class="form-control" id="confPassword" placeholder="Confirm your new password" name="conf">
            <span class="error-display">{{$errors->first('conf')}}</span>
            <span class="glyphicon glyphicon-warning-sign form-control-feedback" id="warning-for-conf-pass"></span>
            <span class="glyphicon glyphicon-ok form-control-feedback" id="success-for-conf-pass"></span>
        </div>

        <div class="form-group">
            <label for="role">Roles</label>
            <select name="role" id="role" class="form-control">
                @foreach ($roles as $role)
                <option value="{{$role->id}}">{{$role->name}}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        {{Form::close()}}
    </div>
</div>
@stop