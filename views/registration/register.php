
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
	 * @var yii\web\View              $this
	 * @var lnch\users\models\User $user
	 * @var lnch\users\UserControl      $module
	 */
	$this->title = Yii::t('user', 'Sign up');
	$this->params['breadcrumbs'][] = $this->title;

    LoginAssets::register($this);

?>

<div class="row">

    <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">

        <div class="lnch-users-wrapper">  

            <div class="lnch-users-container">

                <div class="lnch-users-form">

                    <div class="lnch-header">
                        <h3 class="panel-title"><?= Html::encode($this->title); ?></h3>
                    </div> <!-- End .lnch-header -->

                    <div class="lnch-content" style="padding-top: 1em;">

                        <p class="text-center">
                            <?php echo Yii::t('user', 'Fill in the fields below to create an account'); ?><!-- 
                            --><?php 
                                if($module->enableGeneratingPassword == true)
                                {
                                    echo ". ";
                                    echo Yii::t('user', 'Your password will be randomly generated and emailed to you.'); 
                                }
                            ?>
                        </p>

                        <?php 

                            $form = ActiveForm::begin([
                                'id'                     => 'registration-form',
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
                            ]);

                            echo $form->field($model, 'username', [
                                'inputOptions' => [ 
                                    'class' => 'lnch-form-control', 
                                    'placeholder' => 'Username',
                                ],
                                'labelOptions' => [
                                    'style' => 'display: none;'
                                ]
                            ]);

                            if($module->enableGeneratingPassword == false) 
                            {
                                echo $form->field($model, 'password', [
                                    'inputOptions' => [ 
                                        'class' => 'lnch-form-control', 
                                        'placeholder' => 'Password',
                                    ],
                                    'labelOptions' => [
                                        'style' => 'display: none;'
                                    ]
                                ])->passwordInput();
                            }

                            echo Html::submitButton(Yii::t('user', 'Sign up'), ['class' => 'lnch-btn lnch-btn-success lnch-btn-block']); 

                            ActiveForm::end(); 

                        ?>

                    </div> <!-- End .lnch-content -->

                </div> <!-- End .lnch-users-form -->

                <p class="text-center">
                    <?= Html::a(Yii::t('user', 'Already registered? Sign in!'), ['/user/security/login']); ?>
                </p>

            </div> <!-- End .lnch-users-container -->

        </div> <!-- End .lnch-users-wrapper -->

    </div>

</div>