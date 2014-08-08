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

/* this section is for authenticated users only */
Route::group(array(
    'before' => 'checkAuth'
), function ()
{
    // general user login and other pages
    Route::get('user/logout', 'UserController@handleUserLogout');
    Route::get('user/dashboard', 'UserController@handleUserDashboard');
    
    // edit profile section
    Route::get('edit-profile', 'UserController@handleEditProfile');
    Route::post('save-profile', 'UserController@handleSaveProfile');
    
    // the permission matrix section
    Route::get('user/permission/list', 'PermissionController@handlePermissionListing');
    Route::post('user/permission/save', 'PermissionController@handlePermissionSave');
    Route::post('user/permission/add', 'PermissionController@handlePermissionAdd');
    Route::post('user/role/add', 'PermissionController@handleRoleAdd');
});

Route::filter('checkAuth', function ()
{
    if (! GlobalHelper::checkAuth())
        return Redirect::to('/');
});