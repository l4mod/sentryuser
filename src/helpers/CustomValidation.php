<?php namespace Amitavroy\Sentryuser;
/**
 * Created by PhpStorm.
 * User: amitav
 * Date: 15/8/14
 * Time: 9:40 AM
 */

use Illuminate\Validation\Validator;

class CustomValidation extends Validator
{
    /**
     * This function is checking if the email address is already associated with any user.
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateCheckemailexist($attribute, $value, $parameters)
    {
        $SentryUser = new \SentryUser;
        if ($SentryUser->checkIfUserExist($value))
            return true;
        else
            return false;
    }

    /**
     * This function is checking if the password and confirm password is matching or not.
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateMatchpass($attribute, $value, $parameters)
    {
        $cPassword = $parameters[0];
        if ($cPassword == $value)
            return $value == $cPassword;
    }
}