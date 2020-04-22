<?php

/* 
 * @created at 2016
 * @author Kirill Shavrak.
 * kirill@shavrak.ru
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use yii\data\ActiveDataProvider;
use kartik\grid\GridView;

$this->title =$batch->campaign_name;
?>

<br>
    <ul class="nav nav-tabs">
    <li   ><a href="/users/simple-load?batch_id=<?=$batch->id?>">Статус рассылки</a></li>
    <?php if (Yii::$app->user->id == 11) {?> 
       <li class="active" ><a href="/users/simple-unisender-validation?batch_id=<?=$batch->id?>&CsvBatchesSearch[batch_id]=<?=$batch->id?>">Повторная валидация</a></li>
    <?php } ?> 
    <li  ><a href="/users/simple-csv-huge?CsvBatchesSearch[batch_id]=<?=$batch->id?>">Просмотр файла</a></li>
    <li  ><a href="/users/simple-csv-report?batch_id=<?=$batch->id?>">Просмотр валидации</a></li>
        <?php if (in_array(Yii::$app->user->id,[11, 23, 15])) {?> 
       <li  ><a href="/users/simple-batch-tasks?batch_id=<?=$batch->id?>">Системные задачи</a></li>
       <li  ><a href="/users/simple-crm-tasks?CrmUsersSearch[batch_id]=<?=$batch->id?>">Задачи CRM</a></li>
    <?php } ?> 
  </ul>


<div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"><?= $this->title ?></h1>
                </div>
    
                <div class="col-lg-12">
                    <?php
                       foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
                       echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
                       }
                    ?>
                </div>
    
                <div class="col-lg-3">
                    <a href="/users/simple-unisender-validation?batch_id=<?=$batch->id?>&mode=1" class="btn btn-default">1. Создать списки</a>
                </div>
                <div class="col-lg-3">
                      <a href="/users/simple-unisender-validation?batch_id=<?=$batch->id?>&mode=2" class="btn btn-default">2. Запускс подписки</a>
                </div>
                <div class="col-lg-3">
                      <a href="/users/simple-unisender-validation?batch_id=<?=$batch->id?>&mode=3" class="btn btn-default">3. Экспорт из Unisender</a>
                </div>
    
                <div class="col-lg-3">
                      <a href="/users/simple-unisender-validation?batch_id=<?=$batch->id?>&mode=4" class="btn btn-danger">4. Запуск рассылки</a>
                </div>
    
</div>

<div class="row">
    &nbsp;<br>
    <div class="col-lg-12">
        <h3>
    <?php 
    echo @\app\models\AgencyCsvBatch::getStatus($batch->current_step);
            ?>
    </h3>
    </div>
    
    
    &nbsp;<br>
</div>
<div class="row">

</div>


<div class="row">
<div class="col-lg-12">
           
            <?php
            $gridColumns = [
                       ['class' => 'kartik\grid\SerialColumn'],
                          [
                           'attribute'=>'email_availability', 
                           'width'=>'250px',
                            'label'=>'Доступность e-mail адреса',
//                                            'value'=>function ($model, $key, $index, $widget) { 
//                                                return $model->supplier->company_name;
//                                            },
//                                            'filterType'=>GridView::FILTER_SELECT2,
//                                            'filter'=>ArrayHelper::map(Suppliers::find()->orderBy('company_name')->asArray()->all(), 'id', 'company_name'), 
//                                            'filterWidgetOptions'=>[
//                                                'pluginOptions'=>['allowClear'=>true],
//                                            ],
//                                            'filterInputOptions'=>['placeholder'=>'Any supplier'],
                           'group'=>true,  // enable grouping
                       ],
                       [
                           'attribute'=>'email_status', 
                           'width'=>'400px',
                           'label'=>'Статус e-mail адреса',

                       ],

                       [
                           'attribute'=>'c', 
                           'width'=>'150px',
                           'label'=>'Кол-во',

                       ],



                   ];
                   echo GridView::widget([
                     //  'language'=>'ru',
                       'dataProvider' => $dataProvider,
                     //  'filterModel' => $MaildeliverysearchModel,
                       'columns' => $gridColumns,

                           'panel'=>['type'=>'primary', 'heading'=>'Общая статитсика по файлу'],
                          'pjax'=>true,
                           'striped'=>true,
                           'hover'=>true,

                   ]);

            ?>
                             
                         
            
         </div>
         </div>


<div class="row">
    <div class="col-lg-12">
    &nbsp;<br>
    
    Просмотр файла
    &nbsp;<br>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
    
    
     <?php 
      
    if(1==1){
        
       echo GridView::widget([
           
        'filterModel' => $searchModel,
        'dataProvider' => $dataProviderCsv,
           'layout' => "{summary}\n{items}",  
        'columns' => [
   
            
            'email',

            'hostess_name',
            
                    
             [
                'label'=>'Доступность e-mail адреса',
                'format' => 'raw',
                'attribute'=>'email_availability',    
                'filter'=> ['available'=>'available',
                             'unreachable'=> 'unreachable',
                             'temp_unreachable'=> 'temp_unreachable',
                              'mailbox_full'=>  'mailbox_full',
                              'spam_rejected'=>  'spam_rejected',
                              'spam_folder'=>  'spam_folder',
                                ], 
                'value'=>'email_availability'
                

               ], 
                    
               [
                'label'=>'Статус e-mail адреса',
                'format' => 'raw',
                'attribute'=>' email_status',    
                'filter'=> ['new'=>'new',
                             'invited'=> 'invited',
                             'active'=> 'active',
                              'inactive'=>  'inactive',
                              'unsubscribed'=>  'unsubscribed',
                              'blocked'=>  'blocked',
                              'activation_requested'=>  'activation_requested',
                                ], 
                'value'=>'email_status'
                

               ],  
            'sub_code',
            'sub_message'
            
            
            
            
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
                    
    ]); 
       
    }
       ?>
</div>
</div>