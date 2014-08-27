@section('scripts')
@parent<script type="text/javascript" src="{{ asset('packages/l4mod/sentryuser/sentry-scripts.js') }}"></script>
@stop
@section('content')
<div class="row">
	<div class="col-md-12">
		<h1>Permission Matrix</h1>
	</div>
</div>

<div class="row">
    <div class="col-md-12">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li class="active"><a href="#home" role="tab" data-toggle="tab">Permissions Assignment</a></li>
            <li><a href="#add-permission" role="tab" data-toggle="tab">Manage Permissions</a></li>
            <li><a href="#add-role" role="tab" data-toggle="tab">Manage Roles</a></li>
        </ul>
    </div>
</div>

<div class="row">
	<div class="col-md-12">
		<!-- Tab panes -->
		<div class="tab-content">
			<div class="tab-pane active" id="home">
				<h3>Assign permissions</h3>
				{{ Form::open(array('url' => 'user/permission/save', 'role' =>
				'form')) }}
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Permission name</th> @foreach ($groups as $group)
							<th>{{$group->name}}</th> @endforeach
						</tr>
					</thead>
					<tbody>
						@foreach ($permissions as $key => $permission)
						<tr>
							<td>{{ ucwords($key) }}</td> 
							@foreach ($permission as $p) 
                                @if($p->allow == 1)
                                <td><input type="checkbox" checked
                                	name="{{$p->permission_name}}|{{$p->name}}"
                                	value="{{$p->permission_id}}|{{$p->group_id}}|{{$p->allow}}"> <input
                                	type="hidden" name="{{$p->permission_name}}|{{$p->name}}|hidden"
                                	value="{{$p->permission_id}}|{{$p->group_id}}|{{$p->allow}}|{{$p->ping_id}}"></td>
                                @else
                                <td><input type="checkbox"
                                	name="{{$p->permission_name}}|{{$p->name}}"
                                	value="{{$p->permission_id}}|{{$p->group_id}}|{{$p->allow}}"> <input
                                	type="hidden" name="{{$p->permission_name}}|{{$p->name}}|hidden"
                                	value="{{$p->permission_id}}|{{$p->group_id}}|{{$p->allow}}|{{$p->ping_id}}"></td>
                                @endif
					        @endforeach
						</tr>
						@endforeach
					</tbody>
				</table>
				<input type="submit" class="btn btn-success" name="save"
					value="Save" /> {{ Form::close() }}
			</div>
			<div class="tab-pane" id="add-permission">
				<h3>Manage Permissions</h3>
				{{ Form::open(array('url' => 'user/permission/add', 'role' =>
				'form')) }}
				<div class="form-group">
					<input type="text" class="form-control" id="permissionName"
						name="permission_name" placeholder="Enter the new permission name">
				</div>
				<input type="submit" class="btn btn-success" name="save"
					value="Save" /> {{ Form::close() }}

			</div>

            <div class="tab-pane" id="add-role">
                <div class="row">
                    <div class="col-md-4">
                        <h3>Add New Role</h3>

                        {{ Form::open(array('url' => 'user/role/add', 'role' =>
                        'form')) }}
                        <div class="form-group">
                            <input type="text" class="form-control" id="roleName"
                                   name="role_name" placeholder="Enter the new role name">
                        </div>
                        <input type="submit" class="btn btn-success" name="save"
                               value="Save" />
                        {{ Form::close() }}
                    </div>
                    <div class="col-md-4 col-md-push-4">
                        <h3>Manage Role</h3>
                        <ul class="list-group">
                            @foreach ($groups as $group)
                            <li class="list-group-item">
                                @if ($group->id != 1 && $group->id != 3)
                                <a href="javascript:void(0);">
                                    <span class="pull-right remove fa fa-trash-o delete-entity"
                                          data-entity="role"
                                          data-entity-id="{{$group->id}}"
                                          id="group-{{$group->id}}"></span>
                                </a>
                                <a href="javascript:void(0);">
                                    <span class="pull-right edit fa fa-edit edit-entity"
                                          data-entity="role"
                                          data-entity-id="{{$group->id}}"></span></a>
                                @endif
                                {{$group->name}}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

		</div>
	</div>
</div>
@stop
