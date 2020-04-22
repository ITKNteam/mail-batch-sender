<?php

use yii\bootstrap\Tabs;
use yii\bootstrap\Modal;
use yii\bootstrap\Collapse;
use yii\bootstrap\ActiveForm;


use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\User;

use kartik\grid\GridView;
use yii\helpers\Url;

use kartik\widgets\DatePicker;
use dosamigos\datepicker\DateRangePicker;

 use app\models\Handbook;
use kartik\select2\Select2;
/* 
 * @created at 2016
 * @author Kirill Shavrak.
 * kirill@shavrak.ru
 */

$this->title = 'Статистика по городам';




?>


<div class="content-inner">

    
    
    
    

    
    <br>
    <br>
    <ul class="nav nav-tabs">
    <li  <?php if ($campaign_id== 35) {echo 'class="active"';}  ?> ><a href="/users/simple-stat-2?campaign_id=35">P&S </a></li>
    <li  <?php if ($campaign_id== 57) {echo 'class="active"';}  ?>><a href="/users/simple-stat-2?campaign_id=57">Балканская звезда</a></li>

  </ul>
    <br>
<h2><?= $this->title?></h2>
<?php 
      
    
        
       echo GridView::widget([
           
       // 'filterModel' => $searchModel,
        'dataProvider' => $StatProvider,
        'layout' => "{summary}\n{items}",  
        'columns' => [
   
            
            [
                 'label'=>'Город',
                 'format' => 'html',
                 'attribute'=>'activity_loc',    
                
           
                 'value'=>'activity_loc'
                 //'value'=>function ($data) { return User::userSexName($data->sex);}

                ],
            
            [
                 'label'=>'Валидные',
                 'format' => 'html',
                 'attribute'=>'passed',    
                
           
                 'value'=>'passed',
                'pageSummary'=>true

                ],
            [
                 'label'=>'Не валидные',
                 'format' => 'html',
                 'attribute'=>'failed',    
                
           
                 'value'=>'failed',
                'pageSummary'=>true
             

                ],
            [
                 'label'=>'Нет email',
                 'format' => 'html',
                 'attribute'=>'no_email',    
                
           
                 'value'=>'no_email',
                'pageSummary'=>true
             

                ],
            
            
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
            'showPageSummary' => true,
            'panel' => [
                'type' => GridView::TYPE_PRIMARY
            ],
                    
    ]); ?>
    
<br>
<br>
<br>
<hr>
</div>