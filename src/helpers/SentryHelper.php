<?php

class SentryHelper {

    /**
     * This function will be used to spit out the variable dump.
     * It takes two parameters, one is the array / variable
     * and the second is flag for exit.
     * @param  array  $var  [array or variable]
     * @param  boolean $exit [should continue or exit]
     * @return none
     */
    public static function dsm($var, $exit = false)
    {
        print '<pre>';
        print_r($var);
        print '</pre>';

        if ($exit ===  true)
            exit;
    }

    /**
     * This function will set the message in session so that when the page renders,
     * we can display a message on top of the page.
     * @param $message
     * @param string $flag
     */
    public static function setMessage($message, $flag = 'info')
    {
        $tempMessage = '';
        if (Session::get('message'))
            $tempMessage = Session::get('message');

        if ($tempMessage == "")
            $tempMessage = $message;
        else
            $tempMessage = $tempMessage . '<br />' . $message;

        Session::flash('message', $tempMessage);
        Session::flash('message-flag', $flag);
    }
}