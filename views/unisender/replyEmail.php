<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\UnisenderList */

$this->title = 'Переслать email';
$this->params['breadcrumbs'][] = ['label' => 'Email для рассылки', 'url' => ['email']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="unisender-list-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="info">
    Не забудьте выбрать новый список рассылки, для этого письма.
    
</p>
     <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
     ?>
    
    <?= $this->render('_formEmail', [
        'model' => $model,
    ]) ?>

</div>
