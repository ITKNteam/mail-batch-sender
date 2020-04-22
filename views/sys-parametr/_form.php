<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysParametr */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-parametr-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sys_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'data_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'group_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
