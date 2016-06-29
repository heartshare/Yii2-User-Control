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
    $this->title = Yii::t('user', 'Users Dashboard');

    $this->params['breadcrumbs'][] = $this->title;

?>

<?php 

    $this->beginContent('@lnch/users/views/admin/__template-index.php'); 

    echo $this->render('/_flash', [
        'module' => Yii::$app->getModule('user'),
    ]);

    if($this->context->module->showAdminMenu)
    {
        echo $this->render('/admin/_menu');
    } 

    echo $this->render('_index-list', [
        'dataProvider' => $dataProvider,
        'searchModel'  => $searchModel,
    ]);

    $this->endContent(); 

?>