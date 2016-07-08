<?php 

namespace lnch\users\models;

use Yii;

use yii\web\User as WebUser;

class BaseUser extends WebUser 
{
	public function can( $operation, $params = [], $allowCaching = true ) 
	{
		if(Yii::$app->user->isGuest)
		{
			return false;
		}
 
        // Does the operation appear in the session?
        return ( new PermissionsManager() )->has( $operation );
    } 
}