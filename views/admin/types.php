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

    
    if(Yii::$app->user->has("users:create-permission"))
    {
        echo $this->render("_new-permission");

        $this->registerJs(
           '$("document").ready(function(){ 
                $("#new_permission").on("pjax:end", function() {
                    $.pjax.reload({container:"#user-types-pjax"});  //Reload GridView
                });
            });'
        );
    }


    echo "<div class='table table-responsive'>";

    Pjax::begin([
        'id' => 'user-types-pjax'
    ]);

    echo GridView::widget([
        'dataProvider'  => $dataProvider,
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
            			$perms .= "<span class='user-type-permission'>" 
                                    . $perm->group . " : " . $perm->permission;

                        if(Yii::$app->user->identity->isFounder)
                        {
                            $perms .= "<span class='founder-delete-permission' 
                                            data-group='".$perm->group."' 
                                            data-permission='".$perm->permission."'
                                            data-confirm='Are you sure?'>Delete</span>";
                        }
                        
                        $perms .= "</span>";
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

    $this->registerJs(
       '$("document").ready(function(){ 
            
            $("body").on("click", ".founder-delete-permission", function() {
                
                var group = $(this).data("group");
                var permission = $(this).data("permission");

                var okCallback = function()
                {
                    $.ajax({
                        type: "POST",
                        url: "/user/admin/delete-type-permission",
                        data: {
                            group: group,
                            permission: permission
                        },
                        success: function()
                        {
                            $.pjax.reload({container:"#user-types-pjax"});  //Reload GridView
                        },
                        error: function(xhr){
                            alert("An error occured: " + xhr.status + " " + xhr.statusText);
                        },
                    });
                }

                var cancelCallback = function() 
                {
                    
                }

                var options = {
                    confirmButtonText:  "Delete",
                    confirmButtonColor: "#c95c5c",

                    cancelButtonText:   "Keep",

                    html: true,
                    showLoaderOnConfirm: true,
                    closeOnConfirm: true
                }

                yii.confirm("Are you sure you want to delete the <strong>"+group+":"+permission+"</strong> permission from the system? This cannot be undone.", 
                    okCallback, cancelCallback, options);

            });

        });'
    );

    $this->endContent(); 

?>