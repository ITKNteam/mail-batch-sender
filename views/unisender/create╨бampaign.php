<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\UnisenderList */

$this->title = 'Создать рассылку';
$this->params['breadcrumbs'][] = ['label' => 'Запуск рассылки', 'url' => ['/unisender/campaign']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unisender-list-create">

    <h1><?= Html::encode($this->title) ?></h1>

     <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
     ?>
    
    <?= $this->render('_formCampaign', [
        'model' => $model,
    ]) ?>

</div>
