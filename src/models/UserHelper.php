<?php

class UserHelper extends Eloquent
{
    /**
     * Pass the user id to get the user object with the required details.
     *
     * @param unknown $user_id
     * @param bool    $extra
     *
     * @return unknown|boolean
     */
    public static function getUserObj($user_id, $extra = false)
    {
        $arrSelect = array(
            'users.id', 'users.email', 'users.first_name', 'users.last_name', 'users.created_at', 'users.updated_at',
            'user_details.user_profile_img','user_details.user_type', 'user_details.oauth_pic',
            'groups.name as group_name', 'groups.id as group_id'
        );
        
        $query = DB::table('users');
        
        if ($extra == false)
            $query->select($arrSelect);
        
        $query->leftjoin('user_details', 'user_details.user_id', '=', 'users.id');
        $query->leftjoin('users_groups', 'users_groups.user_id', '=', 'users.id');
        $query->leftjoin('groups', 'groups.id', '=', 'users_groups.group_id');
        
        $query->where('users.activated', 1);
        $query->where('users.id', $user_id);
        
        $result = $query->first();
        
        if ($result != null)
            return $result;
        else 
            return false;
    }
    
    /**
     * Get the user profile picture
     */
    public static function getUserPicture()
    {
        if (Session::has('userObj'))
        {
            $userObj = Session::get('userObj');

            // first check if o-auth user profile pic is present.
            if ($userObj->oauth_pic != '')
            {
                $url = $userObj->oauth_pic;
            }

            // then check if local image is present
            elseif ($userObj->user_profile_img == "0")
            {
                $url = Config::get('sentryuser::sentryuser.default-pic');
            }

            else
            {
                $fileId = $userObj->user_profile_img;
                $url = DB::table('files_managed')->select('file_url')->where('file_id', $fileId)->pluck('file_url');
            }

            return $url;
        }
    }
    
    /**
     * 
     * @return string
     */
    public static function getUserDisplayName()
    {
        if (Session::has('userObj'))
        {
            $userObj = Session::get('userObj');
            return $userObj->first_name . ' ' . $userObj->last_name;
        }
    }
}