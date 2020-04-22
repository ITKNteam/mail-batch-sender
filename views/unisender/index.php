<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Списки контактов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unisender-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

  
    <p>
        <?= Html::a('Создать список контактов', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

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
                         
            
            [
                 'label'=>'Название списка',
                  'attribute'=>'list_title', 
                 'format' => 'raw',
                 'value'=>function ($data) {return Html::a($data->list_title, '/unisender/update?id='.$data->id,
                                                ['title' => Yii::t('yii', 'Редактировать'), 'data-pjax' => '0']);
                            }
                 
             ],
             
               [
                 'label'=>'Статистика',
                  'attribute'=>'list_id', 
                 'format' => 'raw',
                 'value'=>function ($data) {return Html::a('Просмотр статуса', '/unisender/list-stat?list_id='.$data->list_id.'&agency_id='.$data->agency_id,
                                                ['title' => Yii::t('yii', 'Статус'), 'data-pjax' => '0']);
                            }
                 
             ],            
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
