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
    $this->title = Yii::t('user', 'User Types');

    $this->params['breadcrumbs'][] = $this->title;
    
    AdminAssets::register($this);

?>

<?php 

    $this->beginContent('@lnch/users/views/admin/__template-types.php'); 

    echo $this->render('/_flash', [
        'module' => Yii::$app->getModule('user'),
    ]);
    
    Pjax::begin();

    echo GridView::widget([
        'dataProvider'  => $dataProvider,
        'filterModel'   => $searchModel,
        'layout'        => "{items}\n{pager}",
    ]); 

    Pjax::end(); 

    $this->endContent(); 

?>