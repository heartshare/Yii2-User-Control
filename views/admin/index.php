<?php
/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

    use lnch\users\models\UserSearch;

    use yii\data\ActiveDataProvider;
    use yii\grid\GridView;
    use yii\helpers\Html;
    use yii\jui\DatePicker;
    use yii\web\View;
    use yii\widgets\Pjax;

    /**
     * @var View $this
     * @var ActiveDataProvider $dataProvider
     * @var UserSearch $searchModel
     */
    $this->title = Yii::t('user', 'Manage users');
    $this->params['breadcrumbs'][] = $this->title;

?>

<?php $this->beginContent('@lnch/users/views/admin/__template-index.php'); ?>

<?php 

    echo $this->render('/_flash', [
        'module' => Yii::$app->getModule('user'),
    ]);

    if($this->context->module->showAdminMenu): echo $this->render('/admin/_menu'); endif;

    echo $this->render('_index-list', [
        'dataProvider' => $dataProvider,
        'searchModel'  => $searchModel,
    ]);
    
?>

<?php $this->endContent(); ?>