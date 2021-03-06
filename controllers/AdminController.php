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

use yii\base\ExitException;
use yii\base\Model;
use yii\base\Module as Module2;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

use lnch\users\behaviours\AccessRule;
use lnch\users\Finder;
use lnch\users\models\Profile;
use lnch\users\models\User;
use lnch\users\models\UserSearch;
use lnch\users\models\UserTypePermission;
use lnch\users\models\UserTypeSearch;
use lnch\users\Module;
use lnch\users\traits\AjaxValidationTrait;
use lnch\users\traits\EventTrait;

/**
 * AdminController allows you to administrate users.
 *
 * @property Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com
 */
class AdminController extends Controller
{
    use AjaxValidationTrait;
    use EventTrait;

    /**
     * Event is triggered before creating new user.
     * Triggered with \lnch\users\events\UserEvent.
     */
    const EVENT_BEFORE_CREATE = 'beforeCreate';

    /**
     * Event is triggered after creating new user.
     * Triggered with \lnch\users\events\UserEvent.
     */
    const EVENT_AFTER_CREATE = 'afterCreate';

    /**
     * Event is triggered before updating existing user.
     * Triggered with \lnch\users\events\UserEvent.
     */
    const EVENT_BEFORE_UPDATE = 'beforeUpdate';

    /**
     * Event is triggered after updating existing user.
     * Triggered with \lnch\users\events\UserEvent.
     */
    const EVENT_AFTER_UPDATE = 'afterUpdate';

    /**
     * Event is triggered before updating existing user's type.
     * Triggered with \lnch\users\events\UserEvent.
     */
    const EVENT_BEFORE_UPDATE_TYPE = 'beforeUpdateType';

    /**
     * Event is triggered after updating existing user's type.
     * Triggered with \lnch\users\events\UserEvent.
     */
    const EVENT_AFTER_UPDATE_TYPE = 'afterUpdateType';

    /**
     * Event is triggered before updating existing user's profile.
     * Triggered with \lnch\users\events\UserEvent.
     */
    const EVENT_BEFORE_PROFILE_UPDATE = 'beforeProfileUpdate';

    /**
     * Event is triggered after updating existing user's profile.
     * Triggered with \lnch\users\events\UserEvent.
     */
    const EVENT_AFTER_PROFILE_UPDATE = 'afterProfileUpdate';

    /**
     * Event is triggered before confirming existing user.
     * Triggered with \lnch\users\events\UserEvent.
     */
    const EVENT_BEFORE_CONFIRM = 'beforeConfirm';

    /**
     * Event is triggered after confirming existing user.
     * Triggered with \lnch\users\events\UserEvent.
     */
    const EVENT_AFTER_CONFIRM = 'afterConfirm';

    /**
     * Event is triggered before deleting existing user.
     * Triggered with \lnch\users\events\UserEvent.
     */
    const EVENT_BEFORE_DELETE = 'beforeDelete';

    /**
     * Event is triggered after deleting existing user.
     * Triggered with \lnch\users\events\UserEvent.
     */
    const EVENT_AFTER_DELETE = 'afterDelete';

    /**
     * Event is triggered before blocking existing user.
     * Triggered with \lnch\users\events\UserEvent.
     */
    const EVENT_BEFORE_BLOCK = 'beforeBlock';

    /**
     * Event is triggered after blocking existing user.
     * Triggered with \lnch\users\events\UserEvent.
     */
    const EVENT_AFTER_BLOCK = 'afterBlock';

    /**
     * Event is triggered before unblocking existing user.
     * Triggered with \lnch\users\events\UserEvent.
     */
    const EVENT_BEFORE_UNBLOCK = 'beforeUnblock';

    /**
     * Event is triggered after unblocking existing user.
     * Triggered with \lnch\users\events\UserEvent.
     */
    const EVENT_AFTER_UNBLOCK = 'afterUnblock';

    /** @var Finder */
    protected $finder;

