<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      {{ link_to('/', Config::get('packages/l4mod/sentryuser/sentryuser.site-title'), array('class' => 'navbar-brand')) }}
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav navbar-right">
        <span class="user-image"><img src="{{asset(UserHelper::getUserPicture())}}" alt="" class="pull-left" width="35" height="35" /></span>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{UserHelper::getUserDisplayName()}} <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li>{{ link_to('edit-profile', 'Edit Profile') }}</li>
            <li class="divider"></li>
            <li>{{ link_to('user/logout', 'Logout') }}</li>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>