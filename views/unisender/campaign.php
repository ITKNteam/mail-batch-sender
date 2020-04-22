<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Запуск рассылки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unisender-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

   
    
    
        <div class="pull-left">
            
        <?= Html::a('Создать ', ['create-campaign'], ['class' => 'btn btn-success']) ?>
        </div>
    <div class="pull-right">
        <a target="_blank" href="https://www.dropbox.com/s/yrcoxw3if7piecb/download-email-stat.pdf?dl=0"><span class="glyphicon glyphicon-download-alt"></span> Скачать инструкцию по выгрузке результатов</a>
          
    </div>
<p>
    &nbsp;
    <br>
    &nbsp;
</p>
    
    
    
    
     <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
     ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
                 'label'=>' Агентство',
                 'format' => 'raw',
                  'value'=> function ($data) {
        //$data['agency']['name'],
                     return $data->agency['name'];
                 },
                
                 
             ], 
                         
            'name',
//              [
//                 'label'=>' Email',
//                 'format' => 'raw',
//                  'value'=> function ($data) {
//        //$data['agency']['name'],
//                     return $data->message['name'];
//                 },
//                
//                 
//             ], 
                         
                 [
                 'attribute'=> 'status',
                 'label'=>'статус',
                 'format' => 'raw',
                 'value'=>function ($data) {return Html::a('Просмотр статуса', '/unisender/campaign-stat?campaign_id='.$data->campaign_id.'&agency_id='.$data->agency_id.'&local_campaign_id='.$data->id,
                                                ['title' => Yii::t('yii', 'Статус'), 'data-pjax' => '0']);
                            }
                 
             ],
             
         //    'count',
             
               [
                 'label'=>'Дата создания',
                 'format' => 'raw',
                 'value'=>function ($data) {return date("d.m.Y h:i:s",  strtotime($data->dt_create)); },
                 
             ],            
             
            // 'last_uid',
            // 'dt_last',

          //  ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
