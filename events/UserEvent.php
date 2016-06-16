<?php
/*
 * This file is part of a LNCH Yii2 Extension 
 *
 * (c) LNCH UK Group <http://www.lnch.co.uk/licencing>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace lnch\users\events;

use yii\base\Event;

use lnch\users\models\User;

/**
 * @property User $user
 * @author Tom Lynch <tom@lnch.co.uk>
 */
class UserEvent extends Event
{
    /**
     * @var User
     */
    private $_user;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @param User $form
     */
    public function setUser(User $user)
    {
        $this->_user = $user;
    }
}