@section('scripts')
@parent<script type="text/javascript" src="{{ asset('packages/l4mod/sentryuser/sentry-scripts.js') }}"></script>
@stop
@section('content')
@section('content')
<div class="row">
    <div class="col-md-12">
        <h1>User listing</h1>
    </div>

    <div class="col-md-12">
        <p><a href="{{url('user/add')}}">+ Add User</a></p>
    </div>

    {{ Form::open(array('url' => 'entity-bulk-update', 'role' => 'form', 'class' => 'form-inline')) }}
    <div class="col-md-12">
        <div class="form-group">
            <label for="action">Bulk operation</label>
            <select name="actions" id="action" class="form-control">
                <option value="">SELECT</option>
                <option value="delete">Delete</option>
            </select>
        </div>
        <div class="form-group">
            <div class="input-group">
                <input type="submit" name="Update" class="btn btn-primary" value="Update"/>
            </div>
        </div>
        <p>&nbsp;</p>
        <table class="table table-bordered table-responsive table-striped table-hover">
            <thead>
            <tr>
                <th><input type="checkbox" name="multi-select-parent" value="" data-child="multi-select" class="chk-select-all"/></th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Logged in</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
            <tr class="tr-chk-bx-sel">
                <td><input type="checkbox" name="user-{{$user->id}}" value="user-{{$user->id}}" class="multi-select"/></td>
                <td>{{$user->first_name}} {{$user->last_name}}</td>
                <td>{{$user->email}}</td>
                <td>{{$user->roleName}}</td>
                <td>{{$user->last_login}}</td>
                <td>
                    <a href="javascript:void(0);">
                        <span class="pull-right remove fa fa-trash-o delete-entity"
                              data-entity="user"
                              data-entity-id="{{$user->id}}"></span></a>
                    <a href="javascript:void(0);">
                        <span class="pull-right edit fa fa-edit edit-entity"
                              data-entity="user"
                              data-entity-id="{{$user->id}}"></span></a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        {{$users->links()}}
    </div>
    {{Form::close()}}
</div>
@stop