<?php
/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

    // use lnch\users\models\UserSearch;

    // use yii\data\ActiveDataProvider;
    use yii\grid\GridView;
    use yii\helpers\Html;
    // use yii\jui\DatePicker;
    // use yii\web\View;
    use yii\widgets\Pjax;

?>

<?php Pjax::begin() ?>

<?= GridView::widget([
    'dataProvider' 	=> $dataProvider,
    'filterModel'  	=> $searchModel,
    'layout'  		=> "{items}\n{pager}",
    'columns' => [
        'username',
        'email:email',
        [
            'attribute' => 'signup_ip',
            'value' => function ($model) {
                return $model->signup_ip == null
                    ? '<span class="not-set">' . Yii::t('user', '(not set)') . '</span>'
                    : $model->signup_ip;
            },
            'format' => 'html',
            'filter' => false
        ],
        [
            'attribute' => 'creation_date',
            'value' => function ($model) {
                if (extension_loaded('intl')) 
                {
                    return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [strtotime($model->creation_date)]);
                } 
                else 
                {
                    return date('Y-m-d G:i:s', $model->creation_date);
                }
            },
            'filter' => false
            // 'filter' => DatePicker::widget([
            //     'model'      => $searchModel,
            //     'attribute'  => 'creation_date',
            //     'dateFormat' => 'php:Y-m-d',
            //     'options' => [
            //         'class' => 'form-control',
            //     ],
            // ]),
        ],
        [
            'attribute' => 'last_login',
            'value' => function ($model) {
                if($model->last_login == NULL)
                {
                    return "-";
                }
                
                if(extension_loaded('intl')) 
                {
                    return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [strtotime($model->last_login)]);
                } 
                else 
                {
                    return date('Y-m-d G:i:s', $model->last_login);
                }
            },
            'filter' => false
            // 'filter' => DatePicker::widget([
            //     'model'      => $searchModel,
            //     'attribute'  => 'creation_date',
            //     'dateFormat' => 'php:Y-m-d',
            //     'options' => [
            //         'class' => 'form-control',
            //     ],
            // ]),
        ],
        [
            'header' => 'Type',
            'attribute' => 'user_type',
            'value' => function($model) {
                switch($model->user_type)
                {
                    case 10:
                        return 'User';
                    case 20:
                        return 'Moderator';
                    case 30:
                        return 'Admin';
                    case 40:
                        return 'Founder';
                    default:
                        return ' - ';
                }
            },
            'format' => 'raw',
            'filter' => [
                10 => 'User', 
                20 => 'Moderator', 
                30 => 'Admin', 
                40 => 'Founder'
            ]
        ],
        [
            'header' => Yii::t('user', 'Confirmation'),
            'value' => function ($model) {
                if ($model->isConfirmed) {
                    return '<div class="text-center"><span class="text-success">' . Yii::t('user', 'Confirmed') . '</span></div>';
                } else {
                    return Html::a(Yii::t('user', 'Confirm'), ['confirm', 'id' => $model->id], [
                        'class' => 'btn btn-xs btn-success btn-block',
                        'data-method' => 'post',
                        'data-confirm' => Yii::t('user', 'Are you sure you want to confirm this user?'),
                    ]);
                }
            },
            'format' => 'raw',
            'visible' => Yii::$app->getModule('user')->enableConfirmation,
        ],
        [
            'header' => Yii::t('user', 'Ban status'),
            'value' => function ($model) {
                if ($model->isBanned) {
                    return Html::a(Yii::t('user', 'Unban'), ['ban', 'id' => $model->id], [
                        'class' => 'btn btn-xs btn-success btn-block',
                        'data-method' => 'post',
                        'data-confirm' => Yii::t('user', 'Are you sure you want to unban this user?'),
                    ]);
                } else {
                    return Html::a(Yii::t('user', 'Ban'), ['ban', 'id' => $model->id], [
                        'class' => 'btn btn-xs btn-danger btn-block',
                        'data-method' => 'post',
                        'data-confirm' => Yii::t('user', 'Are you sure you want to ban this user?'),
                    ]);
                }
            },
            'format' => 'raw',
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}',
        ],
    ],
]); ?>

<?php Pjax::end() ?>