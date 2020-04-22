<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Agency;
use app\models\UnisenderEmail;

/* @var $this yii\web\View */
/* @var $model app\models\UnisenderList */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="unisender-list-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'agency_id')->dropDownList(Agency::getList(), ['rows' => 3]) ?>
    <?= $form->field($model, 'message_id')->dropDownList(UnisenderEmail::getEmailsList(), ['rows' => 3]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    


    

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
