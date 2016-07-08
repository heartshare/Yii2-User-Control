<?php 

    use yii\bootstrap\ActiveForm;
    use yii\bootstrap\Modal;
    use yii\helpers\Html;
    use yii\widgets\Pjax;

    use lnch\users\models\UserTypePermission;

?>

<div class="text-right">
    <?php

        Modal::begin([
            'header' => 'Add a new permission',
            'toggleButton' => [
                'label' => 'New Permission',
                'class' => 'btn btn-primary',
                'style' => 'margin-bottom: 16px;'
            ],
        ]);

        Pjax::begin([
            'id' => 'new_permission',
            'timeout' => 0,
            'enablePushState' => false
        ]);

        $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'options' => [
                'data-pjax' => true 
            ],
            'action' => '/user/admin/new-permission',
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'wrapper' => 'col-sm-8',
                ],
            ],
        ]);
        
        $model = new UserTypePermission();

        echo "<p style='margin-bottom: 32px;'>To add a new user permission, please fill in the form below. 
        All user types with a higher access level than the the type chosen below will also get the new permission.</p>";

        echo $form->field($model, 'group')->textInput(['maxlength' => 200]);
        echo $form->field($model, 'permission')->textInput(['maxlength' => 200]);
        echo $form->field($model, 'min_user_type')->dropDownList([
            10 => 'User',
            20 => 'Moderator',
            30 => 'Administrator',
            40 => 'Founder'
        ]);
         
        ?><div class="form-group">
            <div class="col-lg-offset-3 col-sm-8 text-right"><?php
                echo Html::submitButton(Yii::t('app', 'Update'), [
                    'class' => 'btn btn-success pjax-submit',
                    'data' => [
                        'confirm' => 'Are you sure you want to add this permission? It cannot be removed later.'
                    ]
                ]);
            ?></div>
        </div><?php

        ActiveForm::end();
            
        Pjax::end();

        Modal::end();

    ?>
</div>