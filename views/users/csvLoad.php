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
    <h3 class="title">Загрузка </h3>
    
    
    <a href="https://www.dropbox.com/s/g13ukh1ehri3sbx/manual-daily_mailing.pdf?dl=0"><b>Инструкция по повседневной загрузке  </b></a><br><br>
    
    <div class="user-block clearfix">
     <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
     ?>
    
    </div>
    
      
<?php 
$date_end = date("d.m.Y", strtotime("15.12.2015"));

if ( $date_end >=  date("d.m.Y") ) :
?>
<div class="bs-callout bs-callout-danger">
    <h4>Внимание! </h4>
    <p>При создании CSV файла, необходимо указать разделить запятая ( ,).
    <br>Если такой опции нет в вашем Excel или по каким-то иным причнам, был создан файл с разделителем точка с зяпятой,
    то, при загрузке файла, выберите необходимый разделитель из выпадющего списка.
        
        <br><img src="http://westbtl.dev.shavrak.ru/data/uploads/2015-12-14%2017-36-39%20West%20BTL.png">
    </p>
    <small>Данное сообщение будет скрыто, после <?= $date_end?></small>
    
  </div>
<div class="bs-callout bs-callout-warning">
    <h4>Внимание! Произведены обновления в сиcтеме.</h4>
    <p>Обратите внимание, изменился порядок отображения списков контактов, в всплывающем окне "Отправка контактов". 
    <br>Теперь, самые последние записи отображаются вначале.
    </p>
    <small>Данное сообщение будет скрыто, после <?= $date_end?></small>
    
  </div>
<?php  endif;?>
    
    
     <p>
        <?php 
         Modal::begin([
            'header' => '<h2>Выбор файла</h2>',
            'toggleButton' => ['label' => 'Загрузить', 'class' => 'btn btn-info'],
                ]);

                
               
            $form = $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
             echo $form->field($csv_upload, 'agency_id')->dropDownList(app\models\Agency::getList(), ['rows' => 3]);
             echo $form->field($csv_upload, 'delimer')->dropDownList([','=>'Разделитель запятая ( ,)', ';'=>'Разделитель точка с запятой ( ;)',], ['rows' => 3]);
             echo $form->field($csv_upload, 'file')->fileInput();
             echo Html::submitButton( 'Загрузка', ['class' =>  'btn btn-info']);
             ActiveForm::end(); 

             Modal::end();

        ?>
       <?php // echo Html::a('Принять загрузку', ['create?m=code'], ['class' => 'btn btn-success']) ?>

      
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
    

    <?php 
        
      echo GridView::widget([
           
        'dataProvider' => $CsvProvider,
        'columns' => $columns
    ]); ?>
    
    
    </div>
    




    

