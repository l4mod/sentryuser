<?php
/**
 * Created by PhpStorm.
 * User: Amitav Roy
 * Date: 7/29/14
 * Time: 11:43 AM
 */

class SentryuserEventHandler {

    public function onUserLogin($user, $OAuth = null)
    {
        $SentryUser = new SentryUser;
        $SentryUser->setUserSession($user->id);

        if ($OAuth != null) {
            // update o-auth data
            $SentryUser->updateOAuthProfileData($user->id, $OAuth);
        }
    }
    
    public function onUserProfileChange($user)
    {
        $SentryUser = new SentryUser;
        $SentryUser->setUserSession($user->id);
    }

    public function subscribe($events)
    {
        $events->listen('sentryuser.login', 'SentryuserEventHandler@onUserLogin');
        $events->listen('sentryuser.profilechange', 'SentryuserEventHandler@onUserProfileChange');
    }
}