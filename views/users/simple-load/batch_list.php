<?php

use yii\bootstrap\Progress;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

use yii\grid\GridView;
use app\models\User;

use yii\bootstrap\Modal;
use yii\bootstrap\ActiveForm;
/* @var $this yii\web\View */
/* @var $searchModel app\models\usersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Загрузки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?php  echo $this->title;?>
        </h1>
  
    
    <p>
        <?php 
         Modal::begin([
            'header' => '<h2>Выбор файла</h2>',
            'toggleButton' => ['label' => 'Загрузить', 'class' => 'btn btn-info'],
                ]);

               
            $form = $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
            echo $form->field($csv_upload, 'campaign_name')->textInput(); 
            echo $form->field($csv_upload, 'delimer')->dropDownList([','=>'Разделитель запятая ( ,)', ';'=>'Разделитель точка с запятой ( ;)',], ['rows' => 3]);
             echo $form->field($csv_upload, 'campaign_id')->dropDownList(app\models\UnisenderMailTpl::getCampaignNames(), ['rows' => 3]);
             echo $form->field($csv_upload, 'file')->fileInput();
             
             echo Html::submitButton( 'Загрузка', ['class' =>  'btn btn-info']);
             ActiveForm::end(); 

             Modal::end();

        ?>
      
          
    </p>
    
    <br>
    
        
    
    <ul class="nav nav-tabs">
        <li  <?php if ($campaign== 35) {echo 'class="active"';}  ?> ><a href="/users/simple-batches?campaign=35">P&S </a></li>
        <li  <?php if ($campaign== 57) {echo 'class="active"';}  ?>><a href="/users/simple-batches?campaign=57">Балканская звезда</a></li>
        
      </ul>
    
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
                 
             'format' => 'raw',
             'attribute'=>'campaign_name',  
              'value'=>'campaign_name',    
//                 'value'=>function ($data) {return Html::a( $data->campaign_name, '/users/simple-unisender-validation?batch_id='.$data->id.'&CsvBatchesSearch[batch_id]='.$data->id,
//                                                ['title' => Yii::t('yii', 'Статус'), 'data-pjax' => '0']);
//                            }
                 
             ],   
                        
            [
                 'label'=>'Статус',
                 'format' => 'raw',
                 'value'=>function ($data) {return Html::a( @\app\models\AgencyCsvBatch::getStatus($data->current_step), '/users/simple-load?batch_id='.$data->id,
                                                ['title' => Yii::t('yii', 'Статус'), 'data-pjax' => '0']);
                            }
                 
             ],   
                        
                         
             
             
                     
             [
                 'label'=>'Просмотр файла',
                 'format' => 'raw',
                 'value'=>function ($data) {return Html::a( $data->file_name, '/users/simple-csv-huge?CsvBatchesSearch[batch_id]='.$data->id,
                                                ['title' => Yii::t('yii', 'Просмотр файла'), 'data-pjax' => '0']);
                            }
                 
             ],  
             [
                 'label'=>'Кол-во строк',
                 'format' => 'raw',
                 'value'=>function ($data) {return Html::a( $data->string_count, '/users/simple-csv-report?batch_id='.$data->id,
                                                ['title' => Yii::t('yii', 'Просмотр отчёта'), 'data-pjax' => '0']);
                            }
                 
             ],  
                      
            
          [
                 'label'=>' Загрузил',
                 'format' => 'raw',
                  'value'=> function ($data) {
        //$data['agency']['name'],
                     return $data->createU['username'];
                 },
                
                 
             ],
              'batch_date',
                        
                         
                    [
                 'attribute'=>'batch_comment',
                 'format' => 'raw',
                  'value'=> 'batch_comment',
                  'visible'=> (in_array(Yii::$app->user->id,[11, 23, 15]))            
                
                 
             ],      
                       
                 
              
                
                [
                  'label' =>'Удалить', 
                  'value' => function ($data) 
                  { 
                  return Html::a('<span class="glyphicon glyphicon-trash"></span>', 
                    ['/users/delete-batch', 'id' => $data->id], 
                    ['data' => 
                        ['confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        ],]); 
                  },
                  'format'=>'raw', 
                   'visible'=> (in_array(Yii::$app->user->id,[11, 23, 15]))                      
                ],                                    
                                    
              
          
        ],
    ]); ?>

</div>
