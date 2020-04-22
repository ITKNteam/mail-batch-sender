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
use kartik\grid\GridView;

$this->title = 'Просмотр валидации по всем файлам';
?>
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
    
    
</div>

<div class="row">
    &nbsp;<br>
    &nbsp;<br>
</div>
<div class="row">

</div>


<div class="row">
<div class="col-lg-12">
           
            <?php
           
                   echo GridView::widget([
                     //  'language'=>'ru',
                       'dataProvider' => $dataProvider,
                     //  'filterModel' => $MaildeliverysearchModel,
                       'columns' => $gridColumns,

                           'panel'=>['type'=>'primary', 'heading'=>'Общая статитсика по всем файлам'],
                          'pjax'=>true,
                           'striped'=>true,
                           'hover'=>true,

                   ]);

            ?>
                             
                         
            
         </div>
         </div>

