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

use yii\base\Model;

use lnch\users\Finder;
use lnch\users\helpers\Password;
use lnch\users\traits\ModuleTrait;

/**
 * LoginForm get user's login and password, validates them and logs the user in. If user has been blocked, it adds
 * an error to login form.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class LoginForm extends Model
{
    use ModuleTrait;

    /** @var string User's email or username */
    public $login;

    /** @var string User's plain password */
    public $password;

    /** @var string Whether to remember the user */
    public $rememberMe = false;

    /** @var \dektrium\user\models\User */
    protected $user;

    /** @var Finder */
    protected $finder;

    /**
     * @param Finder $finder
     * @param array  $config
     */
    public function __construct(Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($config);
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'login'      => Yii::t('user', 'Login'),
            'password'   => Yii::t('user', 'Password'),
            'rememberMe' => Yii::t('user', 'Remember me next time'),
        ];
    }

    /** @inheritdoc */
    public function rules()
    {
        return [

            'requiredFields' => [
                ['login', 'password'], 'required'
            ],

            'loginTrim' => [
                ['login'], 'trim'
            ],

            'passwordValidate' => [
                'password',
                function ($attribute) {
                    if($this->user === null || !Password::validate($this->password, $this->user->password_hash)) 
                    {
                        $this->addError($attribute, Yii::t('user', 'Invalid login or password'));
                    }
                }
            ],

            'confirmationValidate' => [
                'login',
                function ($attribute) {
                    if($this->user !== null) 
                    {
                        $confirmationRequired = $this->module->enableConfirmation && !$this->module->enableUnconfirmedLogin;

                        if($confirmationRequired && !$this->user->getIsConfirmed()) 
                        {
                            $this->addError($attribute, Yii::t('user', 'It looks like you need to confirm your email address'));
                        }
                        
                        if($this->user->getIsBanned()) 
                        {
                            $this->addError($attribute, Yii::t('user', 'Your account has been banned.'));
                        }
                    }
                }
            ],

            'rememberMe' => [
                ['rememberMe'], 'boolean'
            ],

        ];
    }

    /**
     * Validates form and logs the user in.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if($this->validate()) 
        {
            if(Yii::$app->getUser()->login($this->user, $this->rememberMe ? $this->module->rememberFor : 0))
            {
                UserLog::log("login-success");
                $this->user->last_login = gmdate("Y-m-d H:i:s");
                $this->user->save();
                return true;
            }
            else 
            {
                return false;
            }
        } 
        else 
        {
            $message = '';

            foreach($this->errors['user'] as $error)
            {
                $message .= $error . "\n";
            }

            UserLog::log("login-failure", $message);
            return false;
        }
    }

    /** @inheritdoc */
    public function formName()
    {
        return 'login-form';
    }

    /** @inheritdoc */
    public function beforeValidate()
    {
        if(parent::beforeValidate()) 
        {
            // Load the user before the validation takes place
            $this->user = $this->finder->findUserByUsernameOrEmail(trim($this->login));
            return true;
        } 
        else 
        {
            return false;
        }
    }
}