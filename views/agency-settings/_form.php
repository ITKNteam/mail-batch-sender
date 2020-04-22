<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\SysParametr;
use app\models\Agency;

/* @var $this yii\web\View */
/* @var $model app\models\AgencySettings */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="agency-settings-form">

    <?php $form = ActiveForm::begin(); ?>

   <?= $form->field($model, 'agency_id')->dropDownList(Agency::getList(), ['rows' => 3]) ?>
    <?= $form->field($model, 'sys_parametr_id')->dropDownList(SysParametr::getList(), ['rows' => 3]) ?>
    <?= $form->field($model, 'value')->textarea(['rows' => 6]) ?>
  

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
