#Sentry User Documentation
##Introduction
A user management and permission management module on top of the Sentry (by cartalyst) module. This module uses all the functionalities core to Sentry module, but just it give you a UI like the Drupal’s permission matrix to manage things a bit easily.

The markup of the UI is Bootstrap layout so the markup can fit in well with any other Bootstrap theme or even if someone is writing their own CSS.

**Update**: Now this module also has O-Auth integration with Google accounts if we want. When this is enabled, we get an additional login button. We need to add two lines of configuration for the O-Auth to work. Here I am just using the O-Auth module from Artdarek.

The requirements for this package is same as what Sentry module has.

##Installation
Assuming that you have the Sentry module already installed, adding the following line to the composer.json will will download the correct package:

    "l4mod/sentryuser": "1.*"

And then do composer update to fetch the package.

After we download the package, we need to add the service provider in our app.php file:

    'Amitavroy\Sentryuser\SentryuserServiceProvider'

Once this is done, I have created a php artisan command which should set up things correctly. We just need to run the following command:

    php artisan su:reset

It should do the following things:

1. Run the migrations for the Sentryuser module which is creating a few default users, the few required tables.
2. Publish the assets for this package
3. Publish the configuration files
4. Clear cache and run the optimize class loader

Should get something like this as output:

    Migrated: 2014_07_17_013906_create_default_users
    Migrated: 2014_07_17_020953_create_permissions_table
    Migrated: 2014_07_19_090625_create_perm_in_group_table
    Migrated: 2014_07_27_062542_create_user_details_tbl
    Assets published for package: l4mod/sentryuser
    Configuration published for package: l4mod/sentryuser
    Application cache cleared!
    Generating optimized class loader

Once this is ready, we are ready with two default users ready to use the application.

##Configurations available

Once this package is enabled, there are quite a few configurations that we can:

1. **Site title:** This will enable us to set the title of the application and also what is shown in the top menu bar of the user section.

2. **Default pic:** I have used a user pic at the top nagivation and so a default pic is also part of the package. If we want, we can change the default picture url.

3. **Nav tpl:** This config will help define the tpl which will be used for the top navigation if someone doesn't want the default navigation provided. Once done, the user will have to include the urls which are part of the default menu.

4. **O-Auth:** If we want to use this functionality, then we need to set this to true. Only then should the login with Google Id come on the login page.

5. **O-Auth-domain:** If the user wants to allow Google Ids from specific domains only like only Gmail, then that can be set in this array.

6. **Login tpl:** If the user want's a custom login form (which is very obvious) then this configuration will help achieve that. Only thing is to keep the form submit on correct url.

7. **Dashboard url:** Where the user would be redirected to once he has logged in successfully? This is the configuration which will control that. Put the direct route url here.