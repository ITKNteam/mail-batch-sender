<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\searchModels\AgencySettingsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="agency-settings-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'agency_id') ?>

    <?= $form->field($model, 'sys_parametr_id') ?>

    <?= $form->field($model, 'value') ?>

    <?= $form->field($model, 'row_order') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
