<?php

/* @var $this yii\web\View */


use app\models\CrmUsers;
use app\models\CronTask;


$this->title = 'Crm load';
?>

<?php


 $batch_id = 55;
 $agency_id = 2;
 $step = 1;
 
 $res = 0;
 
 $task_name ='checkEmailAvability';
 $step_limit = 5;
         
 
  
 // CronTask::addTask('insertUsers', $batch_id, 500);
 // CronTask::addTask($task_name, $batch_id, $step_limit);
 // CronTask::startTask(1);
 
 $res = CronTask::work();
 
 // $res = CrmUsers::insertUsers($batch_id,  $step).'<br>';
//  $res = CrmUsers::checkEmailAvability($batch_id,  1);
 // $res .= CrmUsers::RegisterBasic($batch_id);
 // $res .= CrmUsers::UpdateProfile($batch_id);
 // $res .= CrmUsers::FillSmokingHabbits($batch_id);
 
 ?>

<pre>
    <?php
      print_r($res);   
    ?>
</pre>
