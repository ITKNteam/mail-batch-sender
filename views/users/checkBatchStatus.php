<?php

use yii\bootstrap\Progress;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

use kartik\grid\GridView;
use yii\helpers\Url;

use yii\widgets\Pjax;

$this->title = 'West BTL - Статус выполнения задачи';

?>


<h1><?= $this->title?></h1>

<?php
$script = <<< JS
$(document).ready(function() {
    setInterval(function(){ $("#refreshButton").click(); }, 3000);
});
JS;
$this->registerJs($script);
?>



<div class="bs-callout bs-callout-danger">
    <h4>Внимание!</h4>
    <p>На данной странице, вы можете проследить ход выполнения операции загрузки контактов в Unisender.
       Так как, существуют технологические ограничения, загрузка осуществляется пачками по 500 контактов в минуту.
       Данная странца, обновляется автоматически каждые 3 секунды.
        
    </p>
    <p>В таблице ниже, отображаюется статус по каждому контакту.
        
    </p>
  </div>
 




<div class="row">
    <?php Pjax::begin(); ?>

    <div class="col-lg-10 col-md-6">
        
<?php
// styled

//$percent = app\models\CronTask::getPercentContacts($batch_id, $list_id, 'SubscribeContacts');

echo Progress::widget([
    'percent' => $percent,
    'label' => $percent.'%',
    'barOptions' => ['class' => 'progress-bar-success'],
    'options' => ['class' => 'active progress-striped']
]);



?>
       </div> 
        <div class="col-lg-1 col-md-6">
<?= Html::a("Refresh", ["users/check-batch-status?batch_id=$batch_id&list_id=$list_id"], ['class' => 'btn btn-md btn-primary', 'id' => 'refreshButton']) ?>


<?php Pjax::end(); ?>

</div>  
</div>  
   
    

<?= 
GridView::widget([
      'dataProvider' => $provider,
      'layout' => "{summary}\n{items}",  
      'columns' => [
           'email',
            'message',
            [
                 'label'=>'Дата создания',
                 'format' => 'raw',
                 'value'=>function ($data) {return date("d.m.Y h:i:s",  strtotime($data->dt_create)); },
                 
             ], 
          
           //'dt_create',
          
          
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
  ])
//GridView::widget([
//      'dataProvider' => $provider,
//      'layout' => "{summary}\n{items}",  
//      'columns' => [
//           'total',
//            'inserted',
//            'updated',
//            'deleted',
//            'new_emails',
//            'invalid',
//           //'dt_create',
//          
//          
//      ],
//  ])
        
        ?>