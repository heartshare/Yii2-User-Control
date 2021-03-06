<?php
/*
 * This file is part of the Dektrium project
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */
/**
 * @var yii\web\View
 * @var dektrium\user\models\User
 */
?>

<?php $this->beginContent('@lnch/users/views/admin/update.php', ['user' => $user]) ?>

<table class="table">
    <tr>
        <td><strong><?= Yii::t('user', 'Registration time') ?>:</strong></td>
        <td><?= Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [strtotime($user->creation_date)]) ?></td>
    </tr>
    <?php if ($user->signup_ip !== null): ?>
        <tr>
            <td><strong><?= Yii::t('user', 'Registration IP') ?>:</strong></td>
            <td><?= $user->signup_ip ?></td>
        </tr>
    <?php endif ?>
    <tr>
        <td><strong><?= Yii::t('user', 'Confirmation status') ?>:</strong></td>
        <?php if ($user->status != "P"): ?>
            <td class="text-success"><?= Yii::t('user', 'Confirmed at {0, date, MMMM dd, YYYY HH:mm}', [strtotime($user->confirmation_date)]) ?></td>
        <?php else: ?>
            <td class="text-danger"><?= Yii::t('user', 'Unconfirmed') ?></td>
        <?php endif ?>
    </tr>
    <tr>
        <td><strong><?= Yii::t('user', 'Ban status') ?>:</strong></td>
        <?php if ($user->isBanned): ?>
            <!-- <td class="text-danger"><?//= Yii::t('user', 'Banned at {0, date, MMMM dd, YYYY HH:mm}', [$user->last_updated]) ?></td> -->
            <td class="text-danger"><?= Yii::t('user', 'User banned'); ?></td>
        <?php else: ?>
            <td class="text-success"><?= Yii::t('user', 'Not banned') ?></td>
        <?php endif ?>
    </tr>
</table>

<?php $this->endContent() ?>