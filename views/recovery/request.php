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
    
    /*
     * @var yii\web\View $this
     * @var yii\widgets\ActiveForm $form
     * @var dektrium\user\models\RecoveryForm $model
     */
    $this->title = Yii::t('user', 'Recover your password');
    $this->params['breadcrumbs'][] = $this->title;

    LoginAssets::register($this);

?>
<div class="row">
    <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">

        <div class="lnch-users-wrapper">  

            <div class="lnch-users-container">

                <div class="lnch-users-form">

                    <div class="lnch-header">
                        <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
                    </div> <!-- End .lnch-header -->

                    <div class="lnch-content" style="padding-top: 1em; padding-bottom: 0;">

                        <p class="text-center"><?php echo Yii::t('user', 'If you have forgotten your password, enter your registered email address and a link will be sent to you to reset your password.'); ?></p>

                        <?php 

                            $form = ActiveForm::begin([
                                'id'                     => 'password-recovery-form',
                                'enableAjaxValidation'   => true,
                                'enableClientValidation' => false,
                            ]); 

                            echo $form->field($model, 'email', [
                                'inputOptions' => [ 
                                    'class' => 'lnch-form-control', 
                                    'placeholder' => 'Email',
                                ],
                                'labelOptions' => [
                                    'style' => 'display: none;'
                                ]
                            ])->textInput(['autofocus' => true]);

                            echo Html::submitButton(Yii::t('user', 'Continue'), ['class' => 'btn btn-primary btn-block']);

                            ?><br><?php

                            ActiveForm::end(); 

                        ?>
                    </div> <!-- End .lnch-content -->

                </div> <!-- End .lnch-users-form -->

                <p class="text-center">
                    <?= Html::a(Yii::t('user', 'Back to Login page'), ['/user/security/login']); ?>
                </p>
            
            </div> <!-- End .lnch-users-container -->

        </div> <!-- End .lnch-users-wrapper -->

    </div>
</div>