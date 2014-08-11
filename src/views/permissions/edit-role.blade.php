@section('content')
<div class="row">
    <div class="col-md-12">
        <h1>Edit Role</h1>
    </div>

    <div class="col-md-12">
        {{ Form::open(array('url' => 'user/role/update', 'role' => 'form')) }}
        <div class="form-group">
            <label for="editrole">Email address</label>
            <input type="text" class="form-control" id="editrole" placeholder="Enter role name" name="role" value="{{$role->name}}">
            <input type="hidden" name="roleId" value="{{$role->id}}"/>
        </div>
        <input type="submit" value="Save" class="btn btn-primary"/>
        {{Form::close()}}
    </div>
</div>
@stop