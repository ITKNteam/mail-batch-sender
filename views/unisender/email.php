<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Email для рассылки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unisender-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать ', ['create-email'], ['class' => 'btn btn-success']) ?>
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
                 'label'=>'Действие',
                 'format' => 'raw',
                 'value'=>function ($data) {return Html::a( 'Переслать ', '/unisender/reply-email?id='.$data->id,
                                                ['title' => Yii::t('yii', 'переслать'), 'data-pjax' => '0']);
                            }
                 
             ], 
            [
                 'label'=>' Агентство',
                 'format' => 'raw',
                  'value'=> function ($data) {
        //$data['agency']['name'],
                     return $data->agency['name'];
                 },
                
                 
             ], 
                         
            'name',
              [
                 'label'=>' Список рассылок',
                 'format' => 'raw',
                  'value'=> function ($data) {
        //$data['agency']['name'],
                     return $data->list['list_title'];
                 },
                
                 
             ], 
             'sender_name',
             'sender_email',
             'subject',
          //   'body',
             
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
