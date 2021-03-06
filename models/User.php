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

use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\web\Application as WebApplication;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;

use lnch\users\Finder;
use lnch\users\helpers\Password;
use lnch\users\Mailer;
use lnch\users\Module;
use lnch\users\traits\ModuleTrait;

/**
 * User ActiveRecord model.
 *
 * @property bool    $isAdmin
 * @property bool    $isBlocked
 * @property bool    $isConfirmed
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
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class User extends ActiveRecord implements IdentityInterface
{
    use ModuleTrait;

    const BEFORE_CREATE   = 'beforeCreate';
    const AFTER_CREATE    = 'afterCreate';
    const BEFORE_REGISTER = 'beforeRegister';
    const AFTER_REGISTER  = 'afterRegister';

    // following constants are used on secured email changing process
    const OLD_EMAIL_CONFIRMED = 0b1;
    const NEW_EMAIL_CONFIRMED = 0b10;

    /** @var string Plain password. Used for model validation. */
    public $password;

    /** @var bool Whether to confirm the users account automatically */
    public $confirmOnCreation;

    /** @var Profile|null */
    private $_profile;

    /** @var string Default username regexp */
    public static $usernameRegexp = '/^[-a-zA-Z0-9_\.@]+$/';

    /** @var array Array of possible user types */
    public static $userTypes = [
        10 => "User",
        20 => "Moderator",
        30 => "Admin",
        40 => "Founder"
    ];

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'creation_date',
                'updatedAtAttribute' => 'last_updated',
                'value' => gmdate('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * @return Finder
     * @throws \yii\base\InvalidConfigException
     */
    protected function getFinder() 
    {
        return Yii::$container->get(Finder::className());
    }

    /**
     * @return Mailer
     * @throws \yii\base\InvalidConfigException
     */
    protected function getMailer() 
    {
        return Yii::$container->get(Mailer::className());
    }

    /**
     * @return bool Whether the user is confirmed or not.
     */
    public function getIsConfirmed()
    {
        return $this->confirmation_date != null && $this->status != 'P';
    }

    /**
     * @return bool Whether the user is blocked or not.
     */
    public function getIsBanned()
    {
        return $this->status == 'B';
    }

    /**
     * @return bool Whether the user is a moderator or not.
     */
    public function getIsModerator()
    {
        return $this->user_type >= 20;
    }

    /**
     * @return bool Whether the user is an admin or not.
     */
    public function getIsAdmin()
    {
        return $this->user_type >= 30;
    }

    /**
     * @return bool Whether the user is a founder or not.
     */
    public function getIsFounder()
    {
        return $this->user_type == 40;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne($this->module->modelMap['Profile'], ['user_id' => 'id']);
    }

    /**
     * @param Profile $profile
     */
    public function setProfile(Profile $profile)
    {
        $this->_profile = $profile;
    }

    /** @inheritdoc */
    public function getId()
    {
        return $this->getAttribute('id');
    }

    /** @inheritdoc */
    public function getAuthKey()
    {
        return $this->getAttribute('auth_key');
    }

    /** @inheritdoc */
    public static function tableName()
    {
        return '{{%lnch_users}}';
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'username'              => Yii::t('user', 'Username'),
            'email'                 => Yii::t('user', 'Email'),
            'signup_ip'             => Yii::t('user', 'Signup IP'),
            'password'              => Yii::t('user', 'Password'),
            'creation_date'         => Yii::t('user', 'Registered Date'),
            'confirmation_date'     => Yii::t('user', 'Confirmed At'),
            'status'                => Yii::t('user', 'Status'),
            'user_type'             => Yii::t('user', 'User Type'),
            'last_updated'          => Yii::t('user', 'Last Updated'),
            'last_login'            => Yii::t('user', 'Last Login'),

            'confirmOnCreation'     => Yii::t('user', 'Automatically confirm the account? No confirmation email will be sent'),
        ];
    }

    // /** @inheritdoc */
    // public function behaviors()
    // {
    //     return [
    //         TimestampBehavior::className(),
    //     ];
    // }

    /** @inheritdoc */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        return ArrayHelper::merge($scenarios, [
            'register' => ['username', 'email', 'password'],
            'create'   => ['username', 'email', 'password'],
            'update'   => ['username', 'email', 'password'],
            'settings' => ['username', 'email', 'password'],
            'manage-type' => ['user_type'],
        ]);
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            // username rules
            'usernameRequired' => ['username', 'required', 'on' => ['register', 'create', 'connect', 'update']],
            'usernameMatch'    => ['username', 'match', 'pattern' => static::$usernameRegexp],
            'usernameLength'   => ['username', 'string', 'min' => 3, 'max' => 255],
            'usernameUnique'   => ['username', 'unique', 'message' => Yii::t('user', 'This username has already been taken')],
            'usernameTrim'     => ['username', 'trim'],

            // email rules
            'emailRequired' => ['email', 'required', 'on' => ['register', 'connect', 'create', 'update']],
            'emailPattern'  => ['email', 'email'],
            'emailLength'   => ['email', 'string', 'max' => 255],
            'emailUnique'   => ['email', 'unique', 'message' => Yii::t('user', 'This email address has already been taken')],
            'emailTrim'     => ['email', 'trim'],

            // password rules
            'passwordRequired' => ['password', 'required', 'on' => ['register']],
            'passwordLength'   => ['password', 'string', 'min' => 6, 'on' => ['register', 'create']],

            // Confirm on creation
            'confirmOnCreation' => ['confirmOnCreation', 'boolean'],

            // User Type
            'userTypeRequired'  => ['user_type', 'required', 'on' => ['manage-type']],
            'userType'          => ['user_type', 'integer'],


            // Default values
        ];
    }

    /** @inheritdoc */
    public function validateAuthKey($authKey)
    {
        return $this->getAttribute('auth_key') === $authKey;
    }

    /**
     * Creates new user account. It generates password if it is not provided by user.
     *
     * @return bool
     */
    public function create()
    {
        if($this->getIsNewRecord() == false) 
        {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }

        $this->confirmation_date = gmdate("Y-m-d H:i:s");
        $this->status = "A";
        $this->password = $this->password == null ? Password::generate(8) : $this->password;
        
        $this->trigger(self::BEFORE_CREATE);

        if(!$this->save()) 
        {
            return false;
        }

        $this->mailer->sendWelcomeMessage($this, null, true);

        $this->trigger(self::AFTER_CREATE);

        return true;
    }

    /**
     * This method is used to register new user account. If Module::enableConfirmation is set true, this method
     * will generate new confirmation token and use mailer to send it to the user.
     *
     * @return bool
     */
    public function register()
    {
        if($this->getIsNewRecord() == false) 
        {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }

        $this->confirmation_date    = $this->module->enableConfirmation ? null : time();
        $this->password             = $this->module->enableGeneratingPassword ? Password::generate(8) : $this->password;
        
        $this->trigger(self::BEFORE_REGISTER);
        
        if(!$this->save()) 
        {
            return false;
        }

        if($this->module->enableConfirmation) 
        {
            /** @var Token $token */
            $token = Yii::createObject(['class' => Token::className(), 'type' => Token::TYPE_CONFIRMATION]);
            $token->link('user', $this);
        }

        $this->mailer->sendWelcomeMessage($this, isset($token) ? $token : null);
        
        $this->trigger(self::AFTER_REGISTER);
        
        return true;
    }

    /**
     * Attempts user confirmation.
     *
     * @param string $code Confirmation code.
     *
     * @return boolean
     */
    public function attemptConfirmation($code)
    {
        $token = $this->finder->findTokenByParams($this->id, $code, Token::TYPE_CONFIRMATION);

        if($token instanceof Token && !$token->isExpired) 
        {
            $token->delete();

            if( ( $success = $this->confirm() ) ) 
            {
                Yii::$app->user->login($this, $this->module->rememberFor);
                $message = Yii::t('user', 'Thank you, registration is now complete.');
                UserLog::log("confirm-success");
            } 
            else 
            {
                $message = Yii::t('user', 'Something went wrong and your account has not been confirmed.');
                UserLog::log("confirm-fail", "", $this);
            }
        } 
        else 
        {
            $success = false;
            $message = Yii::t('user', 'The confirmation link is invalid or expired. Please try requesting a new one.');
            UserLog::log("confirm-expired", "", $this);
        }

        Yii::$app->session->setFlash($success ? 'success' : 'danger', $message);

        return $success;
    }

    // /**
    //  * This method attempts changing user email. If user's "unconfirmed_email" field is empty is returns false, else if
    //  * somebody already has email that equals user's "unconfirmed_email" it returns false, otherwise returns true and
    //  * updates user's password.
    //  *
    //  * @param string $code
    //  *
    //  * @return bool
    //  * @throws \Exception
    //  */
    // public function attemptEmailChange($code)
    // {
    //     // TODO refactor method
    //     /** @var Token $token */
    //     $token = $this->finder->findToken([
    //         'user_id' => $this->id,
    //         'code'    => $code,
    //     ])->andWhere(['in', 'type', [Token::TYPE_CONFIRM_NEW_EMAIL, Token::TYPE_CONFIRM_OLD_EMAIL]])->one();
    //     if (empty($this->unconfirmed_email) || $token === null || $token->isExpired) {
    //         Yii::$app->session->setFlash('danger', Yii::t('user', 'Your confirmation token is invalid or expired'));
    //     } else {
    //         $token->delete();
    //         if (empty($this->unconfirmed_email)) {
    //             Yii::$app->session->setFlash('danger', Yii::t('user', 'An error occurred processing your request'));
    //         } elseif ($this->finder->findUser(['email' => $this->unconfirmed_email])->exists() == false) {
    //             if ($this->module->emailChangeStrategy == Module::STRATEGY_SECURE) {
    //                 switch ($token->type) {
    //                     case Token::TYPE_CONFIRM_NEW_EMAIL:
    //                         $this->flags |= self::NEW_EMAIL_CONFIRMED;
    //                         Yii::$app->session->setFlash('success', Yii::t('user', 'Awesome, almost there. Now you need to click the confirmation link sent to your old email address'));
    //                         break;
    //                     case Token::TYPE_CONFIRM_OLD_EMAIL:
    //                         $this->flags |= self::OLD_EMAIL_CONFIRMED;
    //                         Yii::$app->session->setFlash('success', Yii::t('user', 'Awesome, almost there. Now you need to click the confirmation link sent to your new email address'));
    //                         break;
    //                 }
    //             }
    //             if ($this->module->emailChangeStrategy == Module::STRATEGY_DEFAULT || ($this->flags & self::NEW_EMAIL_CONFIRMED && $this->flags & self::OLD_EMAIL_CONFIRMED)) {
    //                 $this->email = $this->unconfirmed_email;
    //                 $this->unconfirmed_email = null;
    //                 Yii::$app->session->setFlash('success', Yii::t('user', 'Your email address has been changed'));
    //             }
    //             $this->save(false);
    //         }
    //     }
    // }

    public static function manageableUserTypes()
    {
        return self::$userTypes;
    }

    /**
     * Confirms the user by setting 'confirmed_at' field to current time.
     */
    public function confirm()
    {
        return (bool)$this->updateAttributes(['confirmation_date' => gmdate('Y-m-d H:i:s'), 'status' => 'A']);
    }

    /**
     * Resets password.
     *
     * @param string $password
     *
     * @return bool
     */
    public function resetPassword($password)
    {
        return (bool)$this->updateAttributes(['password_hash' => Password::hash($password)]);
    }

    /**
     * Blocks the user by setting 'blocked_at' field to current time and regenerates auth_key.
     */
    public function block()
    {
        return (bool)$this->updateAttributes([
            // 'blocked_at' => time(),
            'auth_key'   => Yii::$app->security->generateRandomString(),
            'status'     => 'B'
        ]);
    }

    /**
     * UnBlocks the user by setting 'blocked_at' field to null.
     */
    public function unblock()
    {
        return (bool)$this->updateAttributes([
            // 'blocked_at' => null
            'status' => 'A'
        ]);
    }

    public function log($action, $message = '')
    {
        UserLog::log($action, $message);
    }

    // /**
    //  * Generates new username based on email address, or creates new username
    //  * like "emailuser1".
    //  */
    // public function generateUsername()
    // {
    //     // try to use name part of email
    //     $username = explode('@', $this->email)[0];
    //     $this->username = $username;
    //     if ($this->validate(['username'])) {
    //         return $this->username;
    //     }
    //     // valid email addresses are less restricitve than our
    //     // valid username regexp so fallback to 'user123' if needed:
    //     if (!preg_match(self::$usernameRegexp, $username)) {
    //         $username = 'user';
    //     }
    //     $this->username = $username;
    //     // generate username like "user1", "user2", etc...
    //     while (!$this->validate(['username'])) {
    //         $row = (new Query())
    //             ->from('{{%user}}')
    //             ->select('MAX(id) as id')
    //             ->one();
    //         $this->username = $username . ++$row['id'];
    //     }
    //     return $this->username;
    // }

    /** @inheritdoc */
    public function beforeSave($insert)
    {
        if($insert) 
        {
            $this->setAttribute('auth_key', Yii::$app->security->generateRandomString());
            
            if(Yii::$app instanceof WebApplication) 
            {
                $this->setAttribute('signup_ip', Yii::$app->request->userIP);
            }
        }

        if(!empty($this->password)) 
        {
            $this->setAttribute('password_hash', Password::hash($this->password));
        }

        return parent::beforeSave($insert);
    }

    /** @inheritdoc */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if($insert) 
        {
            // Create the user profile item
            if($this->_profile == null) 
            {
                $this->_profile = Yii::createObject(Profile::className());
            }

            $this->_profile->link('user', $this);
        }
    }

    /** @inheritdoc */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /** @inheritdoc */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('Method "' . __CLASS__ . '::' . __METHOD__ . '" is not implemented.');
    }
}