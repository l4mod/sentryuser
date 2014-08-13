@if(PermApi::user_has_permission('manage_permissions'))
<div class="btn-group">
    <a href="{{url('user/permission/list')}}" class="btn btn-primary">Permission</a>
</div>
@endif

@if(PermApi::user_has_permission('manage_users'))
<div class="btn-group">
    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Users <span class="caret"></span></button>
    <ul class="dropdown-menu" role="menu">
        <li>{{ link_to('user/list', 'Manage Users') }}</li>
        <li>{{ link_to('user/add', 'Add Users') }}</li>
    </ul>
</div>
@endif

@if (in_array('Amitavroy\Mailing\MailingServiceProvider', Config::get('app.providers')))
<div class="btn-group">
    <a href="{{url('mailing/list')}}" class="btn btn-primary">Mailing List</a>
</div>
@endif