@section('scripts')
@parent<script type="text/javascript" src="{{ asset('packages/l4mod/sentryuser/sentry-scripts.js') }}"></script>
@stop
@section('content')
<div class="row">
    <div class="col-md-4">
        {{ Form::open(array('url' => 'save-profile', 'role' => 'form')) }}
        <h2>User details</h2>
        <div class="form-group">
            <label for="emailadress">Email address</label>
            <input type="email" class="form-control" id="emailadress" placeholder="Enter first name" name="emailadress" value="{{$userdata->email}}" readonly>
        </div>
        <div class="form-group">
            <label for="firstName">First Name</label>
            <input type="text" class="form-control" id="firstName" placeholder="Enter first name" name="firstname" value="{{$userdata->first_name}}">
        </div>
        <div class="form-group">
            <label for="lastName">Last Name</label>
            <input type="text" class="form-control" id="lastName" placeholder="Enter last name" name="lastName" value="{{$userdata->last_name}}">
        </div>
        @if (in_array('Amitavroy\Filemanaged\FilemanagedServiceProvider', Config::get('app.providers')))
        <div class="form-group">
            <label for="lastName">Profile image</label>
            <input type="text" class="form-control" id="profileImage" placeholder="Profile image url" name="profileImage" value="{{UserHelper::getUserPicture()}}">
            <input type="hidden" name="hiddenProfileImage" value="{{asset(UserHelper::getUserPicture())}}"/>
        </div>
        @endif
        
        @if ($userdata->user_type == 'normal')
        <h2>Password</h2>
        <div class="form-group">
            <label for="currentPassword">Current password</label>
            <input type="password" class="form-control" id="currentPassword" placeholder="Enter your current password" name="currentPassword">
            <p class="help-block">Curreny password is required if you are changing your password.</p>
        </div>
        <div class="form-group">
            <label for="newPassword">New password</label>
            <input type="password" class="form-control" id="newPassword" placeholder="Enter your new password" name="newPassword">
            <p class="help-block">Minimum 8 characters</p>
            <span class="glyphicon glyphicon-warning-sign form-control-feedback" id="warning-for-new-pass"></span>
            <span class="glyphicon glyphicon-ok form-control-feedback" id="success-for-new-pass"></span>
        </div>
        <div class="form-group">
            <label for="confPassword">Confirm password</label>
            <input type="password" class="form-control" id="confPassword" placeholder="Confirm your new password" name="conf">
            <span class="glyphicon glyphicon-warning-sign form-control-feedback" id="warning-for-conf-pass"></span>
            <span class="glyphicon glyphicon-ok form-control-feedback" id="success-for-conf-pass"></span>
        </div>
        @endif
        
        @if(PermApi::user_has_permission('manage_users'))
        <div class="form-group">
            <label for="roles">Role</label>
            {{SentryHelper::getDropdownFromArray('roles', SentryHelper::getGroupsArray(), $userdata->group_id)}}
            {{Form::hidden('old_group_id', $userdata->group_id)}}
        </div>
        @endif
        
        <button type="submit" class="btn btn-success">Save</button>
        @if (isset($uid))
        {{Form::hidden('user_id', $uid)}}
        @endif
        {{ Form::close() }}
    </div>
    <div class="col-md-8">
        @if (in_array('Amitavroy\Filemanaged\FilemanagedServiceProvider', Config::get('app.providers')))
        <div class="profile-image">
            <img src="{{asset(UserHelper::getUserPicture())}}" alt="" class="img-thumbnail pull-right" />
        </div>
        @endif
    </div>
</div>
@stop