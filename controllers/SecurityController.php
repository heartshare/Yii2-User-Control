<?php
/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace lnch\users\controllers;

use Yii;

use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

use lnch\users\Finder;
use lnch\users\models\LoginForm;
use lnch\users\models\UserLog;
use lnch\users\UserControl;
use lnch\users\traits\AjaxValidationTrait;
use lnch\users\traits\EventTrait;

/**
 * Controller that manages user authentication process.
 *
 * @property Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class SecurityController extends Controller
{
    use AjaxValidationTrait;
    use EventTrait;

    /**
     * Event is triggered before logging user in.
     * Triggered with \dektrium\user\events\FormEvent.
     */
    const EVENT_BEFORE_LOGIN = 'beforeLogin';
    
    /**
     * Event is triggered after logging user in.
     * Triggered with \dektrium\user\events\FormEvent.
     */
    const EVENT_AFTER_LOGIN = 'afterLogin';
    
    /**
     * Event is triggered before logging user out.
     * Triggered with \dektrium\user\events\UserEvent.
     */
    const EVENT_BEFORE_LOGOUT = 'beforeLogout';

    /**
     * Event is triggered after logging user out.
     * Triggered with \dektrium\user\events\UserEvent.
     */
    const EVENT_AFTER_LOGOUT = 'afterLogout';

    /** @var Finder */
    protected $finder;

    /**
     * @param string $id
     * @param Module $module
     * @param Finder $finder
     * @param array  $config
     */
    public function __construct($id, $module, Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'actions' => ['login', 'blocked'], 'roles' => ['?']],
                    ['allow' => true, 'actions' => ['login', 'logout'], 'roles' => ['@']],
                ],
            ],
        ];
    }

    /**
     * Displays the login page.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if(!Yii::$app->user->isGuest) 
        {
            $this->goHome();
        }

        /** @var LoginForm $model */
        $model = Yii::createObject(LoginForm::className());
        $event = $this->getFormEvent($model);

        $this->performAjaxValidation($model);

        $this->trigger(self::EVENT_BEFORE_LOGIN, $event);

        if($model->load(Yii::$app->getRequest()->post()) && $model->login()) 
        {
            $this->trigger(self::EVENT_AFTER_LOGIN, $event);
            UserLog::log("login-success");
            return $this->redirect(['/']);
        }

        return $this->render('login', [
            'model'  => $model,
            'module' => $this->module,
        ]);
    }

    /**
     * Logs the user out and then redirects to the homepage.
     *
     * @return Response
     */
    public function actionLogout()
    {
        $event = $this->getUserEvent(Yii::$app->user->identity);

        $this->trigger(self::EVENT_BEFORE_LOGOUT, $event);
        UserLog::log("logout");
        Yii::$app->getUser()->logout();
        $this->trigger(self::EVENT_AFTER_LOGOUT, $event);

        return $this->goHome();
    }
}