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
        if (Config::get('packages/l4mod/sentryuser/sentryuser.master-tpl') != '')
        {
            $this->layout = Config::get('packages/l4mod/sentryuser/sentryuser.master-tpl');
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

    public function handleUserAdd()
    {
        // checking the access for the user
        PermApi::access_check('create_users');

        // get all sentry groups
        $roles = Sentry::findAllGroups();

        $this->layout->content = View::make('sentryuser::add-user')->with('roles', $roles);
    }

    public function handleUserSave()
    {
        $postData = Input::all();

        // message if validation fails
        $messages = array(
            'emailadress.required' => 'We need to know your e-mail address!',
            'emailadress.email' => 'We don\'t think it is an email address',
            'emailadress.checkemailexist' => 'This email is in use.',
            'fname.required' => 'Please enter your full first name',
            'lname.required' => 'Please enter your full last name',
            'password.required' => 'You have to set a password',
            'password.min' => 'Password should be at least 8 characters long',
            'conf.required' => 'Write is again so that you are sure about your password',
            'conf.matchpass' => 'The two passwords does not match', // this is for the custom validatio that we have written
        );

        // rules for the validation
        $rules = array(
            'fname' => 'required|min:3',
            'lname' => 'required|min:1',
            'password' => 'required|min:8',
            'conf' => 'required|Matchpass:' . $postData["password"],
            'emailadress' => 'required|email|Checkemailexist',
        );

        $validator = Validator::make($postData, $rules, $messages);

        // when there are errors in the form
        if ($validator->fails())
        {
            // send back to the page with the input data and errors
            SentryHelper::setMessage('Fix the errors.', 'warning'); // setting the error message
            return Redirect::to('user/add')->withInput()->withErrors($validator);
        }

        $SentryUser = new SentryUser;

        // creating new user
        $SentryUser->addNewUser($postData);

        return Redirect::to('user/list');
    }

    public function entityOperationHandle()
    {
        $postData = Input::all();

        if ($postData['actions'] == '')
        {
            SentryHelper::setMessage('You need to select an action', 'warning');
            return Redirect::to('user/list');
        }

        $userIds = array();

        foreach ($postData as $key => $value)
        {
            $tempArr = explode('-', $key);
            if ($tempArr[0] == 'user')
                $userIds[] = $tempArr[1];
        }

        switch ($postData['actions'])
        {
            case 'delete':
                $SentryUser = new SentryUser;
                $SentryUser->deleteMultipleUser($userIds);
        }

        return Redirect::to('user/list');
    }

    public function entityDeleteHandle()
    {
        $entity = Input::get('entity');
        $entityId = Input::get('entityId');

        switch ($entity)
        {
            case 'user':
                $table = 'users';
                DB::table($table)->where('id', $entityId)->delete();
                SentryHelper::setMessage('The user has been deleted');
                break;
        }
    }
}