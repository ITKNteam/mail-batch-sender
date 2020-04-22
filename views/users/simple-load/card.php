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

$this->title = @\app\models\AgencyCsvBatch::getStatus($batch_model->current_step);

?>
  <br>
    <ul class="nav nav-tabs">
    <li  class="active" ><a href="/users/simple-load?batch_id=<?=$batch_model->id?>">Статус рассылки</a></li>
    <?php if (Yii::$app->user->id == 11) {?> 
      <li  ><a href="/users/simple-unisender-validation?batch_id=<?=$batch_model->id?>&CsvBatchesSearch[batch_id]=<?=$batch_model->id?>">Повторная валидация</a></li>
    <?php } ?> 
    <li  ><a href="/users/simple-csv-huge?CsvBatchesSearch[batch_id]=<?=$batch_model->id?>">Просмотр файла</a></li>
    <li  ><a href="/users/simple-csv-report?batch_id=<?=$batch_model->id?>">Просмотр валидации</a></li>
    <?php if (in_array(Yii::$app->user->id,[11, 23, 15])) {?> 
       <li  ><a href="/users/simple-batch-tasks?batch_id=<?=$batch_model->id?>">Системные задачи</a></li>
       <li  ><a href="/users/simple-crm-tasks?CrmUsersSearch[batch_id]=<?=$batch_model->id?>">Задачи CRM</a></li>
    <?php } ?> 
    

  </ul>

<div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"><?= $this->title?></h1>
                </div>
               <div class="col-lg-12">
                    <?php
                       foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
                       echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
                       }
                    ?>
                </div>
                <!-- /.col-lg-12 -->
                
                
            </div>

     <?php if (in_array(Yii::$app->user->id,[11, 23, 15])){?> 
  <br>

             
        <div class="row">
                                
            <div class="col-lg-12">


              <?php 
              $form = $form = ActiveForm::begin(
                    ['action' => ['users/simple-comment-batch'],
                     'options' => ['enctype' => 'multipart/form-data']]);
              echo $form->field($batch_model, 'id')->hiddenInput();
              echo $form->field($batch_model, 'batch_comment')->textarea();
              echo Html::submitButton('Создать', ['class' =>  'btn btn-info']);
              ActiveForm::end(); 

              ?>
            </div>

        </div>
  
  <br>
      <?php } ?> 
    

            <div class="row">
                <div class="col-lg-12">
                     
                    
                    
               <?php if ($batch_model->current_step == 1) {?>     
                     <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-envelope-o fa-fw"></i> Подготовка рассылки
                            <div class="pull-right">
                                <div class="btn-group">
                                    
                                    
                                </div>
                            </div>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="row">
                                
                                <div class="col-lg-12">
                               
                                
                                  <?php 
                                  $form = $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
                                    echo $form->field($batch_model, 'campaign_name')->textInput();
                                    echo $form->field($batch_model, 'email_tpl_id')->dropDownList([1=>'Тест Gold',2=>'Тест  Blue',3=>'P&S Gold (CODE) ',4=>'P&S Blue (CODE) '], ['rows' => 3]);
                                    
                                    echo Html::submitButton('Создать', ['class' =>  'btn btn-info']);
                                    ActiveForm::end(); 
                                    
                                  ?>
                                </div>
                                
                            </div>
                            <hr>
                            <h2>Варианты шаблонов</h2>
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Тест Gold
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <img src="/data/img/brit_test_gold.png" width="100"> 
                                    </div>
                                    </div>
                                    
                             
                                </div>
                                <div class="col-lg-3">
                                    <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Тест  Blue
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <img src="/data/img/brit_test_blue.png" width="100"> 
                                    </div>
                                    </div>
                                    
                             
                                </div>
                                <div class="col-lg-3">
                                    <div class="panel panel-default">
                                    <div class="panel-heading">
                                        P&S Gold (CODE) 
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <img src="/data/img/code_gold.png" width="100"> 
                                    </div>
                                    </div>
                                    
                             
                                </div>
                                <div class="col-lg-3">
                                    <div class="panel panel-default">
                                    <div class="panel-heading">
                                        P&S Blue (CODE)
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <img src="/data/img/code_blue.png" width="100"> 
                                    </div>
                                    </div>
                                    
                             
                                </div>
                                
                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    
               <?php } ?>     
               <?php if ($batch_model->current_step >= 6) {
                   
                       $query = \app\models\UnisenderList::find();
                        $query->andFilterWhere(['batch_id'=>$batch_model->id]);
                      //  $query->andFilterWhere(['validate_status'=>'passed']);

                     $dataProvider = new ActiveDataProvider([
                         'query' => $query,
                         'pagination' => [
                                 'pageSize' => 20,
                             ],
                         
                     ]);

                   
                   ?>     
                     <!-- /.panel -->
                   <?= ListView::widget([
                            'dataProvider' => $dataProvider,
                       'options' => [
                            'tag' => 'ul',
                            'class' => 'timeline',
                            'id' => '',
                        ],
                       'layout' => "{items}",
                            'itemOptions' => ['class' => '',  'tag' => 'li',],
                            'itemView' => '_stat_item',

                        ]) ?>

               <?php } ?>     
                    
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-clock-o fa-fw"></i> История операциий 
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            
                               <?= ListView::widget([
                                        'dataProvider' => $StepsDataProvider,
                                   'options' => [
                                        'tag' => 'ul',
                                        'class' => 'timeline',
                                        'id' => '',
                                    ],
                                   'layout' => "{items}",
                                        'itemOptions' => ['class' => '',  'tag' => 'li',],
                                        'itemView' => '_timline_item',
                                        
                                    ]) ?>

                             
                            
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
                
            </div>
            <!-- /.row -->
           