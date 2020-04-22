<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Письмо для подтверждения рассылки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unisender-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    
        <div class="pull-left">
            
        <?= Html::a('Создать ', ['create-confirm-letter'], ['class' => 'btn btn-success']) ?>
        </div>
    
<p>
    &nbsp;
    <br><br><br>
    
    К каждому списку рассылки прикреплён текст приглашения подписаться и подтвердить свой email, отправляемый подписчику для подтверждения рассылки. 
    С помощью данного метода  можно изменить текст письма. Текст обязательно должен включать в себя как минимум одну ссылку с атрибутом href="{{ConfirmUrl}}".
</p>
    
    
    
    
     <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
     ?>

    <?php
      echo   GridView::widget([
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
                 'label'=>' name',
                  'attribute'=>'name',
                 'format' => 'raw',
                   'value'=>function ($data) {return Html::a($data->name, '/unisender/update-confirm-letter?id='.$data->id,
                                                ['title' => Yii::t('yii', 'Статус'), 'data-pjax' => '0']);
                            }
                 
             ], 
                     
               [
               'label'=>' sender_name',
                'attribute'=>'sender_name',
               'format' => 'raw',
                 'value'=>function ($data) {return Html::a($data->sender_name, '/unisender/update-mail-confirm-tpl?id='.$data->id,
                                              ['title' => Yii::t('yii', 'Статус'), 'data-pjax' => '0']);
                          }

           ],          
                     
            'sender_name',
            'sender_email',
            'subject',
                   
              
             
         //    'count',
             
               [
                 'label'=>'Дата создания',
                 'format' => 'raw',
                 'value'=>function ($data) {return date("d.m.Y h:i:s",  strtotime($data->created_at)); },
                 
             ],            
             
            // 'last_uid',
            // 'dt_last',

          //  ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); 
                 
                 
                 ?>

</div>
