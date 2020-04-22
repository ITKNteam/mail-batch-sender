<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\AuthItem;
use app\models\Handbook;
use kartik\select2\Select2;


/* @var $this yii\web\View */
/* @var $model app\models\Users */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tasks-form">
<h2>Редактирование пользователя</h2>
    <?php $form = ActiveForm::begin(
            [
             'id' => 'user',  
              'enableAjaxValidation' => false,
            ]); ?>

     <?= $form->field($model, 'email')->textInput(  [
                         'inputOptions' => [  'autocomplete' => 'off',  ],
                         'enableAjaxValidation' => true, 
                       
                    ]) ?>

      <?= $form->field($model, 'role')->dropDownList(AuthItem::getRoleList(), ['rows' => 3]) ?>
      <?= $form->field($model, 'status_id')->dropDownList([1=>'Активный', 0=>'Заблокирован'], ['rows' => 3]) ?>

     <?php
            echo $form->field($model, 'cityes')->widget(Select2::classname(), [
            'data' => Handbook::getValueListKey(1),
            'language' => 'ru',
            'options' => ['multiple' => true, 'placeholder' => 'Выбор городов для списка ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

           ?>    


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
