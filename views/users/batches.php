<?php

use yii\bootstrap\Progress;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

use yii\grid\GridView;
use app\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\usersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Архив загрузок';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?php  echo $this->title;?>
        </h1>
  
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            
            'id',
//              [
//                 'label'=>' Агентство',
//                 'format' => 'raw',
//                  'value'=> function ($data) {
//        //$data['agency']['name'],
//                     return $data->agency['name'];
//                 },
//                
//                 
//             ], 
               [
                 'label'=>' Загрузил',
                 'format' => 'raw',
                  'value'=> function ($data) {
        //$data['agency']['name'],
                     return $data->createU['username'];
                 },
                
                 
             ], 
               [
                 'label'=>' Статус',
                 'format' => 'raw',
                  'value'=> function ($data) {
                     
                     return @\app\models\AgencyCsvBatch::getStatus($data->status_id);
                 },
                
                 
             ], 
               [
                 'attribute'=>'campaign_id',
                 'format' => 'raw',
                  'value'=> function ($data) {
                     
                     return @\app\models\SysParametr::getName($data->campaign_id);
                 },
                
                 
             ], 
             'file_name',
             'string_count',            
            
          
               
            
          
              'batch_date',
                 
             [
                 'label'=>'Просмотр файла',
                 'format' => 'raw',
                 'value'=>function ($data) {return Html::a( 'Посмотреть', '/users/csv-load-huge?CsvBatchesSearch[batch_id]='.$data->id,
                                                ['title' => Yii::t('yii', 'Профиль'), 'data-pjax' => '0']);
                            }
                 
             ],        
//             [
//                 'label'=>'Просмотр Статуса',
//                 'format' => 'raw',
//                 'value'=>function ($data) {
//                 if ($data->status_id == 2) {
//                    return Html::a( 'Статус рассылки', '/users/crm-users-list?batch_id='.$data->id,
//                                                ['title' => Yii::t('yii', 'Статус'), 'data-pjax' => '0']);
//                 
//                 
//                 } else {
//                     return ' Не отправлялось в Unisender';
//                 }
//                            }
//                 
//             ],        
             [
                 'label'=>'Название списка',
                 'format' => 'raw',
                 'value'=>function ($data) {
                 if ($data->status_id == 2) {
                     $list = app\models\UnisenderList::find()->where(['id'=>$data->last_list_id])->one();
                     
                     return @$list->list_title;
                 } else {
                     return ' Не отправлялось в Unisender';
                 }
                            }
                 
             ],        
             [
                 'label'=>'Отправка контактов',
                 'format' => 'raw',
                 'value'=>function ($data) {
                         $percent = app\models\CronTask::getPercentContacts($data->id, $data->last_list_id, 'SubscribeContacts2');
                        return 
                         Html::a( 'Выполнено на '.$percent. '%'.
                         Progress::widget([
                            'percent' => $percent,
                            'label' => $percent.'%',
                            'barOptions' => ['class' => 'progress-bar-success'],
                            'options' => ['class' => 'active progress-striped']
                        ])
                                 , '/users/check-batch-status-2?batch_id='.$data->id.'&list_id='.$data->last_list_id,
                                                ['title' => Yii::t('yii', 'Статус отправки контактов в Unisender'), 'data-pjax' => '0']);
                        
                            }
                 
             ],        
                     
             

           // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
