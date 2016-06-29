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
    use lnch\users\models\User;
    use lnch\users\models\UserSearch;

    use kartik\editable\Editable;

    // use yii\data\ActiveDataProvider;
    use yii\grid\GridView;
    use yii\helpers\Html;
    // use yii\jui\DatePicker;
    // use yii\web\View;
    use yii\widgets\Pjax;

    AdminAssets::register($this);

?>

<?php 
    
    Pjax::begin();

    echo GridView::widget([
        'dataProvider'  => $dataProvider,
        'filterModel'   => $searchModel,
        'layout'        => "{items}\n{pager}",
        'columns' => [

            // Username, links through to view profile
            [
                'attribute' => 'username',
                'value'     => function($model) 
                {
                    return Html::a($model->username, ['/user/profile/show/' . $model->id]);
                },
                'format'    => 'html'
            ],

            // Email, with mailto link
            [
                'attribute' => 'email',
                'value'     => function($model)
                {
                    return Html::mailto($model->email, $model->email);
                },
                'format'    => 'html'
            ],

            // User Type, with editable field for easy promotion/demotion
            [
                'attribute' => 'user_type',
                'value'     => function($model)
                {
                    // return User::$userTypes[$model->user_type];
                    if($model->id !== Yii::$app->user->identity->id && Yii::$app->user->identity->user_level >= $model->user_level)
                    {   
                        return Editable::widget([
                            'model'     => $model, 
                            'attribute' => 'user_type',
                            'size'      => 'md',
                            'format'    => Editable::FORMAT_LINK,
                            'placement' => 'top',
                            'displayValue'  => User::$userTypes[$model->user_type],

                            'inputType' => Editable::INPUT_DROPDOWN_LIST,
                            'data'      => User::manageableUserTypes(),

                            'beforeInput' => function($form, $widget) 
                            {
                                echo $form->field($widget->model, 'id', ['labelOptions' => ['style' => 'display: none;']])->hiddenInput();
                            },

                            'formOptions'   => [
                                'action' => ['/user/admin/update-user-type']
                            ],
                            'buttonsTemplate' => '{submit}',
                        ]);
                    }
                    else 
                    {
                        // Can't modify your own account!
                        return User::$userTypes[$model->user_type];
                    }
                    
                },
                'format'    => 'raw',
                'filter'    => User::$userTypes
            ],

            // Creation Date
            [
                'attribute' => 'creation_date',
                'value'     => function ($model) 
                {
                    if(extension_loaded('intl')) 
                    {
                        return Yii::t('user', '{0, date, dd/MM/yyyy hh:mma}', [strtotime($model->creation_date)]);
                    } 
                    else 
                    {
                        return date('d/m/Y h:ia', strtotime($model->creation_date));
                    }
                },
                'filter' => false
            ],

            // Statuses (confirmed & banned statuses)
            [
                'header'    => 'Status',
                'value'     => function($model)
                {
                    $status = "";

                    if($model->isConfirmed)
                    {
                        $status .= "<span class='glyphicon glyphicon-ok lnch-status-icon ok' title='Confirmed' aria-hidden='true'></span>";
                    }
                    else 
                    {
                        $status .= "<span class='glyphicon glyphicon-ok lnch-status-icon' title='Not Confirmed' aria-hidden='true'></span>";
                    }

                    if($model->isBanned)
                    {   
                        $status .= "<span class='glyphicon glyphicon-warning-sign lnch-status-icon not-ok' title='Banned' aria-hidden='true'></span>";
                    }
                    else 
                    {
                        $status .= "<span class='glyphicon glyphicon-warning-sign lnch-status-icon' title='Not Banned' aria-hidden='true'></span>";
                    }
                    

                    return $status;
                },
                'format'    => 'html',
                'contentOptions'    => [
                    'style' => 'text-align: center;'
                ]
            ],

            // Last login
            [
                'attribute' => 'last_login',
                'format'    => ['date', 'php:d/m/Y h:ia'],
                'filter'    => false,
            ],

            // Actions column. Edit & delete user
            [
                'class'     => 'yii\grid\ActionColumn',
                'header'    => 'Actions',
                'template'  => '{update} {ban} {unban} {delete}',

                'buttons'   => [
                    'update'    => function($url, $model, $key)
                    {
                        return Html::a("<span class='glyphicon glyphicon-pencil lnch-action-icon' title='Update' aria-hidden='true'></span>", $url, [
                            'data' => [
                                'pjax'  => '0'
                            ]
                        ]);
                    },
                    'ban' => function($url, $model, $key)
                    {
                        return Html::a("<span class='glyphicon glyphicon-ban-circle lnch-action-icon' title='Ban User' aria-hidden='true'></span>", $url, [
                            'data' => [
                                'method' => 'post',
                                'confirm' => Yii::t('user', 'Are you sure you want to ban this user?'),
                                'pjax' => '0'
                            ]
                        ]);
                    },
                    'unban' => function($url, $model, $key)
                    {
                        return Html::a("<span class='glyphicon glyphicon-ok-circle lnch-action-icon' title='Unban User' aria-hidden='true'></span>", 
                            ['/user/admin/ban/' . $model->id], 
                            [
                                'data' => [
                                    'method' => 'post',
                                    'confirm' => Yii::t('user', 'Are you sure you want to unban this user?'),
                                    'pjax' => '0'
                                ]
                            ]);
                    },
                    'delete'    => function($url, $model, $key)
                    {
                        return Html::a("<span class='glyphicon glyphicon-trash lnch-action-icon' title='Delete' aria-hidden='true'></span>", $url, [
                            'data' => [
                                'method'    => 'post',
                                'pjax'      => '0',
                                'confirm'   => Yii::t('user', 'Are you sure you want to delete this user? This cannot be undone.')
                            ]
                        ]);
                    }
                ],

                'visibleButtons' => [
                    'update' => function($model, $key, $index)
                    {
                        return Yii::$app->user->identity->user_type >= 20;
                    },
                    'delete' => function($model, $key, $index)
                    {
                        return Yii::$app->user->identity->user_type >= 30;
                    },
                    'ban' => function($model, $key, $index)
                    {
                        return !$model->isBanned;
                    },
                    'unban' => function($model, $key, $index)
                    {
                        return $model->isBanned;
                    }
                ],

                'contentOptions'    => [
                    'style' => 'text-align: center;'
                ]
            ],
        ],
    ]); 

    Pjax::end(); 

?>