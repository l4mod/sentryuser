<?php
use Illuminate\Support\Facades\Redirect;
/**
 * Created by PhpStorm.
 * User: Amitav Roy
 * Date: 7/17/14
 * Time: 10:26 AM
 */

class UserController extends BaseController
{
    protected $layout = 'sentryuser::master';

    public function handleAccessDeniedPage()
    {
        $this->layout->menuSkip = true;
        $this->layout->content = View::make('sentryuser::access-denied');
    }

    public function handleLoginPage()
    {
        $this->layout->menuSkip = true;
        $this->layout->content = View::make('sentryuser::login');
    }

    public function handleUserAuthentication()
    {
        $username = Input::get('email');
        $password = Input::get('password');

        $SentryUser = new SentryUser;

        if ($SentryUser->authenticateUser($username, $password))
        {
            GlobalHelper::setMessage('Login successful', 'success');

            /* firing the login event*/
            $user = Session::get('userObj'); // getting the user object from session to pass to the event.
            $userSubscriber = new SentryuserEventHandler;

            Event::subscribe($userSubscriber);
            Event::fire('sentryuser.login', array($user));

            return Redirect::to('user/dashboard');
        }
        else
        {
            return Redirect::to('user');
        }
    }

    public function handleUserDashboard()
    {
        $this->layout->content = "asdffsdasdsadd";
    }

    public function handleUserLogout()
    {
        Sentry::logout();
        GlobalHelper::setMessage('You have been logged out of the system.');
        return Redirect::to('user');
    }
    
    public function handleEditProfile()
    {
        $thisUser = Session::get('userObj');
        $userData = UserHelper::getUserObj($thisUser->id);
        $this->layout->content = View::make('sentryuser::edit-profile')->with('userdata', $userData);
    }
    
    public function handleSaveProfile()
    {
        $postData = Input::all();
        
        // creating the SentryUser object and calling the edit profile function.
        $SentryUser = new SentryUser;
        $SentryUser->editProfile($postData);
        
        return Redirect::to('edit-profile');
    }
}