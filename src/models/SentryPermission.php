<?php

/**
 * Created by PhpStorm.
 * User: Amitav Roy
 * Date: 7/22/14
 * Time: 10:39 PM
 */
class SentryPermission extends Eloquent
{

    /**
     * This function will query the permission_in_group table and pass the data
     * to render the permission table with check boxes.
     * 
     * @return unknown
     */
    public function getPermissionData()
    {
        $query = DB::table('permission_in_group');
        $query->join('permissions', 'permissions.permission_id', '=', 'permission_in_group.permission_id');
        $query->join('groups', 'groups.id', '=', 'permission_in_group.group_id');
        $query->orderBy('permissions.permission_group_name', 'asc');
        $query->orderBy('permissions.permission_name', 'asc');
        $data = $query->get();
        
        return $data;
    }

    /**
     * Fetcht the permission data and format it for the permission table view.
     * 
     * @return Ambigous <multitype:, unknown>
     */
    public function formatPermissionData()
    {
        $data = $this->getPermissionData();
        
        $permissions = array();
        foreach ($data as $row) {
            // unsanitize the permission name for display purpose
            $row->permission_name = $this->unsanitizePermissionName($row->permission_name);
            $permissions[$row->permission_name][] = $row;
        }
        
        return $permissions;
    }

    /**
     * This function will format the groups from the groups table.
     * 
     * @return multitype:unknown
     */
    public function getPermissionTableGroups()
    {
        $groups_temp = DB::table('groups')->get();
        $groups = array();
        foreach ($groups_temp as $row) {
            $groups[$row->id] = $row;
        }
        
        return $groups;
    }

    /**
     * This function is updating the user in group table for mapping purpose.
     *
     * @param unknown $postData
     * @return bool
     */
    public function updatePermissionMapping($postData)
    {
        foreach ($postData as $key => $value) {
            $arrayCheck = explode('|', $key);
            
            // getting only the hidden values
            if (count($arrayCheck) == 3) {
                $arrPermission = explode('|', $value);
                $ping_id = $arrPermission[3];
                $this->updateGroupPermission($ping_id);
                $data = $this->matchHiddenAndActualPost($key, $postData);
                
                DB::beginTransaction(); // start the DB transaction
                try {
                    $query = DB::table('permission_in_group');
                    $query->where('ping_id', $ping_id);
                    $query->update($data);
                    
                    $this->updateGroupPermission($ping_id);
                    DB::commit(); // commit the DB transaction
                } catch (\Exception $e) {
                    DB::rollback(); // something went wrong
                }
            }
        }
        
        return true;
    }

    /**
     * This is where I am updating the Sentry group.
     * 
     * @param unknown $ping_id            
     */
    private function updateGroupPermission($ping_id)
    {
        $query = DB::table('permission_in_group')->select('permissions.permission_name', 'permission_in_group.allow', 'permission_in_group.group_id');
        $query->where('ping_id', $ping_id);
        $result = $query->join('permissions', 'permissions.permission_id', '=', 'permission_in_group.permission_id')->first();
        
        $permission_name = $result->permission_name;
        $permission_allow = $result->allow;
        $group_id = $result->group_id;
        
        $group = Sentry::findGroupById($group_id);
        $group->permissions = array(
            $permission_name => $permission_allow
        );
        
        $group->save();
    }

    /**
     * This function is checking whether the hidden value and the checked value is present.
     *
     * If the checked value is not present, then the table row is updated as allow 0 else 1.
     * 
     * @param unknown $hidden            
     * @param unknown $postData            
     * @return the data to be updated on the mapping table
     */
    private function matchHiddenAndActualPost($hidden, $postData)
    {
        $arrHiddenKey = explode('|', $hidden);
        $arrHiddenData = explode('|', $postData[$hidden]);
        $strActualKey = $arrHiddenKey[0] . '|' . $arrHiddenKey[1];
        
        if (! isset($postData[$strActualKey])) {
            // unchecked so the data should have allow as 0
            $data = array(
                'permission_id' => $arrHiddenData[0],
                'group_id' => $arrHiddenData[1],
                'allow' => 0
            );
        } else {
            // checked so the data should have allow as 1
            
            $data = array(
                'permission_id' => $arrHiddenData[0],
                'group_id' => $arrHiddenData[1],
                'allow' => 1
            );
        }
        
        return $data;
    }

    public function addPermission($postData)
    {
        $permission_name = $this->sanitizePermissionName($postData['permission_name']);
        
        // checking if a permission with the same name is already present.
        if (! $this->checkPermissionExist($permission_name)) {
            GlobalHelper::setMessage('A permission with the same name already exist.', 'warning');
            return false;
        } else {
            DB::beginTransaction(); // start the DB transaction
            try {
                $permission_id = DB::table('permissions')->insertGetId(array(
                    'permission_name' => $permission_name,
                    'permission_group_name' => 'Users'
                ));
                
                // updating the permission group mapping table
                DB::table('permission_in_group')->insert(array(
                    'permission_id' => $permission_id,
                    'group_id' => 1,
                    'allow' => 1
                ));
                
                // once added in permission table, Super admin should be assigned the permission.
                $group = Sentry::findGroupById(1);
                $group_perm_array = $group->permissions;
                $group_perm_array = array_merge($group_perm_array, array(
                    $permission_name => 1
                ));
                $group->permissions = $group_perm_array;
                $group->save();
                
                // adding zero to all other groups
                $groups = $this->getPermissionTableGroups();
                foreach ($groups as $group) {
                    if ($group->id != 1) {
                        DB::table('permission_in_group')->insert(array(
                            'permission_id' => $permission_id,
                            'group_id' => $group->id,
                            'allow' => 0
                        ));
                    }
                }
                
                GlobalHelper::setMessage('A new permission has been added.');
                DB::commit(); // commit the DB transaction
                
                return true;
            } catch (\Exception $e) {
                DB::rollback(); // something went wrong
            }
        }
    }

    /**
     * This function will do some basic string correct to make proper permission name.
     * 
     * @param unknown $name            
     * @return unknown
     */
    private function sanitizePermissionName($name)
    {
        // checking for space and replace it with underscore
        $name = str_replace(' ', '_', $name);
        
        // lower case the full string
        $name = strtolower($name);
        
        return $name;
    }

    private function unsanitizePermissionName($name)
    {
        $name = str_replace('_', ' ', $name);
        return $name;
    }

    /**
     * This function is querying the database table to check if the permission already exist.
     * 
     * @param unknown $permission_name            
     * @return boolean
     */
    private function checkPermissionExist($permission_name)
    {
        $query = DB::table('permissions')->where('permission_name', $permission_name)->first();
        
        if ($query != null)
            return false;
        else
            return true;
    }
}