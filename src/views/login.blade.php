@section('content')
<div class="row">
    <div class="col-md-6 col-lg-push-2">
        {{ Form::open(array('url' => 'do-login', 'role' => 'form')) }}
        <div class="form-group">
            <label for="exampleInputEmail1">Email address</label>
            <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email" name="email">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Password</label>
            <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" name="password">
        </div>
        <button type="submit" class="btn btn-success">Login</button>
        @if (Config::get('packages/l4mod/sentryuser/sentryuser.o-auth') != false)
        <a href="{{url('user/oauth')}}" class="btn btn-info col-md-push1">Login with Google ID</a>
        @endif
        {{ Form::close() }}
    </div>
</div>
@stop