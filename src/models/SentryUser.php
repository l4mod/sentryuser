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
            
            // calling the event of setting user session
            $subscriber = new SentryuserEventHandler();
            Event::subscribe($subscriber);
            Event::fire('sentryuser.login', $user);
            
            return true;
        } catch (Cartalyst\Sentry\Users\LoginRequiredException $e) {
            SentryHelper::setMessage('Login field is required.', 'warning');
        } catch (Cartalyst\Sentry\Users\PasswordRequiredException $e) {
            SentryHelper::setMessage('Password field is required.', 'warning');
        } catch (Cartalyst\Sentry\Users\WrongPasswordException $e) {
            SentryHelper::setMessage('Wrong password, try again.', 'warning');
        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            SentryHelper::setMessage('User was not found.', 'warning');
        } catch (Cartalyst\Sentry\Users\UserNotActivatedException $e) {
            SentryHelper::setMessage('User is not activated.', 'warning');
        }        

        // The following is only required if the throttling is enabled
        catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e) {
            SentryHelper::setMessage('User is suspended.', 'warning');
        } catch (Cartalyst\Sentry\Throttling\UserBannedException $e) {
            SentryHelper::setMessage('User is banned.', 'warning');
        }
    }

    /**
     * Creating the session for the user with the user details
     *
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
        if (isset($postData['newPassword']) && $postData['newPassword'] != '' 
            && isset($postData['conf']) && $postData['conf'] != ''
            && isset($postData['currentPassword']) && $postData['currentPassword'] != '') {
            if ($this->updateProfilePassword($postData['currentPassword'], $postData['newPassword'], $postData['conf']))
                $passwordChangeFlag = true;
        }
        
        $logged_in_user = Session::get('userObj')->id;
        
        // check if uid is present. Uid as hidden when coming from user edit form. Else no id.
        if (isset($postData['user_id']))
            $user = Sentry::findUserById($postData['user_id']);
        else
            $user = Sentry::getUser();
            
        // set the password only if the flag is true
        if ($passwordChangeFlag == true)
            $user->password = $postData['conf'];
        
        $user->first_name = $postData['firstname'];
        $user->last_name = $postData['lastName'];
        
        // role update from edit page only when there is a change
        if (isset($postData['roles']) && $postData['roles'] != $postData['old_group_id']) {
            $this->changeUserGroup($postData['user_id'], $postData['roles'], $postData['old_group_id']);
        }
        
        // first check if the module is present
        if (in_array('Amitavroy\Filemanaged\FilemanagedServiceProvider', Config::get('app.providers'))) {
            if ($postData['hiddenProfileImage'] != asset($postData['profileImage'])) {
                $destination = 'uploads/user_pic/';
                $this->setUserProfileFromUrl($postData['profileImage'], $destination);
            }
        }
        
        if ($user->save()) {
            // event should fire only when user is editing his own profile.
            if (! isset($postData['user_id']) || $logged_in_user == $postData['user_id']) {
                // calling the event of profile change
                $subscriber = new SentryuserEventHandler();
                Event::subscribe($subscriber);
                Event::fire('sentryuser.profilechange', $user);
            }
            
            SentryHelper::setMessage('Your profile details were updated');
            return true;
        } else {
            SentryHelper::setMessage('Your changes were not saved. Please try again', 'info');
            return false;
        }
    }

    /**
     * This function is checking if the current password is correct.
     *
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
     *
     * @param unknown $currentPass            
     * @param unknown $newPass            
     * @param unknown $confPass            
     * @return boolean
     */
    private function updateProfilePassword($currentPass, $newPass, $confPass)
    {
        // although check in JS, still at PHP end checking if the two password match.
        if ($newPass != $confPass) {
            SentryHelper::setMessage('The two password does not match.', 'info');
            return false;
        }
        
        // checking if the current password is correct or not.
        if (! $this->checkUserCurrentPassword($currentPass)) {
            SentryHelper::setMessage('Your current password does not match.', 'info');
            return false;
        } else
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
        
        $subscriber = new SentryuserEventHandler();
        Event::subscribe($subscriber);
        Event::fire('sentryuser.profilechange', $user);
        
        return true;
    }
    
    private function changeUserGroup($user_id, $new_group_id, $old_group_id)
    {
        $thisUser = Sentry::findUserById($user_id);
        $newGroup = Sentry::findGroupById($new_group_id);
        $oldGroup = Sentry::findGroupById($old_group_id);
        
        // assign the new group
        $thisUser->addGroup($newGroup);
        
        // remove the previous group
        $thisUser->removeGroup($oldGroup);
    }

    public function getUsers($user_id = null)
    {
        $arrSelect = array(
            'u.id',
            'u.email',
            'u.activated',
            'u.last_login',
            'u.first_name',
            'u.last_name',
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

    public function addNewUser($userData)
    {
        $newUser = Sentry::createUser(array(
            'email' => $userData['emailadress'],
            'password' => $userData['password'],
            'activated' => true,
            'first_name' => $userData['fname'],
            'last_name' => $userData['lname']
        ));
        
        DB::table('user_details')->insert(array(
            'user_id' => $newUser->id,
            'user_type' => $userData['user_type'],
        ));
        
        $group = Sentry::findGroupById($userData['role']);
        
        $newUser->addGroup($group);
        
        return true;
    }

    public function checkIfUserExist($emailAddress)
    {
        $query = DB::table('users')->where('email', $emailAddress)->first();
        
        if ($query != null)
            return false;
        else
            return true;
    }

    /**
     * This function will delete multiple users
     *
     * @param
     *            $userIds
     */
    public function deleteMultipleUser($userIds)
    {
        // deleting user
        try {
            DB::beginTransaction(); // start the DB transaction
            
            foreach ($userIds as $key => $id) {
                if ($id == 1)
                    SentryHelper::setMessage('Super user with id 1 cannot be deleted.', 'warning');
                else {
                    $user = Sentry::findUserById($id);
                    $user->delete();
                    DB::table('user_details')->where('user_id', $id)->delete();
                    DB::table('users_groups')->where('user_id', $id)->delete();
                }
            }
            
            DB::commit(); // commit the DB transaction
        } catch (\Exception $e) {
            DB::rollback(); // something went wrong
        }
        
        SentryHelper::setMessage('Users deleted.');
    }

    /**
     * Validating the email address from the O Auth data to check if this domain is allowed to create users.
     * @param unknown $email
     * @return boolean
     */
    public function validateOAuthAllowedDomains($email)
    {
        $allowedDomains = Config::get('packages/l4mod/sentryuser/sentryuser.o-auth-domain'); // fetching the config
        $domain = explode('@', $email);
        $domain = $domain[1]; // the second key will have the email address
        
        if (in_array($domain, $allowedDomains)) {
            return true;
        } else {
            return false;
        }
    }
    
    public function handleOAuthLogin($OAuthData)
    {
        if (!$this->checkIfUserExist($OAuthData['email'])) {
            // user should login
            $user = Sentry::findUserByLogin($OAuthData['email']); // get the sentry user object
            Sentry::login($user, true); // log in the user using sentry
            
            // calling the event of setting user session
            $subscriber = new SentryuserEventHandler();
            Event::subscribe($subscriber);
            Event::fire('sentryuser.login', array($user, $OAuthData));
        } 
        else {
            // creating the user
            $newUser = Sentry::createUser(array(
                'email' => $OAuthData['email'],
                'password' => time() . rand(0, 99),
                'activated' => true,
                'first_name' => ($OAuthData['given_name']) ? $OAuthData['given_name'] : "",
                'last_name' => ($OAuthData['family_name']) ? $OAuthData['family_name'] : "",
            ));
            
            // insert extra details about the user
            DB::table('user_details')->insert(array(
                'user_id' => $newUser->id,
                'user_type' => 'o-auth',
                'oauthid' => $OAuthData['id'],
                'oauth_link' => (isset($OAuthData['link'])) ? $OAuthData['link'] : "",
                'oauth_pic' => (isset($OAuthData['picture'])) ? $OAuthData['picture'] : "",
                'gender' => (isset($OAuthData['gender'])) ? $OAuthData['gender'] : "",
                'locale' => (isset($OAuthData['locale'])) ? $OAuthData['locale'] : "",
            ));
            
            // assign the group to the user
            $group = Sentry::findGroupById(3); // authenticated user group
            $newUser->addGroup($group);
            
            // login in the user
            $user = Sentry::findUserById($newUser->id); // get the sentry user object
            Sentry::login($user, true); // log in the user using sentry
            
            // calling the event of setting user session
            $subscriber = new SentryuserEventHandler();
            Event::subscribe($subscriber);
            Event::fire('sentryuser.login', array($user, $OAuthData));
            
            SentryHelper::setMessage('Welcome to Focalworks Intranet', 'success');
            return true;
        }
    }
    
    public function updateOAuthProfileData($uid, $OAuthData)
    {
        DB::table('user_details')->where('user_id', $uid)->update(array(
                'oauth_pic' => (isset($OAuthData['picture'])) ? $OAuthData['picture'] : "",
            ));
    }
}