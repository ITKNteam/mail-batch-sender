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

$this->title = 'Системные задачи';
?>

<div class="row">
    &nbsp;
</div>


<div class="row">
    &nbsp;
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
            
        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-6">
                        <a href="/users/simple-batch-tasks?batch_id=<?= $task->batch->id ?>"> 
                        <?= $task->batch->campaign_name ?></a>
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
                    <a  href="/sys/change-task-status?task_id=<?=$task->id?>&status_id=-3" class="btn btn-danger">Стоп</a>
                    <a  href="/sys/change-task-status?task_id=<?=$task->id?>&status_id=1" class="btn btn-info">Пуск</a>
                    <a  href="/sys/change-task-status?task_id=<?=$task->id?>&status_id=0" class="btn btn-success">Завершить</a>
                    
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

