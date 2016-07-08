<?php

namespace lnch\users\models;

use Yii;

use lnch\users\models\UserTypePermission;

class PermissionsManager 
{
	/**
	 * Returns true or false to say if a user does or
	 * does not have the specified permission. 
 	 *
	 * It is used by the AccessControl and User classes
	 * in order to correctly filter out certain actions
	 * depending on whether or not the user has the 
	 * permissions for that page.
	 *
	 * @param string $permission 	The permission that
	 * 								the user is being 
	 *								tested for
	 * @return boolean		Whether the user has the
	 *						permission or not
	 *
	 */
	public function has($permission)
	{
        $group = substr($permission, 0, strpos($permission, ':'));
        $perm = substr($permission, strpos($permission, ':') + 1, strlen($permission));
        
		// Extract the user types permission
		$perm = UserTypePermission::find()
				->where(['group' => $group, 'permission' => $perm])
				->one();

		if($perm == null)
		{
			return false;
		}
		
		$minUserType = $perm->min_user_type;

		// Check core user type has permission
		if( Yii::$app->user->identity->user_type >= $minUserType ) 
		{
			return true;			
		} 
		else 
		{
			// Check if the user has individual permissions set

			// Check if a separate user group has permissions set

			return false;
		}
	}
}