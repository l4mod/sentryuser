### Sentry user and Permission
This module is a ready made user management and permission management module. Once this module is enabled and the Migrations are executed, this module creates a default Super Admin user and also creates a permission matrix system. The permission Matrix is a custom module which is using the Sentry groups along with custom tables to manage the permission management screen similar to drupal.

There will be a single function which can be used to check the access of a user based on the group he is in and the permission which his particular group has. 
[Note: This module depends completly on Sentry module]

#### How to use
To use this module, first we need to add the Service provider in app.php

    'Amitavroy\Sentryuser\SentryuserServiceProvider'

Once this is added, we need to run the migrations for this package and so a few additional tables are created and a default user with role Super Admin is created.

a) permissions b) permissision in groups.

##### Users
The user module has the basic login functionality which is internally using Sentry module. There is edit profile page where user can update his First name, Last name and also change his password.

##### Permissions
The permission setting page can be accessed from the top menu if you are using the complete Github application or through this url: "user/permission/list".