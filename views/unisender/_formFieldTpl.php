<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\UnisenderFields;

/* @var $this yii\web\View */
/* @var $model app\models\UnisenderList */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="unisender-list-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'u_field_id')->dropDownList(UnisenderFields::getList(), ['rows' => 3]) ?>
    
    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>


    

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
