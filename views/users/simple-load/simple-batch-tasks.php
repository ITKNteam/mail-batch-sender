<?php

/* 
 * @created at 2016
 * @author Kirill Shavrak.
 * kirill@shavrak.ru
 */


use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;

use yii\data\SqlDataProvider;

use yii\bootstrap\Progress;

$this->title = 'Задачи в этой пачке';



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
    <li class="active" ><a href="/users/simple-batch-tasks?batch_id=<?=$batch_id?>">Системные задачи</a></li>
    <li  ><a href="/users/simple-crm-tasks?CrmUsersSearch[batch_id]=<?=$batch_id?>">Задачи CRM</a></li>
<?php } ?> 
  </ul>
 
 <br>
 
<div class="row">
    <div class="col-lg-12">
                    <?php
                       foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
                       echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
                       }
                    ?>
                </div>
                
                    <div class="col-lg-6">
                  <a  href="/users/rerun-validation?batch_id=<?=$batch_id?>" class="btn btn-default">Повтор валидации <br> Bulemailvalidator error 
        </a>

                
             
                </div>
                    <div class="col-lg-6">
            
        <a href="/users/rerun-validation?batch_id=<?=$batch_id?>&validate_statuses=all" class="btn btn-default">
            Повтор валидации <br> Bulemailvalidator весь файл</a>
     
                
             
                </div>
                
    <div class="col-lg-12">
                <hr>
                </div>
    
                <div class="col-lg-3">
                   <a href="/sys/start-task?batch_id=<?=$batch_id?>&mode=4&redirect_url=/users/simple-batch-tasks" class="btn btn-default">Подготвить пачку<br> для повторной подписки</a>
             
                </div>
                <div class="col-lg-3">
                      <a href="/sys/start-task?batch_id=<?=$batch_id?>&mode=1&redirect_url=/users/simple-batch-tasks" class="btn btn-default">Создать спиок</a>
                </div>
                <div class="col-lg-3">
                       
                      <a href="/sys/start-task?batch_id=<?=$batch_id?>&mode=2&redirect_url=/users/simple-batch-tasks" class="btn btn-default">Запускс подписки</a>
                </div>
                <div class="col-lg-3">
                      <a href="/sys/start-task?batch_id=<?=$batch_id?>&mode=3&redirect_url=/users/simple-batch-tasks" class="btn btn-default">Экспорт из Unisender</a>
                </div>
    
                
    
</div>
 <hr>
 <div class="row">
     <div class="col-lg-6 col-md-6">
        <?php
            $BatchTaskStatusColumns = [
            
               [
                    'attribute'=>'status_name', 
                    'width'=>'200px',
                    'label'=>'Статус',
                    'group'=>true,

                ],
                [
                    'attribute'=>'quant', 
                    'width'=>'200px',
                    'label'=>'Кол-во',

                ],

            ];
            echo GridView::widget([
                'dataProvider' => $BatchTaskStatusProvider,
                'columns' => $BatchTaskStatusColumns,

                    'panel'=>['type'=>'primary', 'heading'=>'Статусы операций'],
                   'pjax'=>true,
                    'striped'=>true,
                    'hover'=>true,

            ]);


?> 
     </div>
     <div class="col-lg-6 col-md-6">
         <?php 
        
         
                 $ValidateStatusColumns = [
            
               [
                    'attribute'=>'validate_status', 
                    'width'=>'75px',
                    'label'=>'validate_status',
                    'group'=>true,

                ],
               [
                    'attribute'=>'email_availability', 
                    'width'=>'75px',
                    'label'=>'email_availability',
                    'group'=>true,

                ],
               [
                    'attribute'=>'total_rus', 
                    'width'=>'75px',
                    'label'=>'total_rus',
                    'group'=>true,

                ],
              
                [
                    'attribute'=>'quant', 
                    'width'=>'50px',
                    'label'=>'Кол-во',

                ],

            ];
            echo GridView::widget([
                'dataProvider' => $ValidateStatusProvider,
                'columns' => $ValidateStatusColumns,

                    'panel'=>['type'=>'primary', 'heading'=>'Статусы проверок'],
                   'pjax'=>true,
                    'striped'=>true,
                    'hover'=>true,

            ]);
                 
         
         ?>
     </div>
  </div>
<div class="row">
    <div class="col-lg-6 col-md-6">
        <?php
        
        foreach ($CronModelActive as $task ){
            
            $status_name = 'В работе';
            if ($task->status_id == -3 ){
                $status_name = 'Остановлена';
            }
            if ($task->status_id == -4 ){
                $status_name = 'Ожидает запуска';
            }    
            $percent = 0;        
            if($task->steps_count)
               $percent = ceil(($task->current_step/$task->steps_count)*100);
            ?>
            
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-6">
                        <?= $task->batch->campaign_name ?>
                        <br> Статус : <b><?= $status_name?></b>
                        <br> Кол-во строк : <b><?= $task->batch_rows_count?></b>
                    </div>
                    <div class="col-xs-6 text-right">
                       Задача: <?= $task->task_name ?>
                       Выполнена <?php echo $percent.'% ';?> 
                    </div>
                </div>
            </div>
            <a href="#">
                <div class="panel-footer">
                    <?php
                    
                    
//                     'steps_count' => 'Steps Count',
//            'current_step' => 'Current Step',
                    echo Progress::widget([
                        'percent' => $percent,
                        'label' => $percent.'% выполненно',
                        'barOptions' => ['class' => 'progress-bar-success'],
                        'options' => ['class' => 'active progress-striped']
                    ]);
                    ?>
                    <div class="clearfix"></div>
                    <a  href="/sys/change-task-status?task_id=<?=$task->id?>&status_id=-3&redirect_url=/users/simple-batch-tasks&batch_id=<?=$batch_id?>" class="btn btn-danger">Стоп</a>
                    <a  href="/sys/change-task-status?task_id=<?=$task->id?>&status_id=1&redirect_url=/users/simple-batch-tasks&batch_id=<?=$batch_id?>" class="btn btn-info">Пуск</a>
                    <a  href="/sys/change-task-status?task_id=<?=$task->id?>&status_id=0&redirect_url=/users/simple-batch-tasks&batch_id=<?=$batch_id?>" class="btn btn-success">Завершить</a>
                    
                </div>
            </a>
        </div>
        
            <?php
            
        }
        
          
        ?>
        
      
    </div>
    <div class="col-lg-6 col-md-6">
          <?php
            $SubscribeSpeedColumns = [
            
               [
                    'attribute'=>'minutes', 
                    'width'=>'200px',
                    'label'=>'Минута',
                    'group'=>true,

                ],
                [
                    'attribute'=>'quant', 
                    'width'=>'200px',
                    'label'=>'Кол-во',

                ],

            ];
            echo GridView::widget([
                'dataProvider' => $SubscribeSpeedProvider,
                'columns' => $SubscribeSpeedColumns,

                    'panel'=>['type'=>'primary', 'heading'=>'Скорость метода Subscribe'],
                   'pjax'=>true,
                    'striped'=>true,
                    'hover'=>true,

            ]);


?>
        
    </div>
    
</div>