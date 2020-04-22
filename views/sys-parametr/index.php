<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\searchModels\sysParametrSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sys Parametrs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-parametr-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Sys Parametr', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'sys_name',
            'data_type',
            'group_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
