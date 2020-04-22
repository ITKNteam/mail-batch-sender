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
use yii\helpers\Url;


?>

<div class="content-inner">
    <h3 class="title">Моя страница</h3>
    <div class="user-block clearfix">
            <div class="user-info">
                <p class="name"><?php echo $user->username?></p>
            </div>

   
        </div>
    
    
     <p>
       <?= Html::a('Отправить в CRM', ['create?m=code'], ['class' => 'btn btn-success']) ?>
       <?= Html::a('Удалить загрузку', ['create?m=code'], ['class' => 'btn btn-danger']) ?>
        <?php 
         Modal::begin([
            'header' => '<h2>Выбор файла</h2>',
            'toggleButton' => ['label' => 'Загрузить', 'class' => 'btn btn-info'],
                ]);

                
               
            $form = $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
               echo $form->field($csv_upload, 'file')->fileInput();
               echo Html::submitButton( 'Загрузка', ['class' =>  'btn btn-info']);
             ActiveForm::end(); 

             Modal::end();

        ?>
          
    </p>
    

    <?php 
      
    
        
       echo GridView::widget([
           
        'dataProvider' => $CsvDataProvider,
        'columns' => [
   

            'id',
            
              'f_name',
            'l_name',
            'p_name',
             'email',
            'phone',
            'age',
            'gender',
            'priority_mark1',
            'priority_mark2',
            'hostess_id',
            'activity_dt',
            'activity_loc',
            'test_res',
            'test_id',
            
        ],
    ]); ?>
    
    
    </div>
    




    

