<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Users */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="col-lg-12">
    <?php if(Yii::$app->session->hasFlash('recovery_message')): ?>
        <div class="alert alert-info" role="alert">
            <?= Yii::$app->session->getFlash('recovery_message') ?>
        </div>
    <?php endif; ?>
    <?php if(Yii::$app->session->hasFlash('recovery_message_bad')): ?>
        <div class="alert alert-danger" role="alert">
            <?= Yii::$app->session->getFlash('recovery_message_bad') ?>
        </div>
    <?php endif; ?>
</div>

<h2>Востановление пароля</h2>
<div class="tasks-form">

    <?php $form = ActiveForm::begin( [ 'id' => 'email-form', 'enableAjaxValidation' => false,] ); ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'enableAjaxValidation' => true, [ 'inputOptions' => [  'autocomplete' => 'off',  ]]]) ?>

    <div class="form-group">
        <?= Html::submitButton('Далее', ['class' =>  'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
