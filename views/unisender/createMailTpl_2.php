<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\UnisenderList */

$this->title = 'Создать  шаблон';
$this->params['breadcrumbs'][] = ['label' => 'Шаблон письма', 'url' => ['confirm-letter']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unisender-list-create">

    <h1><?= Html::encode($this->title) ?></h1>

     <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
     ?>
    
    <?= $this->render('_formMailTpl_2', [
        'model' => $model,
        'modelCompare'=>$modelCompare,
        'modelUniFileds'=>$modelUniFileds
    ]) ?>

</div>
