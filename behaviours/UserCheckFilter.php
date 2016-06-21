<?php

namespace lnch\users\behaviours;

use Yii;

use yii\base\ActionFilter;

use lnch\users\models\User;

/**
 * Description of TradeAccessFilter
 *
 * @author richard.cross@romo.com
 * @copyright (c)16-Nov-2015 Romo Ltd. All rights Reserved.
 */
class UserCheckFilter extends ActionFilter
{
    public function beforeAction( $action )
    {
		// if(Yii::$app->website->is_trade) 
  //       {
  //           if( !Yii::$app->user->isLoggedIn() ) {
  //               Yii::$app->user->loginRequired();
  //               return false;

  //           } elseif( Yii::$app->user->customer->is_trade !== 'A' ) {
  //               Yii::$app->user->logout();
  //           }
  //       }

        if(User::find()->count() == 0)
        {
            return $this->owner->redirect(['/user/registration/register']);
            exit;
        }

        return parent::beforeAction($action);
    }
}
