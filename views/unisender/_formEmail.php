<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Agency;
use app\models\UnisenderList;

/* @var $this yii\web\View */
/* @var $model app\models\UnisenderList */
/* @var $form yii\widgets\ActiveForm */
?>



<div class="row">
    
    <div class="col-lg-6 col-md-6">
        
    
<div class="unisender-list-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'agency_id')->dropDownList(Agency::getList(), ['rows' => 3]) ?>
    <?= $form->field($model, 'list_id')->dropDownList(UnisenderList::getListName(), ['rows' => 3]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'sender_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'sender_email')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'body')->textArea(['maxlength' => true]) ?>


    

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
        
        </div>
    <div class="col-lg-6 col-md-6">
        <h3>Предпросмотр письма</h3>
        
        <?= $model->body; ?>
        
    </div>

    </div>
