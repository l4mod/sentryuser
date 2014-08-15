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
    public function validateCheckemailexist($attribute, $value, $parameters)
    {
        $SentryUser = new \SentryUser;
        if ($SentryUser->checkIfUserExist($value))
            return true;
        else
            return false;
    }

    public function validateMatchpass($attribute, $value, $parameters)
    {
        $cPassword = $parameters[0];
        if ($cPassword == $value)
            return $value == $cPassword;
    }
}