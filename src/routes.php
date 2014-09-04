<?php
/**
 * Created by PhpStorm.
 * User: Amitav Roy
 * Date: 7/17/14
 * Time: 9:54 AM
 */
Route::get('access-denied', 'UserController@handleAccessDeniedPage');
Route::get('user', 'UserController@handleLoginPage');
Route::post('do-login', 'UserController@handleUserAuthentication');

Route::get('user/oauth', 'UserController@handleOAuthLogin');

/* this section is for authenticated users only */
Route::group(array(
    'before' => 'checkAuth'
), function ()
{
    // entity urls
    Route::post('entity-bulk-update', 'UserController@entityOperationHandle');
    Route::post('delete-entity', 'UserController@entityDeleteHandle');
    Route::post('edit-entity', 'UserController@entityEditHandle');

    // general user login and other pages
    Route::get('user/logout', 'UserController@handleUserLogout');
    Route::get('user/dashboard', 'UserController@handleUserDashboard');
    
    // edit profile section
    Route::get('edit-profile', 'UserController@handleEditProfile');
    Route::post('save-profile', 'UserController@handleSaveProfile');

    // user section
    Route::get('user/list', 'UserController@handleUserListing');
    Route::get('user/add', 'UserController@handleUserAdd');
    Route::post('user/save', 'UserController@handleUserSave');
    Route::get('user/edit/{id}', 'UserController@handleEditUser');

    // the permission matrix section
    Route::get('user/permission/list', 'PermissionController@handlePermissionListing');
    Route::post('user/permission/save', 'PermissionController@handlePermissionSave');
    Route::post('user/permission/add', 'PermissionController@handlePermissionAdd');
    Route::post('user/role/add', 'PermissionController@handleRoleAdd');
    Route::get('user/role/edit/{id}', 'PermissionController@handleRoleEdit');
    Route::post('user/role/update', 'PermissionController@handleRoleUpdate');
});

Route::filter('checkAuth', function ()
{
    if (! Sentry::check() || !Session::get('userObj'))
        return Redirect::to('/');
});