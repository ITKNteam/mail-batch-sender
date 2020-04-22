<?php
/* @var $this yii\web\View */
$this->title = 'West BTL';
use yii\bootstrap\Tabs;
use yii\bootstrap\Modal;
use yii\bootstrap\Collapse;
use yii\bootstrap\ActiveForm;


use yii\helpers\Html;
use yii\helpers\ArrayHelper;

use yii\grid\GridView;
//use kartik\grid\GridView;
use yii\helpers\Url;

 

?>

<div class="content-inner">
    <h3 class="title">Просмотр загрузки</h3>
    <div class="user-block clearfix">
     <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
     ?>
    
    </div>
    
    
     <p>
      
      
          <?php 
          if ($batch_id!=0){
              
              echo Html::a('Удалить загрузку', ['delete-batch?id='.$batch_id], ['class' => 'btn btn-danger']);
              echo '&nbsp;&nbsp;&nbsp;';
              
         Modal::begin([
            'header' => '<h2>Отправка контактов</h2>',
            'toggleButton' => ['label' => 'Отправить в Unisender', 'class' => 'btn btn-info'],
                ]);

                
               
            $form = $form = ActiveForm::begin([
            'id' => 'acept-form',
            'method' => 'post',
            'action' => ['users/acept-batch']]);
             echo $form->field($model, 'batch_id')->hiddenInput();
          
             echo $form->field($model, 'agency_id')->dropDownList(app\models\Agency::getList(), ['rows' => 3]);
                echo $form->field($model, 'list_id')->dropDownList(app\models\UnisenderList::getListName(), ['rows' => 3]);
             echo Html::submitButton( 'Отправить в Unisender', ['class' =>  'btn btn-info']);
             ActiveForm::end(); 

             Modal::end();
          }
        ?>
          
    </p>
    
    
     <p>
       <?= Html::a('Венуться в архив загрузок', ['batches'], ['class' => 'btn btn-success']) ?>
       
          
    </p>
    
    
    <?php 
    
    
    
    
        
      echo GridView::widget([
           
        'dataProvider' => $CsvProvider,
        'columns' => $columns,
           //'containerOptions' => ['style'=>'overflow: auto'], // only set when $responsive = false
//           'beforeHeader'=>[
//                [
//
//                    'options'=>['class'=>'skip-export'] // remove this row from export
//                ]
//            ],
//            'toolbar' =>  [
////                                            ['content'=>
////                                                Html::button('&lt;i class="glyphicon glyphicon-plus">&lt;/i>', ['type'=>'button', 'title'=>Yii::t('kvgrid', 'Add Book'), 'class'=>'btn btn-success', 'onclick'=>'alert("This will launch the book creation form.\n\nDisabled for this demo!");']) . ' '.
////                                                Html::a('&lt;i class="glyphicon glyphicon-repeat">&lt;/i>', ['grid-demo'], ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>Yii::t('kvgrid', 'Reset Grid')])
////                                            ],
//                '{export}',
//                '{toggleData}'
//            ],
        //    'pjax' => true,
       //     'bordered' => false,
        //    'striped' => true,
      //      'condensed' => false,
       //     'responsive' => false,
       //     'hover' => false,
        //    'floatHeader' => true,
           // 'floatHeaderOptions' => ['scrollingTop' => $scrollingTop],
         //   'showPageSummary' => false,
//            'panel' => [
//                'type' => GridView::TYPE_PRIMARY
//            ],
              
    ]); ?>
    
    
    </div>
    




    

