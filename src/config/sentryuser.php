<?php
/**
 * Created by PhpStorm.
 * User: amitav
 * Date: 6/8/14
 * Time: 12:54 PM
 */

return array(

    'site-title' => 'Sentry user Management',

    /**
     * This is the default picture which will be used if a user has not uploaded a user picture
     * or even if the file managed module is not available or enabled.
     */
    'default-pic' => 'packages/l4mod/sentryuser/default-user.png',

    /**
     * Set this to the master layout if the application is using one.
     */
    'master-tpl' => '',

    /**
     * Set this to a navigation template so that the top navigation menu is replaced.
     */
    'nav-tpl' => '',
    
    /**
     * Use OAuth users
     */
    'o-auth' => false,
    
    /**
     * Preferred domain for O Auth
     */
    'o-auth-domain' => array('gmail.com', 'focalworks.in'),
    
    /**
     * Set the login 
     */
    'login-tpl' => '',

);