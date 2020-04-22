<?php

use yii\helpers\Html;
use kartik\grid\GridView;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Отчет по всем рассылкам';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unisender-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

        
<?php 
$date_end = date("d.m.Y", strtotime("15.12.2015"));

if ( $date_end >=  date("d.m.Y") ) :
?>
<div class="bs-callout bs-callout-warning">
    <h4>Внимание! Произведены обновления в сиcтеме.</h4>
    <p>Обратите внимание, изменился порядок отображения списка. 
    <br>Теперь, самые последние записи отображаются вначале.
    </p>
    <small>Данное сообщение будет скрыто, после <?= $date_end?></small>
    
  </div>
<?php  endif;?>
    
    
    <div class="pull-right">
          
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
<div class="row">
    <div class="col-lg-6">
           
                             <?php
                             $gridColumnsbatch = [
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
                                        [
                                            'attribute'=>'email_t', 
                                            'width'=>'400px',
                                            'label'=>' Наличие e-mail адреса',
                                            
                                        ],
                                      
                                        [
                                            'attribute'=>'c', 
                                            'width'=>'150px',
                                            'label'=>'Кол-во',
                                            
                                        ],
                                        
                                    
                                 
                                    ];
                                    echo GridView::widget([
                                      //  'language'=>'ru',
                                        'dataProvider' => $BatchDataProvider,
                                      //  'filterModel' => $MaildeliverysearchModel,
                                        'columns' => $gridColumnsbatch,
                                            
                                            'panel'=>['type'=>'primary', 'heading'=>'Статистика по загруженным анкетам '],
                                           'pjax'=>true,
                                            'striped'=>true,
                                            'hover'=>true,
                                        
                                    ]);
                             
                             ?>
        
        
        </div>
    <div class="col-lg-6">
           
                             <?php
                             $totalgridColumnsbatch = [
                                        ['class' => 'kartik\grid\SerialColumn'],
                                       
                                        [
                                            'attribute'=>'email_t', 
                                            'width'=>'400px',
                                            'label'=>' Наличие e-mail адреса',
                                            
                                        ],
                                      
                                        [
                                            'attribute'=>'c', 
                                            'width'=>'150px',
                                            'label'=>'Кол-во',
                                            
                                        ],
                                        
                                    
                                 
                                    ];
                                    echo GridView::widget([
                                      //  'language'=>'ru',
                                        'dataProvider' => $TotalBatchDataProvider,
                                      //  'filterModel' => $MaildeliverysearchModel,
                                        'columns' => $totalgridColumnsbatch,
                                            
                                            'panel'=>['type'=>'primary', 'heading'=>'Статистика по загруженным анкетам'],
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
                             $TotalgridColumns = [
                                        ['class' => 'kartik\grid\SerialColumn'],
                                        
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
                                        'columns' => $TotalgridColumns,
                                            
                                            'panel'=>['type'=>'primary', 'heading'=>$this->title],
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
                                            
                                            'panel'=>['type'=>'primary', 'heading'=>$this->title.'по городам' ],
                                           'pjax'=>true,
                                            'striped'=>true,
                                            'hover'=>true,
                                        
                                    ]);
                             
                             ?>
                             
                         
            
         </div>
         </div>
</div>
