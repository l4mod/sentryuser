<?php
use Illuminate\Support\Facades\Redirect;
/**
 * Created by PhpStorm.
 * User: Amitav Roy
 * Date: 7/19/14
 * Time: 4:09 PM
 */

class PermissionController extends BaseController
{
    /**
     * Defining the master layout.
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
     * Main function to handle the three tabbed page
     * Permission assignment, Permission management and Role management.
     */
    public function handlePermissionListing()
    {
        // checking if the user has access to the permission
        PermApi::access_check('manage_permissions');

        // creating the sentry permission model instance
        $SentryPermission = new SentryPermission;

        // fetch the permission table data in array format
        $permissions = $SentryPermission->formatPermissionData();

        // get the groups data from groups table in array format
        $groups = $SentryPermission->getPermissionTableGroups();

        $this->layout->content = View::make('sentryuser::permissions.permission-list')
            ->with('groups', $groups)
            ->with('permissions', $permissions);
    }

    /**
     * Post handler for saving permissions.
     * @return mixed
     */
    public function handlePermissionSave()
    {
        PermApi::access_check('manage_permissions');

        $postData = Input::all();
        $SentryPermission = new SentryPermission;

        if ($SentryPermission->updatePermissionMapping($postData))
        {
            SentryHelper::setMessage('Permissions have been updated.');
            return Redirect::to('user/permission/list');
        }
        else 
        {
            SentryHelper::setMessage('Not updated because of some problems.', 'warning');
            return Redirect::to('user/permission/list');
        }
    }

    /**
     * Post handler for Adding permissions.
     * @return mixed
     */
    public function handlePermissionAdd()
    {
        PermApi::access_check('manage_permissions');

        $SentryPermission = new SentryPermission;
        $SentryPermission->addPermission(Input::all());
        
        return Redirect::to('user/permission/list');
    }

    /**
     * Post handler for adding new role.
     * @return mixed
     */
    public function handleRoleAdd()
    {
        PermApi::access_check('manage_permissions');

        $roleName = Input::get('role_name');

        $SentryPermission = new SentryPermission;
        $SentryPermission->addNewRole($roleName);

        return Redirect::to('user/permission/list');
    }

    /**
     * Handling the form for role edit.
     * @param $roleId
     * @return mixed
     */
    public function handleRoleEdit($roleId)
    {
        PermApi::access_check('manage_permissions');

        if ($roleId == 1)
        {
            SentryHelper::setMessage('This role cannot be edited');
            return Redirect::to('user/permission/list');
        }

        $role = DB::table('groups')->where('id', $roleId)->first();
        $this->layout->content = View::make('sentryuser::permissions.edit-role')->with('role', $role);
    }

    /**
     * Handle the role delete. Need to check
     * @return mixed
     */
    public function handleRoleUpdate()
    {
        PermApi::access_check('manage_permissions');

        $roleName = Input::get('role');
        $roleId = Input::get('roleId');

        $SentryPermission = new SentryPermission;
        if ($SentryPermission->updateRole($roleId, $roleName))
        {
            SentryHelper::setMessage('Role updated');
        }
        else
        {
            SentryHelper::setMessage('Role not updated', 'warning');
        }

        return Redirect::to('user/role/edit/' . $roleId);
    }
}