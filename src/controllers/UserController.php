<?php

/**
 * Created by PhpStorm.
 * User: Amitav Roy
 * Date: 7/17/14
 * Time: 10:26 AM
 */
class UserController extends BaseController
{

    /**
     * Defining the master layout.
     * 
     * @var string
     */
    protected $layout = 'sentryuser::master';

    /**
     * Calling the constructor to execute any code on load of this class.
     */
    public function __construct()
    {
        /**
         * Setting the layout of the controller to something else
         * if the configuration is present.
         */
        if (Config::get('packages/l4mod/sentryuser/sentryuser.master-tpl') != '')
            $this->layout = Config::get('packages/l4mod/sentryuser/sentryuser.master-tpl');
    }

    /**
     * Return the access denied page.
     * Mainly the check_access function will redirect a user to this page.
     */
    public function handleAccessDeniedPage()
    {
        $this->layout->menuSkip = true;
        $this->layout->content = View::make('sentryuser::access-denied');
    }

    /**
     * Return the view for the login page.
     */
    public function handleLoginPage()
    {
        if (Sentry::check() && Session::get('userObj')) {
            return Redirect::to('user/dashboard');
        }
        
        if (Config::get('packages/l4mod/sentryuser/sentryuser.login-tpl') == '') {
            $this->layout->menuSkip = true;
            $this->layout->content = View::make('sentryuser::login');            
        } else {
            return View::make(Config::get('packages/l4mod/sentryuser/sentryuser.login-tpl'));
        }
    }

    /**
     * This function is handling the post data from the login page.
     * 
     * @return mixed
     */
    public function handleUserAuthentication()
    {
        $username = Input::get('email');
        $password = Input::get('password');
        
        $SentryUser = new SentryUser();
        
        if ($SentryUser->authenticateUser($username, $password)) {
            SentryHelper::setMessage('Login successful', 'success');

            $user = Session::get('userObj'); // getting the user object from session to pass to the event.
            
            /* firing the login event */
            $userSubscriber = new SentryuserEventHandler();
            Event::subscribe($userSubscriber);
            Event::fire('sentryuser.login', array($user));
            
            return Redirect::to('user/dashboard');
        } else {
            return Redirect::to('user');
        }
    }

    /**
     * Return the view for the dashboard page.
     */
    public function handleUserDashboard()
    {
        $this->layout->content = View::make('sentryuser::dashboard');
    }

    /**
     * Handling user logout.
     * 
     * @return mixed
     */
    public function handleUserLogout()
    {
        Sentry::logout();
        SentryHelper::setMessage('You have been logged out of the system.');
        return Redirect::to('user');
    }

    /**
     * Returning the edit profile view.
     */
    public function handleEditProfile()
    {
        $thisUser = Session::get('userObj');
        $userData = UserHelper::getUserObj($thisUser->id);
        $this->layout->content = View::make('sentryuser::edit-profile')->with('userdata', $userData);
    }

    /**
     * Handling the post from the edit profile form.
     * 
     * @return mixed
     */
    public function handleSaveProfile()
    {
        $postData = Input::all();
        
        // creating the SentryUser object and calling the edit profile function.
        $SentryUser = new SentryUser();
        $SentryUser->editProfile($postData);
        
        if (isset($postData['user_id']))
            return Redirect::to('user/edit/' . $postData['user_id']);
        else
            return Redirect::to('edit-profile');
    }

    /**
     * Returning the user listing view.
     */
    public function handleUserListing()
    {
        // checking the access for the user
        PermApi::access_check('manage_users');
        
        $SentryUser = new SentryUser();
        
        $users = $SentryUser->getUsers()->paginate(10);
        
        $this->layout->content = View::make('sentryuser::user-listing')->with('users', $users);
    }

    /**
     * Returning the user add form view.
     */
    public function handleUserAdd()
    {
        // checking the access for the user
        PermApi::access_check('create_users');
        
        // get all sentry groups
        $roles = Sentry::findAllGroups();
        
        $this->layout->content = View::make('sentryuser::add-user')->with('roles', $roles);
    }

