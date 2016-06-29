<?php
/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

    use lnch\users\assets\LoginAssets;
	
    use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	/**
	 * @var yii\web\View                   $this
	 * @var dektrium\user\models\LoginForm $model
	 * @var dektrium\user\Module           $module
	 */
	$this->title = Yii::t('user', 'Sign in');
	$this->params['breadcrumbs'][] = $this->title;

    LoginAssets::register($this);

?>

<?= $this->render('/_flash', ['module' => Yii::$app->getModule('user')]); ?>

<div class="row">

    <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">

        <div class="lnch-users-wrapper">  

            <div class="lnch-users-container">

                <div class="lnch-users-form">

                    <div class="lnch-header">
                        Sign In
                    </div> <!-- End .lnch-header -->

                    <div class="lnch-content">

                        <?php 

                            $form = ActiveForm::begin([
                                'id'                     => 'login-form',
                                'enableAjaxValidation'   => true,
                                'enableClientValidation' => false,
                                'validateOnBlur'         => false,
                                'validateOnType'         => false,
                                'validateOnChange'       => false,
                            ]); 

                            echo $form->field($model, 'login', [
                                'template' => "<div class='text-center'>{error}</div>",
                            ]);
                            echo $form->field($model, 'password', [
                                'template' => "<div class='text-center'>{error}</div>",
                            ]);

                            echo $form->field($model, 'login', [
                                'template' => "{label}\n{input}\n{hint}",
                                'inputOptions' => [
                                    'autofocus' => 'autofocus', 
                                    'class' => 'lnch-form-control', 
                                    'tabindex' => '1',
                                    'placeholder' => 'Login',
                                ],
                                'labelOptions' => [
                                    'style' => 'display: none;'
                                ]
                            ]);

                            echo $form->field($model, 'password', [
                                'template' => "{label}\n{input}\n{hint}",
                                'options' => [
                                    'style' => 'margin-bottom: 8px;'
                                ],
                                'inputOptions' => [
                                    'class' => 'lnch-form-control', 
                                    'tabindex' => '2',
                                    'placeholder' => 'Password',
                                ],
                                'labelOptions' => [
                                    'style' => 'display: none;'
                                ]
                            ])
                            ->passwordInput();

                            ?><div class="remember-me"><?php

                                echo $form->field($model, 'rememberMe')->checkbox(['tabindex' => '4']);
                            
                            ?></div><?php

                            ?><div class="forgot-password-link"><?php
                            
                                echo ($module->enablePasswordRecovery ? Html::a(Yii::t('user', 'Forgot password?'), ['/user/recovery/request'], ['tabindex' => '5']) : '');

                            ?></div><?php

                            // ->label(Yii::t('user', 'Password') . ($module->enablePasswordRecovery ? ' (' . Html::a(Yii::t('user', 'Forgot password?'), ['/user/recovery/request'], ['tabindex' => '5']) . ')' : ''));




                            echo Html::submitButton(Yii::t('user', 'Login'), ['class' => 'btn btn-success btn-block', 'tabindex' => '3']);
                            
                            

                            ActiveForm::end(); 

                        ?>

                    </div> <!-- End .lnch-content -->

                </div> <!-- End .lnch-users-form -->

                <?php if($module->enableConfirmation): ?>
                    <p class="text-center login-text">
                        <?php echo Html::a(Yii::t('user', 'Didn\'t receive confirmation message?'), ['/user/registration/resend']); ?>
                    </p>
                <?php endif; ?>

                <?php if($module->enableRegistration): ?>
                    <p class="text-center login-text">
                        <?php echo Html::a(Yii::t('user', 'Don\'t have an account? Sign up!'), ['/user/registration/register']); ?>
                    </p>
                <?php endif; ?>

            </div> <!-- End .lnch-users-container -->

        </div> <!-- End .lnch-users-wrapper -->

    </div> <!-- End .col-md-4 .col-md-offset-4 .col-sm-6 .col-sm-offset-3 -->

</div> <!-- End .row -->