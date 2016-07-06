<?php
/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

    use lnch\users\assets\AdminAssets;
    use lnch\users\models\User;

    use yii\bootstrap\Nav;
    use yii\web\View;

    /**
     * @var View 	$this
     * @var User 	$user
     * @var string 	$content
     */
    $this->title = Yii::t('user', 'Update user account');
    $this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;

    AdminAssets::register($this);

?>

<?php 
    echo $this->render('/_flash', [
        'module' => Yii::$app->getModule('user'),
    ]); 
?>

<?php 
    $this->beginContent('@lnch/users/views/admin/__template-update.php', ['user' => $user]); 
?>

<div class="row">
    <div class="col-md-3">
        <div class="panel panel-default lnch-users">
            <div class="panel-body narrow">
                <?= Nav::widget([
                    'options' => [
                        'class' => 'nav-pills nav-stacked',
                    ],
                    'items' => [
                        ['label' => Yii::t('user', 'Account details'), 'url' => ['/user/admin/update', 'id' => $user->id]],
                        ['label' => Yii::t('user', 'Profile details'), 'url' => ['/user/admin/update-profile', 'id' => $user->id]],
                        ['label' => Yii::t('user', 'Information'), 'url' => ['/user/admin/info', 'id' => $user->id]],
                        [
                            'label' => Yii::t('user', 'Assignments'),
                            'url' => ['/user/admin/assignments', 'id' => $user->id],
                            'visible' => isset(Yii::$app->extensions['dektrium/yii2-rbac']),
                        ],
                        '<hr>',
                        [
                            'label' => Yii::t('user', 'Confirm'),
                            'url'   => ['/user/admin/confirm', 'id' => $user->id],
                            'visible' => !$user->isConfirmed,
                            'linkOptions' => [
                                'class' => 'text-success',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('user', 'Are you sure you want to confirm this user?'),
                            ],
                        ],
                        [
                            'label' => Yii::t('user', 'Ban'),
                            'url'   => ['/user/admin/ban', 'id' => $user->id],
                            'visible' => !$user->isBanned,
                            'linkOptions' => [
                                'class' => 'text-danger',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('user', 'Are you sure you want to ban this user?'),
                            ],
                        ],
                        [
                            'label' => Yii::t('user', 'Unban'),
                            'url'   => ['/user/admin/ban', 'id' => $user->id],
                            'visible' => $user->isBanned,
                            'linkOptions' => [
                                'class' => 'text-success',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('user', 'Are you sure you want to unban this user?'),
                            ],
                        ],
                        [
                            'label' => Yii::t('user', 'Delete'),
                            'url'   => ['/user/admin/delete', 'id' => $user->id],
                            'linkOptions' => [
                                'class' => 'text-danger',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('user', 'Are you sure you want to delete this user?'),
                            ],
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="panel lnch-users panel-default">
            <div class="panel-body">
                <?= $content ?>
            </div>
        </div>
    </div>
</div>

<?php 
    $this->endContent();
?>