    /**
     * Handling the post data from user save.
     * 
     * @return mixed
     */
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
            'conf.matchpass' => 'The two passwords does not match' // this is for the custom validatio that we have written
                );
        
        // rules for the validation
        $rules = array(
            'fname' => 'required|min:3',
            'lname' => 'required|min:1',
            'password' => 'required|min:8',
            'conf' => 'required|Matchpass:' . $postData["password"],
            'emailadress' => 'required|email|Checkemailexist'
        );
        
        $validator = Validator::make($postData, $rules, $messages);
        
        // when there are errors in the form
        if ($validator->fails()) {
            // send back to the page with the input data and errors
            SentryHelper::setMessage('Fix the errors.', 'warning'); // setting the error message
            return Redirect::to('user/add')->withInput()->withErrors($validator);
        }
        
        // when created through form, these users will be normal users.
        $postData['user_type'] = 'normal';
        
        // creating new user
        $SentryUser = new SentryUser();
        $SentryUser->addNewUser($postData);
        
        return Redirect::to('user/list');
    }

    /**
     * Handling the page for editing a user profile.
     * 
     * @param null $id            
     */
    public function handleEditUser($id)
    {
        $user = UserHelper::getUserObj($id);
        $thisUser = Session::get('userObj');
        
        $this->layout->content = View::make('sentryuser::edit-profile')
        ->with('currUser', $thisUser)
        ->with('userdata', $user)
        ->with('uid', $id);
    }

    /**
     * This is the generic function which will handle bulk operations.
     * Ajax call from jQuery is coming on this page with the action and processing as per option.
     * TODO: This is right now hardcoded to only delete. Need to make it generic.
     * 
     * @return mixed
     */
    public function entityOperationHandle()
    {
        $postData = Input::all();
        
        if ($postData['actions'] == '') {
            SentryHelper::setMessage('You need to select an action', 'warning');
            return Redirect::to('user/list');
        }
        
        $userIds = array();
        
        foreach ($postData as $key => $value) {
            $tempArr = explode('-', $key);
            if ($tempArr[0] == 'user')
                $userIds[] = $tempArr[1];
        }
        
        switch ($postData['actions']) {
            case 'delete':
                $SentryUser = new SentryUser();
                $SentryUser->deleteMultipleUser($userIds);
        }
        
        return Redirect::to('user/list');
    }

    /**
     * Handling the entity edit ajax requests.
     * 
     * @return mixed
     */
    public function entityEditHandle()
    {
        $entity = Input::get('entity');
        $entityId = Input::get('entityId');
        
        switch ($entity) {
            case 'user':
                return Response::json(array(
                    'url' => 'user/edit/' . $entityId
                ));
                break;
            
            case 'role':
                return Response::json(array(
                    'url' => 'user/role/edit/' . $entityId
                ));
                break;
        }
    }

    /**
     * Handling the entity delete ajax requests.
     */
    public function entityDeleteHandle()
    {
        $entity = Input::get('entity');
        $entityId = Input::get('entityId');
        
        switch ($entity) {
            case 'user':
                $table = 'users';
                DB::table($table)->where('id', $entityId)->delete();
                SentryHelper::setMessage('The user has been deleted');
                break;
            
            case 'role':
                $SentryPermission = new SentryPermission();
                $SentryPermission->deleteRole($entityId);
                break;
        }
    }

    /**
     * Handling the OAuth login
     */
    public function handleOAuthLogin()
    {
        // get data from input
        $code = Input::get('code');
        
        // get google service
        $googleService = OAuth::consumer('Google');
        
        // check if code is valid
        
        // if code is provided get user data and sign in
        if (! empty($code)) {
            // This was a callback request from google, get the token
            $token = $googleService->requestAccessToken($code);
            
            // Send a request with it
            $result = json_decode($googleService->request('https://www.googleapis.com/oauth2/v1/userinfo'), true);
            
            $SentryUser = new SentryUser();
            
            // checking if the email domain is allowed
            if ($SentryUser->validateOAuthAllowedDomains($result['email'])) {
                $SentryUser->handleOAuthLogin($result);
                return Redirect::to('user/dashboard');
            } else {
                SentryHelper::dsm('This domain is not allowed on this site.', 'warning');
            }
        }         // if not ask for permission first
        else {
            // get googleService authorization
            $url = $googleService->getAuthorizationUri();
            
            // return to google login url
            return Redirect::to((string) $url);
        }
    }
}