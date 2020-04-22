<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\UnisenderMailTpl;
use app\models\Agency;
use app\models\AgencySettings;
use app\models\UnisenderList;
use app\models\UnisenderFields;

/* @var $this yii\web\View */
/* @var $model app\models\UnisenderList */
/* @var $form yii\widgets\ActiveForm */
?>



<div class="row">
    
    <div class="col-lg-6 col-md-6">
        
    
<div class="unisender-list-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'agency_id')->dropDownList(Agency::getList(), ['rows' => 3]) ?>
    

    <?php // echo $form->field($model, 'mail_subject')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'mail_body')->textArea(['maxlength' => true]) ?>
    <?= $form->field($model, 'group_id')->dropDownList(UnisenderMailTpl::getMailGroupNames(), ['rows' => 3]) ?>
     <?= $form->field($model, 'campaign_id')->dropDownList(UnisenderMailTpl::getCampaignNames(), ['rows' => 3]) ?>
 
<hr>
    <?= $form->field($modelUniFileds, 'u_field_id')->dropDownList(UnisenderFields::getList(), ['rows' => 3]) ?>
    
<hr>
     <?= $form->field($modelCompare, 'param_id')->dropDownList(AgencySettings::getParamName(), ['rows' => 3]) ?>
     <?= $form->field($modelCompare, 'value')->textInput(['maxlength' => true]) ?>

    

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
        
        </div>
    <div class="col-lg-6 col-md-6">
        <h3>Предпросмотр шаблона</h3>
        
        <?= $model->mail_body; ?>
        
    </div>

    </div>
