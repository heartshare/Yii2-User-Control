<?php
/*
 * This file forms part of a Yii 2 extension from the LNCH group
 *
 * (c) LNCH Group <http://www.lnch.uk>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 *
 */

	use lnch\users\assets\AdminAssets;
    use lnch\users\models\UserTypeSearch;
    use lnch\users\models\UserTypePermission;

    use kartik\editable\Editable;

    use yii\bootstrap\Modal;
    use yii\data\ActiveDataProvider;
    use yii\grid\GridView;
    use yii\helpers\Html;
    use yii\bootstrap\ActiveForm;
    use yii\widgets\Pjax;

    /**
     * @var View $this
     * @var ActiveDataProvider $dataProvider
     * @var UserSearch $searchModel
     */
    $this->title = Yii::t('user', 'User Types');

    $this->params['breadcrumbs'][] = $this->title;
    
    AdminAssets::register($this);

?>

<?php 

    $this->beginContent('@lnch/users/views/admin/__template-types.php'); 

    echo $this->render('/_flash', [
        'module' => Yii::$app->getModule('user'),
    ]);
    
    





    ?><div class="text-right"><?php

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

    ?></div><?php

    $this->registerJs(
       '$("document").ready(function(){ 
            $("#new_permission").on("pjax:end", function() {
                $.pjax.reload({container:"#user-types-pjax"});  //Reload GridView
            });
        });'
    );





    echo "<div class='table table-responsive'>";

    Pjax::begin([
        'id' => 'user-types-pjax'
    ]);

    echo GridView::widget([
        'dataProvider'  => $dataProvider,
        // 'filterModel'   => $searchModel,
        'layout'        => "{items}\n{pager}",
        'columns'       => [
            // [
            //     'attribute' => 'type_id',
            // ],
            [
                'attribute' => 'name', 
            ],
            [
                'attribute' => 'alias',
                'format'    => 'raw',
                'value'     => function($model)
                {
                    return Editable::widget([
                        'pjaxContainerId' => 'user-types-pjax',
                        'header'    => 'Alias',
                        'name'      => 'UserType[alias]',
                        'size'      => 'md',
                        'format'    => Editable::FORMAT_LINK,
                        'placement' => 'top',
                        'displayValue' => $model->alias,
                        'value'     => $model->alias,

                        // 'inputType' => Editable::INPUT_TEXT,

                        'beforeInput' => function($form, $widget) use($model)
                        {
                            // echo $form->field($widget->model, 'type_id', ['labelOptions' => ['style' => 'display: none;']])->hiddenInput();
                            echo Html::hiddenInput('UserType[type_id]', $model->type_id);
                        },

                        'formOptions'   => [
                            'action' => ['/user/admin/type-alias']
                        ],
                        'buttonsTemplate' => '{submit}',
                    ]);
                },
                'contentOptions' => [
                    'style' => 'min-width: 150px;'
                ]
            ],
            [
                'attribute' => 'description'
            ],
            [
            	'header'	=> 'Permissions',
            	'content'	=> function($model)
            	{
            		$perms = "";

            		foreach($model->permissions as $perm)
            		{
            			$perms .= "<span class='user-type-permission'>" . $perm->group . " : " . $perm->permission . "</span>";
            		}

            		return "<div class='permissions-container'>" . $perms . "</div>";
            	},
            	'format' 	=> 'raw',
            	'contentOptions' => [
            		'style' => 'width: 45%; min-width: 200px;'
            	]	
            ]
        ]
    ]); 

    Pjax::end(); 

    echo "</div>";

    $this->endContent(); 

?>