<?php

/**
 * Created by PhpStorm.
 * User: Amitav Roy
 * Date: 7/17/14
 * Time: 11:21 AM
 */
class SentryUser extends Eloquent
{

    /**
     * This function is login the user in by taking the username and password.
     * 
     * @param unknown $username            
     * @param unknown $password            
     * @return boolean
     */
    public function authenticateUser($username, $password)
    {
        try {
            // Authenticate the user
            $credentials = array(
                'email' => $username,
                'password' => $password
            );
            
            $user = Sentry::authenticate($credentials, false);
            $this->setUserSession($user->id);
            
            return true;
        } catch (Cartalyst\Sentry\Users\LoginRequiredException $e) {
            GlobalHelper::setMessage('Login field is required.', 'warning');
        } catch (Cartalyst\Sentry\Users\PasswordRequiredException $e) {
            GlobalHelper::setMessage('Password field is required.', 'warning');
        } catch (Cartalyst\Sentry\Users\WrongPasswordException $e) {
            GlobalHelper::setMessage('Wrong password, try again.', 'warning');
        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            GlobalHelper::setMessage('User was not found.', 'warning');
        } catch (Cartalyst\Sentry\Users\UserNotActivatedException $e) {
            GlobalHelper::setMessage('User is not activated.', 'warning');
        }        

        // The following is only required if the throttling is enabled
        catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e) {
            GlobalHelper::setMessage('User is suspended.', 'warning');
        } catch (Cartalyst\Sentry\Throttling\UserBannedException $e) {
            GlobalHelper::setMessage('User is banned.', 'warning');
        }
    }
    
    /**
     * Creating the session for the user with the user details
     * @param unknown $userId
     */
    public function setUserSession($userId)
    {
        $userObj = UserHelper::getUserObj($userId);
        Session::put('userObj', $userObj);
    }

    /**
     * This function handles the post data and does the edit profile
     *
     * @param unknown $postData
     *
     * @return bool
     */
    public function editProfile($postData)
    {
        // flag to check if we need to change the password or not.
        $passwordChangeFlag = false;
        
        // checking if we need to change the password or not
        if ($postData['newPassword'] != '' && $postData['conf'] != '' && $postData['currentPassword'] != '')
        {
            if ($this->updateProfilePassword($postData['currentPassword'], $postData['newPassword'], $postData['conf']))
                $passwordChangeFlag = true;
        }
        
        // get the sentry user object
        $user = Sentry::getUser();
        
        // set the password only if the flag is true
        if ($passwordChangeFlag == true)
            $user->password = $postData['conf'];
        
        $user->first_name = $postData['firstname'];
        $user->last_name = $postData['lastName'];

        // first check if the module is present
        if (in_array('Amitavroy\Filemanaged\FilemanagedServiceProvider', Config::get('app.providers')))
        {
            if ($postData['hiddenProfileImage'] != asset($postData['profileImage']))
            {
                $destination = 'uploads/user_pic/';
                $this->setUserProfileFromUrl($postData['profileImage'], $destination);
            }
        }

        if ($user->save())
        {
            // calling the event of profile change
            $subscriber = new SentryuserEventHandler;
            Event::subscribe($subscriber);
            Event::fire('sentryuser.profilechange', $user);
            
            SentryHelper::setMessage('Your profile details were updated');
            return true;
        }
        else
        {
            SentryHelper::setMessage('Your changes were not saved. Please try again', 'info');
            return false;
        }
    }
    
    /**
     * This function is checking if the current password is correct.
     * @param unknown $password
     * @return boolean
     */
    private function checkUserCurrentPassword($password)
    {
        if (Sentry::checkPassword($password))
            return true;
        else 
            return false;
    }
    
    /**
     * This is the logic for change password.
     * @param unknown $currentPass
     * @param unknown $newPass
     * @param unknown $confPass
     * @return boolean
     */
    private function updateProfilePassword($currentPass, $newPass, $confPass)
    {
        // although check in JS, still at PHP end checking if the two password match.
        if ($newPass != $confPass)
        {
            GlobalHelper::setMessage('The two password does not match.', 'info');
            return false;
        }
        
        // checking if the current password is correct or not.
        if (!$this->checkUserCurrentPassword($currentPass))
        {
            SentryHelper::setMessage('Your current password does not match.', 'info');
            return false;
        }
        else
            return true;
    }

    private function setUserProfileFromUrl($url, $destination)
    {
        $user = Sentry::getUser();
        $fileId = FileApi::uploadFromURL($url, $destination);

        // the old file needs to be deleted at this instance if not default
        

        DB::table('user_details')->where('user_id', $user->id)->update(array(
            'user_profile_img' => $fileId
        ));

        $subscriber = new SentryuserEventHandler;
        Event::subscribe($subscriber);
        Event::fire('sentryuser.profilechange', $user);

        return true;
    }

    public function getUsers($user_id = null)
    {
        $arrSelect = array(
            'u.id', 'u.email', 'u.activated', 'u.last_login', 'u.first_name', 'u.last_name',
            'g.name as roleName'
        );

        $query = DB::table('users as u');

        $query->select($arrSelect);

        if ($user_id != null)
            $query->where('u.id', $user_id);

        $query->join('users_groups as ug', 'ug.user_id', '=', 'u.id');
        $query->join('groups as g', 'g.id', '=', 'ug.group_id');

        $query->orderBy('u.id', 'desc');

        return $query;
    }
}