#L4 User and Permission Management
##Introduction
A user management and permission management module on top of the Sentry (by cartalyst) module. This module uses all the functionalities core to Sentry module, but just it give you a UI like the Drupal’s permission matrix to manage things a bit easily.

The markup of the UI is Bootstrap layout so the markup can fit in well with any other Bootstrap theme or even if someone is writing their own CSS.

The requirements for this package is same as what Sentry module has.

##Installation
You need to first add the service provider inside app.php to start using the module, so add this line of code:

    'Amitavroy\Sentryuser\SentryuserServiceProvider'
    
Once this is added, we are ready to run one line of command which will set up everything - php artisan. Once this is done, you should get something like this:

    Migrated: 2014_07_17_013906_create_default_users
    Migrated: 2014_07_17_020953_create_permissions_table
    Migrated: 2014_07_19_090625_create_perm_in_group_table
    Migrated: 2014_07_27_062542_create_user_details_tbl
    Assets published for package: l4mod/sentryuser
    Configuration published for package: l4mod/sentryuser
    Application cache cleared!
    Generating optimized class loader

Login page can be accessed using /user url. This is where you can login based on the default users which have been created.

##Default Install

The default installation comes with two roles

1. Super Admin = this role has all permissions
2. Administrator = second role with stripped down privileges

There are two users which come with the default migrations:
amitavroy@gmail.com and amitav.roy@focalworks.in, the first one is super admin and the second being is the one with administrator role.