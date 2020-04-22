<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Agency;
use app\models\AgencySettings;
use app\models\UnisenderMailTpl;

/* @var $this yii\web\View */
/* @var $model app\models\CompareDataEmail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="compare-data-email-form">

    <?php $form = ActiveForm::begin(); ?>

      <?= $form->field($model, 'agency_id')->dropDownList(Agency::getList(), ['rows' => 3]) ?>
      <?= $form->field($model, 'param_id')->dropDownList(AgencySettings::getParamName(), ['rows' => 3]) ?>

    
    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

    
    <?= $form->field($model, 'mail_tpl_id')->dropDownList(UnisenderMailTpl::getMailGroupNames(), ['rows' => 3]) ?>
    
    

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
