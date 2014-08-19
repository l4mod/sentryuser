<?php
/**
 * Created by PhpStorm.
 * User: amitav
 * Date: 8/8/14
 * Time: 9:32 PM
 */

class PermApi
{
    /**
     * This is an internal function to check if the user has the permission.
     * @param $permissionName
     * @return bool
     */
    public static function user_has_permission($permissionName)
    {
        $user = Sentry::getUser();
        $superAdmin = Sentry::findGroupById(1); // hard coded to get super admin

        // check if super admin, bypass has access check
        if($user->inGroup($superAdmin))
            return true;

        if ($user->hasAccess($permissionName))
            return true;
        else
            return false;
    }

    /**
     * This function will call internal function to check permission,
     * if permission is not there, redirect to access denied page.
     * @param $permissionName
     */
    public static function access_check($permissionName)
    {
        if (!PermApi::user_has_permission($permissionName))
        {
            header('Location: ' . url('access-denied'));die;
        }
    }
}