<?php
/**
 * Created by PhpStorm.
 * User: Amitav Roy
 * Date: 7/29/14
 * Time: 11:43 AM
 */

class SentryuserEventHandler {

    public function onUserLogin($event)
    {
        Log::info('I was here while login');
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