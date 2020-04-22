<?php
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;


$this->title = 'Статистика по списку';

//use yii\grid\GridView;
?>
<div class="row">
    <hr>
</div>
<div class="row">
    <div class="col-lg-12">
       <?= Html::a('Венуться', ['/unisender'], ['class' => 'btn btn-success']) ?>
       <?php 
        Modal::begin([
            'header' => '<h2>Получение контактов</h2>',
            'toggleButton' => ['label' => 'Экспорт из Unisender', 'class' => 'btn btn-info'],
                ]);

               ?>
        
        <div class="bs-callout bs-callout-warning">
        <h4>Внимание! Данная задача может занять некоторе время.</h4>
        <p>Нажав кнопку <b>Начать</b>, дождитесь пока страница не обновиться автоматически.
        <br>Данную операцию, рекомендуется проводить переодически, для того чтобы уточнять статус контактов.
        </p>

    
  </div>
        <?= Html::a('Начать', ['unisender/list-stat', 'list_id'=>$list_id, 'agency_id'=>$agency_id, 'request_stat'=>1], ['class' => 'btn btn-success']) ?>
        <?
               
            

             Modal::end();
       
       ?>
        </div>
      
</div>
<div class="row">
    <hr>
</div>
<div class="user-block clearfix">
     <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
     ?>
    <div class="bs-callout bs-callout-danger">
        <h4>Внимание!</h4>
        
        <p><b>Доступность e-mail адреса </b> по результатам последних рассылок. Отправка будет производиться только по доступным адресам.
        </p>
        <p>Рекомендуется делать экспорт из Unisender, для того, чтобы оценить кол-во корректных email адресов, на которые будут оправлены письма.
        </p>

    
  </div>
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
                                        'dataProvider' => $TotalDataProvider,
                                      //  'filterModel' => $MaildeliverysearchModel,
                                        'columns' => $gridColumns,
                                            
                                            'panel'=>['type'=>'primary', 'heading'=>'Общая статитсика по списку'],
                                           'pjax'=>true,
                                            'striped'=>true,
                                            'hover'=>true,
                                        
                                    ]);
                             
                             ?>
                             
                         
            
         </div>
         </div>

<div class="row">
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
//                                  [
//                                            'attribute'=>'email_availability', 
//                                            'width'=>'250px',
//                                             'label'=>'Доступность e-mail адреса',
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
                                            'attribute'=>'email_status_txt', 
                                            'width'=>'400px',
                                            'label'=>'Статус e-mail адреса',
                                            
                                        ],
                                        [
                                            'attribute'=>'email_availability_txt', 
                                            'width'=>'400px',
                                            'label'=>'Доступность e-mail адреса',
                                            
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
                                            
                                            'panel'=>['type'=>'primary', 'heading'=>'Статитсика по списку в разрезе городов'],
                                           'pjax'=>true,
                                            'striped'=>true,
                                            'hover'=>true,
                                        
                                    ]);
                             
                             ?>
                             
                         
            
         </div>
         </div>