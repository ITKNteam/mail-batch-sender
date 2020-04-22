<?php
/* @var $this yii\web\View */
$this->title = 'West BTL';
use yii\bootstrap\Tabs;
use yii\bootstrap\Modal;
use yii\bootstrap\Collapse;
use yii\bootstrap\ActiveForm;


use yii\helpers\Html;
use yii\helpers\ArrayHelper;

//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\helpers\Url;

 

?>

<div class="content-inner">
    <h3 class="title">Просмотр результата загрузки</h3>
    <div class="user-block clearfix">
     <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
     ?>
    
    </div>
    
    
     <p>
       <?= Html::a('Венуться в архив загрузок', ['batches'], ['class' => 'btn btn-success']) ?>
       
          
    </p>
    

    <?php 
    
    
        
      echo GridView::widget([
           
        'dataProvider' => $CsvProvider,
        'columns' => $columns,
           //'containerOptions' => ['style'=>'overflow: auto'], // only set when $responsive = false
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
            'bordered' => false,
            'striped' => true,
            'condensed' => false,
            'responsive' => false,
            'hover' => false,
            'floatHeader' => true,
          
            'showPageSummary' => false,
            'panel' => [
                'type' => GridView::TYPE_ACTIVE
            ],
              
    ]); ?>
    
    
    </div>
    




    

