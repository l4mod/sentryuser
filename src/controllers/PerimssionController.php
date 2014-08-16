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

    /**
     * Main function to handle the three tabbed page
     * Permission assignment, Permission management and Role management
     */
    public function handlePermissionListing()
    {
        PermApi::access_check('manage_permissions');

        // creating the sentry permissin model instance
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
     * Post handler for saving permissions
     * @return mixed
     */
    public function handlePermissionSave()
    {
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
        $roleName = Input::get('role_name');

        $SentryPermission = new SentryPermission;
        $SentryPermission->addNewRole($roleName);

        return Redirect::to('user/permission/list');
    }

    public function handleRoleEdit($roleId)
    {
        if ($roleId == 1)
        {
            SentryHelper::setMessage('This role cannot be edited');
            return Redirect::to('user/permission/list');
        }

        $role = DB::table('groups')->where('id', $roleId)->first();
        $this->layout->content = View::make('sentryuser::permissions.edit-role')->with('role', $role);
    }

    public function handleRoleUpdate()
    {
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

    public function handleRoleDelete($id)
    {
        try
        {
            DB::beginTransaction(); // start the DB transaction

            $group = Sentry::findGroupById($id);
            $authenticatedGroup = Sentry::findGroupById(3);

            // super admin group cannot be deleted
            if ($id == 1 || $id == 3)
            {
                SentryHelper::setMessage('This role cannot be deleted.', 'warning');
                return Redirect::to('user/permission/list');
            }

            // assign authenticated user group
            $users = Sentry::findAllUsersInGroup($group);
            foreach ($users as $user)
            {
                $user->addGroup($authenticatedGroup);
            }

            // delete group
            $group->delete();

            // clear permission in group mapping
            DB::table('permission_in_group')->where('group_id', $id)->delete();

            DB::table('users_groups')->where('user_id', $id)->update(array(
                    'group_id' => $authenticatedGroup->id,
                ));

            DB::commit(); // commit the DB transaction

            SentryHelper::setMessage('Role deleted, all users of this role are now Authenticated users.');
            return Redirect::to('user/permission/list');
        }
        catch (\Exception $e)
        {
            DB::rollback(); // something went wrong
        }
    }
}