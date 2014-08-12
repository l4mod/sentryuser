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

    /**
     * Setting the layout of the controller to something else
     * if the configuration is present.
     */
    public function __construct()
    {
        if (Config::get('sentryuser::sentryuser.master-tpl') != '')
        {
            $this->layout = Config::get('sentryuser::sentryuser.master-tpl');
        }
    }

    // return the access denied page
    public function handleAccessDeniedPage()
    {
        $this->layout->menuSkip = true;
        $this->layout->content = View::make('sentryuser::access-denied');
    }

    // return the view for the login page
    public function handleLoginPage()
    {
        $this->layout->menuSkip = true;
        $this->layout->content = View::make('sentryuser::login');
    }

    // this function is handling the post data from the login page
    public function handleUserAuthentication()
    {
        $username = Input::get('email');
        $password = Input::get('password');

        $SentryUser = new SentryUser;

        if ($SentryUser->authenticateUser($username, $password))
        {
            SentryHelper::setMessage('Login successful', 'success');

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

    // return the view for the dashboard page
    public function handleUserDashboard()
    {
        $this->layout->content = View::make('sentryuser::dashboard');
    }

    // handling user logout
    public function handleUserLogout()
    {
        Sentry::logout();
        SentryHelper::setMessage('You have been logged out of the system.');
        return Redirect::to('user');
    }

    // returning the edit profile view
    public function handleEditProfile()
    {
        $thisUser = Session::get('userObj');
        $userData = UserHelper::getUserObj($thisUser->id);
        $this->layout->content = View::make('sentryuser::edit-profile')->with('userdata', $userData);
    }

    // handling the post from the edit profile form
    public function handleSaveProfile()
    {
        $postData = Input::all();
        
        // creating the SentryUser object and calling the edit profile function.
        $SentryUser = new SentryUser;
        $SentryUser->editProfile($postData);
        
        return Redirect::to('edit-profile');
    }

    // returning the user listing view
    public function handleUserListing()
    {
        // checking the access for the user
        PermApi::access_check('manage_users');

        $SentryUser = new SentryUser;

        $users = $SentryUser->getUsers()->paginate(10);

        $this->layout->content = View::make('sentryuser::user-listing')->with('users', $users);
    }
}