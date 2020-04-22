<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Вход';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Вход в систему</h3>
                    </div>
                    <div class="panel-body">
    

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['class' => 'smart-form client-form'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"input\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'label'],
        ],
    ]); ?>

   <fieldset>
          <section>
           Логин:   
        <?= $form->field($model, 'username') ?>
</section>
          <section>
              Пароль:
        <?= $form->field($model, 'password')->passwordInput() ?>
</section>
          
          <section>
       
              </section>
    </fieldset>
        <footer>
            <div class="pull-left">
                <?= Html::submitButton('Вход', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            
 </footer>
                  
    <?php ActiveForm::end(); ?>

         
                        
                    </div>
                    <div class="panel-footer">
                  
            
                <?= Html::a('Регистрация', ['/users/registration'], ['class'=>'btn btn-success']) ?>
                        &nbsp;&nbsp;&nbsp;       &nbsp;&nbsp;&nbsp;
                 <?= Html::a('Востановление пароля', ['/users/password-recovery'], ['class'=>'btn btn-success']) ?>
            
                    </div>
                </div>
            </div>
        </div>