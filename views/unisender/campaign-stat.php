<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\UnisenderList */

//$CampaignStatus = $inf['CampaignStatus'];

$this->title = 'Статистика рассылки';
$this->params['breadcrumbs'][] = ['label' => 'Запуск рассылки', 'url' => ['/unisender/campaign']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unisender-list-create">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?= Html::a('В список рассылок ', ['campaign'], ['class' => 'btn btn-success']) ?>
    </p>

    
    <div class="col-lg-6">
         <div class="panel panel-primary">
             <div class="panel-heading">
                 Отчёт о статусах доставки сообщений  рассылки
             </div>
             <div class="panel-body">
                            <div class="row">
                   
            <div class="col-xs-6 text-right">
            <?=  \dosamigos\chartjs\ChartJs::widget([
                'type' => 'Doughnut',
                'id'=>'ddd',
                
                'options' => [
                    'defaults'=> 'defaultConfig',
                   
                    'height' => 300,
                    'width' => 250
                ],
                'data' => [
                    [
                    'value'=> @$res['UnisenderAnswer']->result->data->ok_delivered,
                    'color'=>"#46BFB0",
                    'highlight'=> "#5AD3D1",
                    'label'=> "Доставленно : ".@$res['UnisenderAnswer']->result->data->ok_delivered
                ],
                    [
                    'value'=> @$res['UnisenderAnswer']->result->data->ok_read,
                    'color'=>"#F6B1BD",
                    'highlight'=> "#FAD3D1",
                    'label'=> "Прочитано : ". @$res['UnisenderAnswer']->result->data->ok_read
                ],
                    [
                    'value'=> @$res['UnisenderAnswer']->result->data->ok_unsubscribed,
                    'color'=>"#460FBD",
                    'highlight'=> "#460FB0",
                    'label'=> "Отписались : ". @$res['UnisenderAnswer']->result->data->ok_unsubscribed
                ],
                    [
                    'value'=> @$res['UnisenderAnswer']->result->data->err_user_unknown,
                    'color'=>"#F7464A",
                    'highlight'=> "#F74610",
                    'label'=> "Адрес не существует : ". @$res['UnisenderAnswer']->result->data->err_user_unknown
                ],
                    [
                    'value'=> @$res['UnisenderAnswer']->result->data->err_user_inactive,
                    'color'=>"#F7004A",
                    'highlight'=> "#5A00D1",
                    'label'=> "Адрес когда-то существовал, но сейчас отключен : ". @$res['UnisenderAnswer']->result->data->err_user_inactive
                ],
                    [
                    'value'=> @$res['UnisenderAnswer']->result->data->err_delivery_failed,
                    'color'=>"#ff22AA",
                    'highlight'=> "#5A22ff",
                    'label'=> "Доставка не удалась по иным причинам : ". @$res['UnisenderAnswer']->result->data->err_delivery_failed
                ],
                    [
                    'value'=> @$res['UnisenderAnswer']->result->data->err_will_retry,
                    'color'=>"#BBc08D",
                    'highlight'=> "#5AD3D1",
                       'label'=> "Попытки продолжаются : ". @$res['UnisenderAnswer']->result->data->err_will_retry
                      
                ],
                  
                  



                ]
            ]);
            ?>
                 </div>
              <div class="col-xs-6">
                 <div id="js-legend_ddd" class="chart-legend"></div>
             </div>
             </div>
         </div>
        
    </div>
    </div>        
        <div class="col-lg-6">
        <div class="panel panel-primary">
                     <div class="panel-heading">
                         Стаус рассылки 
                     </div>
                     <div class="panel-body">
                         <div class="row">
                             <div class="list-group">
                                 <span  class="list-group-item"><b>Статус рассылки :</b> <?= @$CampaignStatus['status']?></span>
                                 <span  class="list-group-item"><b>Дата и время создания рассылки :</b> <?= @$CampaignStatus['creation_time']?></span>
                                 <span  class="list-group-item"><b>Дата и время запуска рассылки  :</b> <?= @$CampaignStatus['start_time']?></span>
                             </div>
                             
                         </div>
                             
                     </div>
            </div>
            
        
    </div>
    
 <div class="col-lg-12">
           
                             <?php
//                             $gridColumnsbatch = [
//                                        ['class' => 'kartik\grid\SerialColumn'],
//                                        [
//                                            'attribute'=>'city', 
//                                            'width'=>'250px',
//                                            'label'=>'Город',
////                                            'value'=>function ($model, $key, $index, $widget) { 
////                                                return $model->supplier->company_name;
////                                            },
////                                            'filterType'=>GridView::FILTER_SELECT2,
////                                            'filter'=>ArrayHelper::map(Suppliers::find()->orderBy('company_name')->asArray()->all(), 'id', 'company_name'), 
////                                            'filterWidgetOptions'=>[
////                                                'pluginOptions'=>['allowClear'=>true],
////                                            ],
////                                            'filterInputOptions'=>['placeholder'=>'Any supplier'],
//                                            'group'=>true,  // enable grouping
//                                        ],
//                                        [
//                                            'attribute'=>'email', 
//                                            'width'=>'400px',
//                                            'label'=>' Наличие e-mail адреса',
//                                            
//                                        ],
//                                      
//                                        [
//                                            'attribute'=>'c', 
//                                            'width'=>'150px',
//                                            'label'=>'Кол-во',
//                                            
//                                        ],
//                                        
//                                    
//                                 
//                                    ];
//                                    echo GridView::widget([
//                                      //  'language'=>'ru',
//                                        'dataProvider' => $BatchDataProvider,
//                                      //  'filterModel' => $MaildeliverysearchModel,
//                                        'columns' => $gridColumnsbatch,
//                                            
//                                            'panel'=>['type'=>'primary', 'heading'=>'Статитсика анкетам'],
//                                           'pjax'=>true,
//                                            'striped'=>true,
//                                            'hover'=>true,
//                                        
//                                    ]);
                             
                             ?>
                             
                         
            
         </div>    
    
<div class="col-lg-12">
           
                             <?php
                             $gridColumns = [
                                        ['class' => 'kartik\grid\SerialColumn'],
                                        [
                                            'attribute'=>'city', 
                                            'width'=>'250px',
                                            'label'=>'Город',
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
//                                        [
//                                            'attribute'=>'unisender_send_result', 
//                                            'width'=>'250px',
//                                            'label'=>'статус',
////                                            'value'=>function ($model, $key, $index, $widget) { 
////                                                return $model->supplier->company_name;
////                                            },
////                                            'filterType'=>GridView::FILTER_SELECT2,
////                                            'filter'=>ArrayHelper::map(Suppliers::find()->orderBy('company_name')->asArray()->all(), 'id', 'company_name'), 
////                                            'filterWidgetOptions'=>[
////                                                'pluginOptions'=>['allowClear'=>true],
////                                            ],
////                                            'filterInputOptions'=>['placeholder'=>'Any supplier'],
//                                            'group'=>true,  // enable grouping
//                                        ],
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
                                            
                                            'panel'=>['type'=>'primary', 'heading'=>'Статитсика по рассылке'],
                                           'pjax'=>true,
                                            'striped'=>true,
                                            'hover'=>true,
                                        
                                    ]);
                             
                             ?>
                             
                         
            
         </div>
         
      <div class="col-lg-12">
           
                             <?php
                             $gridColumns = [
                                        ['class' => 'kartik\grid\SerialColumn'],
                                 'email',
                                 'send_result',
                                 ['label'=>' Расшифровка',
                                  'format' => 'raw',
                                  'value'=> function ($data){ return \app\models\UnisenderDeliveryStatus::DileveryStatusList($data->send_result);  }   
                                     
                                 ],
//                                        [
//                                            'class' => 'kartik\grid\EditableColumn',
//                                            'attribute' => 'name',
//                                            'pageSummary' => 'Page Total',
//                                            'vAlign'=>'middle',
//                                            'headerOptions'=>['class'=>'kv-sticky-column'],
//                                            'contentOptions'=>['class'=>'kv-sticky-column'],
//                                            'editableOptions'=>['header'=>'Name', 'size'=>'md']
//                                        ],
//                                        [
//                                            'attribute'=>'color',
//                                            'value'=>function ($model, $key, $index, $widget) {
//                                                return "<span class='badge' style='background-color: {$model->color}'> </span>  <code>" . 
//                                                    $model->color . '</code>';
//                                            },
//                                            'filterType'=>GridView::FILTER_COLOR,
//                                            'vAlign'=>'middle',
//                                            'format'=>'raw',
//                                            'width'=>'150px',
//                                            'noWrap'=>true
//                                        ],
//                                        [
//                                            'class'=>'kartik\grid\BooleanColumn',
//                                            'attribute'=>'status', 
//                                            'vAlign'=>'middle',
//                                        ],
//                                        [
//                                            'class' => 'kartik\grid\ActionColumn',
//                                            'dropdown' => true,
//                                            'vAlign'=>'middle',
//                                            'urlCreator' => function($action, $model, $key, $index) { return '#'; },
//                                            'viewOptions'=>['title'=>$viewMsg, 'data-toggle'=>'tooltip'],
//                                            'updateOptions'=>['title'=>$updateMsg, 'data-toggle'=>'tooltip'],
//                                            'deleteOptions'=>['title'=>$deleteMsg, 'data-toggle'=>'tooltip'], 
//                                        ],
                                       // ['class' => 'kartik\grid\CheckboxColumn']
                                    ];
                                    echo GridView::widget([
                                        'dataProvider' => $MaildeliveryProvider,
                                        'filterModel' => $MaildeliverysearchModel,
                                        'columns' => $gridColumns,
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
                                        'showPageSummary' => true,
                                        'panel' => [
                                            'type' => GridView::TYPE_PRIMARY
                                        ],
                                    ]);
                             
                             ?>
                             
                         
            
         </div>
    
</div>
