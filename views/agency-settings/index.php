<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\SysParametr;
use app\models\Agency;

/* @var $this yii\web\View */
/* @var $searchModel app\models\searchModels\AgencySettingsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Agency Settings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="agency-settings-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Agency Settings', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
             'label'=>'Агентсво',
             'format' => 'raw',
             'attribute'=>'agency_id',    
             'filter'=> Agency::getList(), 
             'value'=>function ($data) { return Agency::getName($data->agency_id);}
             //'value'=>function ($data) { return User::userSexName($data->sex);}

            ],
            [
             'label'=>'Параметр',
             'format' => 'raw',
             'attribute'=>'sys_parametr_id',    
             'filter'=> SysParametr::getList(), 
             'value'=>function ($data) { return SysParametr::getName($data->sys_parametr_id);}
             //'value'=>function ($data) { return User::userSexName($data->sex);}

            ],
            
            
            'value:ntext',
            

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
