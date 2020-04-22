<?php
/* @var $this yii\web\View */
$this->title = 'West BTL';
use yii\bootstrap\Tabs;
use yii\bootstrap\Modal;
use yii\bootstrap\Collapse;
//use yii\bootstrap\ActiveForm;



use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\User;

use kartik\grid\GridView;
use yii\helpers\Url;

use kartik\widgets\DatePicker;
use dosamigos\datepicker\DateRangePicker;

use app\models\Handbook;
use kartik\select2\Select2;

 

?>



<br>
    <ul class="nav nav-tabs">
     <li   ><a href="/users/simple-load?batch_id=<?=$batch_id?>">Статус рассылки</a></li>
    <?php if (Yii::$app->user->id == 11) {?> 
       <li  ><a href="/users/simple-unisender-validation?batch_id=<?=$batch_id?>&CsvBatchesSearch[batch_id]=<?=$batch_id?>">Повторная валидация</a></li>
    <?php } ?> 
    <li class="active"  ><a href="/users/simple-csv-huge?CsvBatchesSearch[batch_id]=<?=$batch_id?>">Просмотр файла</a></li>
    <li  ><a href="/users/simple-csv-report?batch_id=<?=$batch_id?>">Просмотр валидации</a></li>
<?php if (in_array(Yii::$app->user->id,[11, 23, 15])) {?> 
       <li  ><a href="/users/simple-batch-tasks?batch_id=<?=$batch_id?>">Системные задачи</a></li>
       <li  ><a href="/users/simple-crm-tasks?CrmUsersSearch[batch_id]=<?=$batch_id?>">Задачи CRM</a></li>
    <?php } ?> 
  </ul>


<div class="content-inner">
   
    
<div class="unisender-list-form">
    <?php $form = ActiveForm::begin(
            [
         'method' => 'get',
        //  'action' => ['controller/action'],
      ]
            ); ?>
    <?= $form->field($searchModel, 'batch_id')->hiddenInput(['value'=>$batch_id]) ?>
    
    <div class="row">
        <div class="col-lg-6">
    
     <?php
            echo $form->field($searchModel, 'activity_loc_ar')->widget(Select2::classname(), [
            'data' => Handbook::getValueListKey(1),
            //'data' => $searchModel::getBatchCityList($batch_id),
            'language' => 'ru',
            'options' => ['multiple' => true, 'placeholder' => 'Выбор городов...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
           

      ?>
            
        </div>    
        <div class="col-lg-6">
            
     <?php
        
//     echo $form->field($searchModel, 'hostess_name_ar')->widget(Select2::classname(), [
//            'data' => $searchModel::getBatchHostesList($batch_id),
//            'language' => 'ru',
//            'options' => ['multiple' => true, 'placeholder' => 'Выбор Hostes ...'],
//            'pluginOptions' => [
//                'allowClear' => true
//            ],
//        ]);

      ?>
            
        </div>     
        </div>     
    
    <div class="row">
        <div class="col-lg-3">
            
            
     <?php
            echo $form->field($searchModel, 'gender_ar')->widget(Select2::classname(), [
            'data' => \app\models\CsvBatches::getSex(),
            'language' => 'ru',
            'options' => ['multiple' => true, 'placeholder' => 'Выбор пола ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

      ?>
            
        </div>
        <div class="col-lg-3">
            
            
     <?php
            echo $form->field($searchModel, 'validate_status_ar')->widget(Select2::classname(), [
//            'data' => ['unknown'=>'unknown',
//                             'failed'=> 'failed',
//                             'passed'=> 'passed',
//                              'error'=>  'error'
//                                ],
            'data' => \app\models\searchModels\CsvBatchesSearch::getValidatStatusNameArr(),
            'language' => 'ru',
            'options' => ['multiple' => true, 'placeholder' => 'Выбор статусов ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

      ?>
            
        </div>
        
        <div class="col-lg-3">
            <?= $form->field($searchModel, 'email')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
     
     <label class="control-label" for="csvbatchessearch-activity_dt_st">Дата активности:</label>
     <?php
      
      echo DateRangePicker::widget([
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
                            ]);       
     
//     echo $form->field($searchModel, 'hostess_name_ar')->widget(Select2::classname(), [
//            'data' => $searchModel::getBatchHostesList($batch_id),
//            'language' => 'ru',
//            'options' => ['multiple' => true, 'placeholder' => 'Выбор Hostes ...'],
//            'pluginOptions' => [
//                'allowClear' => true
//            ],
//        ]);

      ?>
    
     </div>
        
        
        
     </div>
        
     <br>
    
    <div class="form-group">
        <?= Html::submitButton( 'Применить', ['class' => 'btn btn-primary']) ?>
        <a href="/users/simple-csv-huge?CsvBatchesSearch[batch_id]=<?=$batch_id?>" class="btn btn-success">Сбросить фильтр</a>
        
    </div>

    <?php ActiveForm::end(); ?>

</div>
    
    
    

    
    <br>
    <br>
     <?php 
      
    
        
       echo GridView::widget([
           
       // 'filterModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'layout' => "{summary}\n{items}",  
        'columns' => [
   
            
            'f_name',
            'l_name',
            'email',
//             [
//             'label'=>'Статус',
//             'format' => 'raw',
//             'attribute'=>'status_id',    
//             'filter'=> app\models\CsvBatches::getStatusList(),
//             'value'=>function ($data) { return app\models\CsvBatches::getStatusName($data->status_id);}
//             //'value'=>function ($data) { return User::userSexName($data->sex);}
//
//            ],
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
            
            'phone',
            //'age',
            
            [
             'label'=>'Пол',
             'format' => 'raw',
             'attribute'=>'gender',    
          //   'filter'=> app\models\CsvBatches::getSex(), 
             'value'=>function ($data) { return app\models\CsvBatches::userSexName($data->gender);}
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
                    
                    
               [
                'label'=>'Валидация',
                'format' => 'raw',
                'attribute'=>'validate_status',    
                'filter'=> ['unknown'=>'Не извесные',
                             'failed'=> 'Не валидные',
                             'passed'=> 'Валидные',
                             
                                ], 
                'value'=>function ($data) { return \app\models\searchModels\CsvBatchesSearch::getValidateStatusName($data->validate_status);}   
                

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
    




    

