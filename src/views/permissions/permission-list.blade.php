@section('content')
<div class="row">
	<div class="col-md-12">
		<!--<h1>Permission table</h1>-->
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li class="active"><a href="#home" role="tab" data-toggle="tab">Permission
					List</a></li>
			<li><a href="#add-permission" role="tab" data-toggle="tab">Add
					Permission</a></li>
			<li><a href="#add-role" role="tab" data-toggle="tab">Add Role</a></li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<div class="tab-pane active" id="home">
				<h1>Manage your permissions</h1>
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
							<td>{{ ucwords($key) }}</td> @foreach ($permission as $p) @if
							($p->allow == 1)
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
							@endif @endforeach
						</tr>
						@endforeach
					</tbody>
				</table>
				<input type="submit" class="btn btn-success" name="save"
					value="Save" /> {{ Form::close() }}
			</div>
			<div class="tab-pane" id="add-permission">
				<h1>Add a new permission</h1>
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
				<h1>Add new role</h1>
				{{ Form::open(array('url' => 'user/role/add', 'role' =>
				'form')) }}
			</div>
		</div>
	</div>
</div>
@stop
