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

    <div class="col-md-12">
        <table class="table table-bordered table-responsive table-striped table-hover">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Logged in</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
            <tr>
                <td>{{$user->id}}</td>
                <td>{{$user->first_name}} {{$user->last_name}}</td>
                <td>{{$user->email}}</td>
                <td>{{$user->roleName}}</td>
                <td>{{$user->last_login}}</td>
                <td>Edit / Delete</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        {{$users->links()}}
    </div>
</div>
@stop