<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Agency;
use app\models\UnisenderMailTpl;
use app\models\UnisenderConfirmEmailTxt;
use app\models\Handbook;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\UnisenderList */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="unisender-list-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'agency_id')->dropDownList(Agency::getList(), ['rows' => 3]) ?>
    <?= $form->field($model, 'confirm_email')->dropDownList(UnisenderConfirmEmailTxt::getList(), ['rows' => 3]) ?>

    <?= $form->field($model, 'list_title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'campaign_id')->dropDownList(UnisenderMailTpl::getCampaignNames(), ['rows' => 3]) ?>
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


    <?php //echo $form->field($model, 'before_subscribe_url')->textInput(['rows' => 3]) ?>

    <?php //echo $form->field($model, 'after_subscribe_url')->textInput(['rows' => 3]) ?>

    

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
