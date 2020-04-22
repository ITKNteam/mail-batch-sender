<?php

/* 
 * @created at 2016
 * @author Kirill Shavrak.
 * kirill@shavrak.ru
 */

/* @var $this yii\web\View */
$this->title = 'West BTL';

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\User;

use yii\helpers\Url;

use kartik\widgets\DatePicker;
use dosamigos\datepicker\DateRangePicker;

 
use app\models\CrmUsers;

use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

use kartik\grid\GridView;

use yii\data\SqlDataProvider;

use yii\bootstrap\Progress;

$this->title = 'CRM';

// $batch_id = $searchModel->id;

?>
 <br>
    <ul class="nav nav-tabs">
    <li   ><a href="/users/simple-load?batch_id=<?=$batch_id?>">Статус рассылки</a></li>
        <?php if (Yii::$app->user->id == 11) {?> 
    <li  ><a href="/users/simple-unisender-validation?batch_id=<?=$batch_id?>&CsvBatchesSearch[batch_id]=<?=$batch_id?>">Повторная валидация</a></li>
    <?php } ?> 
    <li  ><a href="/users/simple-csv-huge?CsvBatchesSearch[batch_id]=<?=$batch_id?>">Просмотр файла</a></li>
    <li  ><a href="/users/simple-csv-report?batch_id=<?=$batch_id?>">Просмотр валидации</a></li>
    <?php if (in_array(Yii::$app->user->id,[11, 23, 15])) {?> 
    <li ><a href="/users/simple-batch-tasks?batch_id=<?=$batch_id?>">Системные задачи</a></li>
    <li class="active" ><a href="/users/simple-crm-tasks?CrmUsersSearch[batch_id]=<?=$batch_id?>">Задачи CRM</a></li>
<?php } ?> 
  </ul>
 
 <br>
 
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
                  <a  href="/users/simple-crm-tasks?batch_id=<?=$batch_id?>&mode=start" class="btn btn-default">Запуск задач
        </a>
       
             
                </div>
               
                <div class="col-lg-3">
                  <a  href="/users/simple-crm-tasks?batch_id=<?=$batch_id?>&mode=massive" class="btn btn-default">Массовая загрузка
        </a>
       
             
                </div>
               
    
                
    
</div>
 <hr>
 <div class="row">
     <?php 
      
    
        
       echo GridView::widget([
           
        'filterModel' => $searchModel,
        'dataProvider' => $dataProvider,
           'layout' => "{summary}\n{items}",  
        'columns' => [
   
            
            'f_name',
            'l_name',
            'email',
            'phone',
            
            
               [
                
                'format' => 'raw',
                'attribute'=>'user_key',    
                
                   
                    'value'=>function ($data) {return Html::a($data->user_key, 
                                                    'http://westcrm.pro.shavrak.ru/index.php?module=Contacts&action=DetailView&record='.$data->user_key,
                                                ['title' => Yii::t('yii', 'Статус'), 'target' => 'blank']);
                            } 
                   
                
                

               ],
             [
             'label'=>'Статус',
             'format' => 'raw',
             'attribute'=>'rec_status',    
             'filter'=> app\models\CrmUsers::statusList(),
             'value'=>function ($data) { return CrmUsers::statusListName($data->rec_status);}
             //'value'=>function ($data) { return User::userSexName($data->sex);}

            ],

         
//
//               [
//                'label'=>'Город',
//                'format' => 'raw',
//                'attribute'=>'activity_loc',    
//                'filter'=> \app\models\Handbook::getValueListKey(1), 
//                'value'=>'activity_loc'
//                
//
//               ],     
             
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
    




    

