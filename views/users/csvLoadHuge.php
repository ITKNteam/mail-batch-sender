<?php
/* @var $this yii\web\View */
$this->title = 'West BTL';
use yii\bootstrap\Tabs;
use yii\bootstrap\Modal;
use yii\bootstrap\Collapse;
use yii\bootstrap\ActiveForm;


use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\User;

use kartik\grid\GridView;
use yii\helpers\Url;

use kartik\widgets\DatePicker;
use dosamigos\datepicker\DateRangePicker;

 

?>

<div class="content-inner">
    <h3 class="title">Загрузка </h3>
    <div class="user-block clearfix">
     <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
     ?>
    
    </div>
    
      

    
     <p>
      
      
    
    <br>
     <?php 
      
    
        
       echo GridView::widget([
           
        'filterModel' => $searchModel,
        'dataProvider' => $dataProvider,
           'layout' => "{summary}\n{items}",  
        'columns' => [
   
            
            'f_name',
            'l_name',
            'email',
             [
             'label'=>'Статус',
             'format' => 'raw',
             'attribute'=>'rec_status',    
             'filter'=> app\models\CrmUsers::statusList()
          //   'value'=>function ($data) { return app\models\CrmUsers::getListName()   getStatusName($data->status_id);}
             //'value'=>function ($data) { return User::userSexName($data->sex);}

            ],
            [
                 'label'=>'Дата изменения',
                 'format' => 'html',
                 'attribute'=>'dt_load',    
                 'filter' => DateRangePicker::widget([
                             //   'model' => 'activity_dt',
                                'name' => 'CsvBatchesSearch[dt_load_st]',
                              //  'name' => 'CsvBatchesSearch[activity_dt_st]',
                                
                                'value' =>$searchModel->dt_load_st,
                                'nameTo' => 'CsvBatchesSearch[dt_load_fn]',
                                'valueTo' =>$searchModel->dt_load_fn,
                                 'clientOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd'
                                ]
                            ]),
//                  'filter' => yii\jui\DatePicker::widget([
//                                'model'=>$searchModel,
//                                'attribute'=>'activity_dt',
//                                'language' => 'ru',
//                                'dateFormat' => 'yyyy-MM-dd',
//                            ]),
           
                 'value'=>'dt_load'
                 //'value'=>function ($data) { return User::userSexName($data->sex);}

                ],
            
            //'phone',
            //'age',
            
            [
             'label'=>'Пол',
             'format' => 'raw',
             'attribute'=>'gender',    
             'filter'=> User::getSex(), 
             'value'=>function ($data) { return User::userSexName($data->gender);}
             //'value'=>function ($data) { return User::userSexName($data->sex);}

            ],
            
            
           // 'priority_mark1',
            //'priority_mark2',
            //'hostess_id',
            'hostess_name',
           // 'activity_dt',
             [
                 'label'=>'Дата',
                 'format' => 'html',
                 'attribute'=>'activity_dt',    
                 'filter' => DateRangePicker::widget([
                             //   'model' => 'activity_dt',
                                'name' => 'CsvBatchesSearch[activity_dt_st]',
                              //  'name' => 'CsvBatchesSearch[activity_dt_st]',
                                
                                'value' =>$searchModel->activity_dt_st,
                                'nameTo' => 'CsvBatchesSearch[activity_dt_fn]',
                                'valueTo' =>$searchModel->activity_dt_fn,
                                 'clientOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd'
                                ]
                            ]),
//                  'filter' => yii\jui\DatePicker::widget([
//                                'model'=>$searchModel,
//                                'attribute'=>'activity_dt',
//                                'language' => 'ru',
//                                'dateFormat' => 'yyyy-MM-dd',
//                            ]),
           
                 'value'=>'activity_dt'
                 //'value'=>function ($data) { return User::userSexName($data->sex);}

                ],

               [
                'label'=>'Город',
                'format' => 'raw',
                'attribute'=>'activity_loc',    
                'filter'=> \app\models\Handbook::getValueListKey(1), 
                'value'=>'activity_loc'
                

               ],     
             
            //'activity_loc',
            
            
        ],
        
        'containerOptions' => ['style'=>'overflow: auto'], // only set when $responsive = false
            'beforeHeader'=>[
                [

                    'options'=>['class'=>'skip-export'] // remove this row from export
                ]
            ],
            'toolbar' =>  [
        //                                            ['content'=>
        //                                                Html::button('&lt;i class="glyphicon glyphicon-plus">&lt;/i>', ['type'=>'button', 'title'=>Yii::t('kvgrid', 'Add Book'), 'class'=>'btn btn-success', 'onclick'=>'alert("This will launch the book creation form.\n\nDisabled for this demo!");']) . ' '.
        //                                                Html::a('&lt;i class="glyphicon glyphicon-repeat">&lt;/i>', ['grid-demo'], ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>Yii::t('kvgrid', 'Reset Grid')])
        //                                            ],
                '{export}',
                '{toggleData}'
            ],
            'pjax' => true,
            'bordered' => true,
            'striped' => false,
            'condensed' => false,
            'responsive' => true,
            'hover' => true,
            'floatHeader' => true,
           // 'floatHeaderOptions' => ['scrollingTop' => $scrollingTop],
            'showPageSummary' => false,
            'panel' => [
                'type' => GridView::TYPE_PRIMARY
            ],
                    
    ]); ?>
    
    </div>
    




    

