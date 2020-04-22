<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Compare Data Emails';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="compare-data-email-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Compare Data Email', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

             [
                 'label'=>' Агентство',
                 'format' => 'raw',
                  'value'=> function ($data) {
        
                     return $data->agency['name'];
                 },
                
                 
             ], 
             [
                 'label'=>'Поле в файле',
                 'format' => 'raw',
                  'value'=> function ($data) {
                            
                     return app\models\AgencySettings::getSysName($data->param_id);
                 },
                
                 
             ], 
             [
                 'label'=>'Шаблон содержания',
                 'format' => 'raw',
                  'value'=> function ($data) {
                     
                     
                     return app\models\UnisenderMailTpl::getMailGroupName( $data->mail_tpl_id);
                 },
                
                 
             ], 
            
            'value',
            
            // 'dt_create',
            // 'uid_create',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
