<?php
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;


$batch_name = $batch->campaign_name;

$this->title = 'Отчет '.$batch_name;
/* 
 * @created at 2016
 * @author Kirill Shavrak.
 * kirill@shavrak.ru
 */

http://westbtl.dev/users/simple-csv-report?batch_id=161&activity_loc=%D0%92%D0%BE%D1%80%D0%BE%D0%BD%D0%B5%D0%B6
   
?>

<br>
    <ul class="nav nav-tabs">
      <li   ><a href="/users/simple-load?batch_id=<?=$batch->id?>">Статус рассылки</a></li>
    <?php if (Yii::$app->user->id == 11) {?> 
       <li  ><a href="/users/simple-unisender-validation?batch_id=<?=$batch->id?>&CsvBatchesSearch[batch_id]=<?=$batch->id?>">Повторная валидация</a></li>
    <?php } ?> 
    <li  ><a href="/users/simple-csv-huge?CsvBatchesSearch[batch_id]=<?=$batch->id?>">Просмотр файла</a></li>
    <li class="active" ><a href="/users/simple-csv-report?batch_id=<?=$batch->id?>">Просмотр валидации</a></li>
     <?php if (in_array(Yii::$app->user->id,[11, 23, 15])) {?> 
       <li  ><a href="/users/simple-batch-tasks?batch_id=<?=$batch->id?>">Системные задачи</a></li>
       <li  ><a href="/users/simple-crm-tasks?CrmUsersSearch[batch_id]=<?=$batch->id?>">Задачи CRM</a></li>
    <?php } ?> 

  </ul>


<br><br>
<ol class="breadcrumb">
  <li><a href="/users/simple-batches">Загрузки</a></li>
  <li><a href="/users/simple-csv-report?batch_id=<?=$batch->id?>"><?=$batch_name?></a></li>
  <?php echo ($activity_loc == '') ?   '':   '<li class="active">'.$activity_loc.'</li>'; ?>
</ol>

<?php
echo GridView::widget([
                        //  'language'=>'ru',
                          'dataProvider' => $dataProvider,
                        //  'filterModel' => $MaildeliverysearchModel,
                          'columns' => $gridColumns,

                             'panel'=>['type'=>'primary', 'heading'=>'Общая статитсика по списку'],
                             'pjax'=>true,
                              'striped'=>true,
                              'hover'=>true,

                      ]);


?>