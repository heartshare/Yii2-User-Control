<?php
/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace lnch\users;

use yii\authclient\ClientInterface;
use yii\base\Object;
use yii\db\ActiveQuery;

/**
 * Finder provides some useful methods for finding active record models.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Finder extends Object
{
    /** @var ActiveQuery */
    protected $userQuery;

    /** @var ActiveQuery */
    protected $tokenQuery;

    /** @var ActiveQuery */
    protected $profileQuery;

    /** @var ActiveQuery */
    protected $userTypeQuery;

    /**
     * @return ActiveQuery
     */
    public function getUserQuery()
    {
        return $this->userQuery;
    }

    /**
     * @return ActiveQuery
     */
    public function getTokenQuery()
    {
        return $this->tokenQuery;
    }

    /**
     * @return ActiveQuery
     */
    public function getProfileQuery()
    {
        return $this->profileQuery;
    }

    /**
     * @return ActiveQuery
     */
    public function getUserTypeQuery()
    {
        return $this->userTypeQuery;
    }

    /** @param ActiveQuery $userQuery */
    public function setUserQuery(ActiveQuery $userQuery)
    {
        $this->userQuery = $userQuery;
    }

    /** @param ActiveQuery $tokenQuery */
    public function setTokenQuery(ActiveQuery $tokenQuery)
    {
        $this->tokenQuery = $tokenQuery;
    }

    /** @param ActiveQuery $profileQuery */
    public function setProfileQuery(ActiveQuery $profileQuery)
    {
        $this->profileQuery = $profileQuery;
    }

    /** @param ActiveQuery $userTypeQuery */
    public function setUserTypeQuery(ActiveQuery $userTypeQuery)
    {
        $this->userTypeQuery = $userTypeQuery;
    }

    /**
     * Finds a user by the given id.
     *
     * @param int $id User id to be used on search.
     *
     * @return models\User
     */
    public function findUserById($id)
    {
        return $this->findUser(['id' => $id])->one();
    }

    /**
     * Finds a user by the given username.
     *
     * @param string $username Username to be used on search.
     *
     * @return models\User
     */
    public function findUserByUsername($username)
    {
        return $this->findUser(['username' => $username])->one();
    }

    /**
     * Finds a user by the given email.
     *
     * @param string $email Email to be used on search.
     *
     * @return models\User
     */
    public function findUserByEmail($email)
    {
        return $this->findUser(['email' => $email])->one();
    }

    /**
     * Finds a user by the given username or email.
     *
     * @param string $usernameOrEmail Username or email to be used on search.
     *
     * @return models\User
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        if(filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) 
        {
            return $this->findUserByEmail($usernameOrEmail);
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    /**
     * Finds a user by the given condition.
     *
     * @param mixed $condition Condition to be used on search.
     *
     * @return \yii\db\ActiveQuery
     */
    public function findUser($condition)
    {
        return $this->userQuery->where($condition);
    }

    /**
     * Finds a token by user id and code.
     *
     * @param mixed $condition
     *
     * @return ActiveQuery
     */
    public function findToken($condition)
    {
        return $this->tokenQuery->where($condition);
    }

    /**
     * Finds a token by params.
     *
     * @param integer $userId
     * @param string  $code
     * @param integer $type
     *
     * @return Token
     */
    public function findTokenByParams($userId, $code, $type)
    {
        return $this->findToken([
            'user_id' => $userId,
            'code'    => $code,
            'type'    => $type,
        ])->one();
    }

    /**
     * Finds a profile by user id.
     *
     * @param int $id
     *
     * @return null|models\Profile
     */
    public function findProfileById($id)
    {
        return $this->findProfile(['user_id' => $id])->one();
    }

    /**
     * Finds a profile.
     *
     * @param mixed $condition
     *
     * @return \yii\db\ActiveQuery
     */
    public function findProfile($condition)
    {
        return $this->profileQuery->where($condition);
    }

    /**
     * Finds a user type by id.
     *
     * @param int $id
     *
     * @return null|models\Profile
     */
    public function findUserTypeById($id)
    {
        return $this->findUserType(['type_id' => $id])->one();
    }

    /**
     * Finds a user type.
     *
     * @param mixed $condition
     *
     * @return \yii\db\ActiveQuery
     */
    public function findUserType($condition)
    {
        return $this->userTypeQuery->where($condition);
    }
}