@section('content')
<div class="row">
    <div class="col-md-12">
        <h1>Add new User</h1>
    </div>

    <div class="col-md-12">
        {{ Form::open(array('url' => 'user/save', 'role' => 'form')) }}
        <div class="form-group">
            <label for="firstName">First name:</label>
            <input type="text" class="form-control" id="firstName" placeholder="Enter first name" name="fname" value="">
        </div>

        <div class="form-group">
            <label for="lastName">Last name:</label>
            <input type="text" class="form-control" id="lastName" placeholder="Enter last name" name="lname" value="">
        </div>

        <div class="form-group">
            <label for="emailadress">Email address:</label>
            <input type="email" class="form-control" id="emailadress" placeholder="Enter email address" name="emailadress" value="">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" placeholder="Enter your password" name="password">
            <p class="help-block">Minimum 8 characters</p>
        </div>
        <button type="submit" class="btn btn-success">Save</button>
        {{Form::close()}}
    </div>
</div>
@stop