    /**
     * @param string  $id
     * @param Module2 $module
     * @param Finder  $finder
     * @param array   $config
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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete'  => ['post'],
                    'confirm' => ['post'],
                    'ban'     => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        Url::remember('', 'actions-redirect');
        $searchModel  = Yii::createObject(UserSearch::className());
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    /** 
     * Lists all user types
     *
     * @return mixed
     */
    public function actionTypes()
    {
    	$searchModel  = Yii::createObject(UserTypeSearch::className());
        $dataProvider = $searchModel->search(Yii::$app->request->get());
    	return $this->render('types', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
    	]);
    }

    public function actionTypeAlias()
    {
        // Check if there is an Editable ajax request
        if(isset($_POST['hasEditable'])) 
        {
            // Use Yii's response format to encode output as JSON
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            
            $data = Yii::$app->request->post('UserType');

            $type = $this->finder->findUserTypeById($data['type_id']);
            $type->scenario = 'manage-alias';
            
            if($type->load(Yii::$app->request->post()) && $type->save()) 
            {                
                // Return JSON encoded output in the below format
                return ['output' => $data['alias'], 'message' => ''];
            }
            else 
            {
                // Return nothing
                return ['output' => '', 'message' => ''];
            }
        }

        // Else return to rendering a normal view
        return $this->render('view', ['model' => $model]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        /** @var User $user */
        $user = Yii::createObject([
            'class'    => User::className(),
            'scenario' => 'create',
        ]);

        $event = $this->getUserEvent($user);
        
        $this->performAjaxValidation($user);
        
        $this->trigger(self::EVENT_BEFORE_CREATE, $event);
        
        if($user->load(Yii::$app->request->post()) && $user->create()) 
        {
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been created'));

            $this->trigger(self::EVENT_AFTER_CREATE, $event);
            
            return $this->redirect(['update', 'id' => $user->id]);
        }

        return $this->render('create', [
            'user' => $user,
        ]);
    }
    
    /**
     * Updates an existing User model.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        Url::remember('', 'actions-redirect');

        $user = $this->findModel($id);
        $user->scenario = 'update';
        
        $event = $this->getUserEvent($user);
        
        $this->performAjaxValidation($user);
        
        $this->trigger(self::EVENT_BEFORE_UPDATE, $event);
        
        if($user->load(Yii::$app->request->post()) && $user->save()) 
        {
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Account details have been updated'));

            $this->trigger(self::EVENT_AFTER_UPDATE, $event);
            
            return $this->refresh();
        }
        
        return $this->render('_account', [
            'user' => $user,
        ]);
    }

    /** 
     * AJAX handler to allow changing of the user type from the dashboard
     *
     * @return boolean Result of the process
     */
    public function actionUpdateUserType()
    {
        // Check if there is an Editable ajax request
        if(isset($_POST['hasEditable'])) 
        {
            // Use Yii's response format to encode output as JSON
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            
            $data = Yii::$app->request->post('User');

            $user = $this->findModel($data['id']);
            $user->scenario = 'manage-type';
            
            if($user->load(Yii::$app->request->post()) && $user->save()) 
            {                
                // Return JSON encoded output in the below format
                return ['output' => User::$userTypes[$data['user_type']], 'message' => ''];
            }
            else 
            {
                // Return nothing
                return ['output' => '', 'message' => ''];
            }
        }

        // Else return to rendering a normal view
        return $this->render('view', ['model' => $model]);
    }

    public function actionNewPermission()
    {
        // if(Yii::$app->request->isPjax)
        // {
            $model = new UserTypePermission();
            
            if($model->load(Yii::$app->request->post()) && $model->save())
            {

            }               
        // }

        return $this->redirect('/user/admin/types');
    }

    public function actionDeleteTypePermission()
    {
        $data = Yii::$app->request->post();
        $perm = UserTypePermission::findOne(['group' => $data['group'], 'permission' => $data['permission']]);
        $perm->delete();
    }

    /**
     * Updates an existing profile.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionUpdateProfile($id)
    {
        Url::remember('', 'actions-redirect');

        $user    = $this->findModel($id);
        $profile = $user->profile;

        if($profile == null) 
        {
            $profile = Yii::createObject(Profile::className());
            $profile->link('user', $user);
        }

        $event   = $this->getProfileEvent($profile);

        $this->performAjaxValidation($profile);
        
        $this->trigger(self::EVENT_BEFORE_PROFILE_UPDATE, $event);

        if($profile->load(Yii::$app->request->post()) && $profile->save()) 
        {
            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Profile details have been updated'));
            $this->trigger(self::EVENT_AFTER_PROFILE_UPDATE, $event);
            
            return $this->refresh();
        }

        return $this->render('_profile', [
            'user'    => $user,
            'profile' => $profile,
        ]);
    }
    
    /**
     * Shows information about user.
     *
     * @param int $id
     *
     * @return string
     */
    public function actionInfo($id)
    {
        Url::remember('', 'actions-redirect');

        $user = $this->findModel($id);

        return $this->render('_info', [
            'user' => $user,
        ]);
    }

    /**
     * Confirms the User.
     *
     * @param int $id
     *
     * @return Response
     */
    public function actionConfirm($id)
    {
        $model = $this->findModel($id);

        $event = $this->getUserEvent($model);

        $this->trigger(self::EVENT_BEFORE_CONFIRM, $event);

        $model->confirm();

        $this->trigger(self::EVENT_AFTER_CONFIRM, $event);

        Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been confirmed'));

        return $this->redirect(Url::previous('actions-redirect'));
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        if($id == Yii::$app->user->getId()) 
        {
            Yii::$app->getSession()->setFlash('danger', Yii::t('user', 'You can not remove your own account'));
        } 
        else 
        {
            $model = $this->findModel($id);
            $event = $this->getUserEvent($model);

            $this->trigger(self::EVENT_BEFORE_DELETE, $event);
            $model->delete();
            $this->trigger(self::EVENT_AFTER_DELETE, $event);

            Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been deleted'));
        }

        return $this->redirect(['index']);
    }
    
    /**
     * Bans or unbans the user.
     *
     * @param int $id
     *
     * @return Response
     */
    public function actionBan($id)
    {
        if($id == Yii::$app->user->getId()) 
        {
            Yii::$app->getSession()->setFlash('danger', Yii::t('user', 'You can not ban your own account'));
        } 
        else 
        {
            $user  = $this->findModel($id);

            $event = $this->getUserEvent($user);

            if($user->getIsBanned()) 
            {
                $this->trigger(self::EVENT_BEFORE_UNBLOCK, $event);
                $user->unblock();
                $this->trigger(self::EVENT_AFTER_UNBLOCK, $event);

                Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been unbanned'));
            } 
            else 
            {
                $this->trigger(self::EVENT_BEFORE_BLOCK, $event);
                $user->block();
                $this->trigger(self::EVENT_AFTER_BLOCK, $event);

                Yii::$app->getSession()->setFlash('success', Yii::t('user', 'User has been banned'));
            }
        }

        return $this->redirect(Url::previous('actions-redirect'));
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     *
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $user = $this->finder->findUserById($id);

        if($user === null) 
        {
            throw new NotFoundHttpException('The requested page does not exist');
        }

        return $user;
    }
}