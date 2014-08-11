<?php
/**
 * Created by PhpStorm.
 * User: amitav
 * Date: 8/8/14
 * Time: 9:32 PM
 */

class PermApi
{
    public static function user_has_permission($permissionName)
    {
        $user = Sentry::getUser();
        $superAdmin = Sentry::findGroupById(1);

        // check if super admin, bypass has access check
        if($user->inGroup($superAdmin))
            return true;

        if ($user->hasAccess($permissionName))
            return true;
        else
            return false;
    }

    public static function access_check($permissionName)
    {
        if (!PermApi::user_has_permission($permissionName))
        {
            header('Location: ' . url('access-denied'));die;
        }
    }
}