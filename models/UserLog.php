<?php
/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace lnch\users\models;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * UserLog ActiveRecord model.
 *
 * Database fields:
 * @property integer $id
 * @property string  $username
 * @property string  $email
 * @property string  $unconfirmed_email
 * @property string  $password_hash
 * @property string  $auth_key
 * @property integer $registration_ip
 * @property integer $confirmed_at
 * @property integer $blocked_at
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $flags
 *
 * Defined relations:
 * @property Account[] $accounts
 * @property Profile   $profile
 *
 * Dependencies:
 * @property-read Finder $finder
 * @property-read Module $module
 * @property-read Mailer $mailer
 *
 * @author Tom Lynch <tom@lnch.co.uk>
 */
class UserLog extends ActiveRecord 
{
	public static function tableName()
	{
		return '{{%lnch_user_log}}';
	}

	public function behaviors()
	{
	    return [
	        [
	            'class' => TimestampBehavior::className(),
	            'createdAtAttribute' => 'log_date',
	            'updatedAtAttribute' => false,
	            'value' => gmdate('Y-m-d H:i:s'),
	        ],
	    ];
	}

	public static function log($action, $message = '', $user = NULL)
	{
		$log = Yii::createObject(self::className());
		
		$log->action = $action;
		$log->message = $message;

		$log->session_id = Yii::$app->session->id;

		if($user)
		{
			$log->user_id = $user->id;
		}
		else if(!Yii::$app->user->isGuest)
		{
			$log->user_id = Yii::$app->user->identity->id;	
		}
		else 
		{
			$log->user_id = 0;
		}		

		$log->user_ip = Yii::$app->request->userIP;
		$log->http_user_agent = Yii::$app->request->userAgent;

		$log->save();
	}
}	