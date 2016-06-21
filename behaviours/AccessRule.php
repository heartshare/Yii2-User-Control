<?php
/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace lnch\users\behaviours;

/**
 * Access rule class for simpler RBAC.
 * @see http://yii2-user.dmeroff.ru/docs/custom-access-control
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class AccessRule extends \yii\filters\AccessRule
{
    /**
     * @inheritdoc
     * */
    protected function matchRole($user)
    {
        if(empty($this->roles)) 
        {
            return true;
        }

        foreach($this->roles as $role) 
        {
            if($role === '?') 
            {
                if(\Yii::$app->user->isGuest) 
                {
                    return true;
                }
            } 
            elseif ($role === '@') 
            {
                if(!\Yii::$app->user->isGuest) 
                {
                    return true;
                }
            } 
            elseif ($role === 'mod' || $role === 'moderator') 
            {
                if(!\Yii::$app->user->isGuest && \Yii::$app->user->identity->isModerator) 
                {
                    return true;
                }
            } 
            elseif ($role === 'admin' || $role === 'administrator') 
            {
                if(!\Yii::$app->user->isGuest && \Yii::$app->user->identity->isAdmin) 
                {
                    return true;
                }
            } 
            elseif ($role === 'founder') 
            {
                if(!\Yii::$app->user->isGuest && \Yii::$app->user->identity->isFounder) 
                {
                    return true;
                }
            }
        }

        return false;
    }
}