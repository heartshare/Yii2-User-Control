<?php
/*
 * This file is part of the Dektrium project
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;

    /**
     * @var yii\web\View 					$this
     * @var dektrium\user\models\User 		$user
     * @var dektrium\user\models\Profile 	$profile
     */

?>

<?php $this->beginContent('@lnch/users/views/admin/update.php', ['user' => $user]); ?>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'wrapper' => 'col-sm-9',
        ],
    ],
]); ?>

<?= $form->field($profile, 'title'); ?>
<?= $form->field($profile, 'first_name'); ?>
<?= $form->field($profile, 'middle_names'); ?>
<?= $form->field($profile, 'surname'); ?>
<?= $form->field($profile, 'date_of_birth'); ?>
<?= $form->field($profile, 'website'); ?>
<?= $form->field($profile, 'location'); ?>
<?= $form->field($profile, 'timezone'); ?>
<?= $form->field($profile, 'job_title'); ?>
<?= $form->field($profile, 'contact_number'); ?>
<?= $form->field($profile, 'language'); ?>
<?= $form->field($profile, 'bio')->textarea(); ?>


<div class="form-group">
    <div class="col-lg-offset-3 col-lg-9">
        <?= Html::submitButton(Yii::t('user', 'Update'), ['class' => 'btn btn-block btn-success']); ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php $this->endContent(); ?>