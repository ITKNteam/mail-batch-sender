<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UnisenderList */


$this->params['breadcrumbs'][] = 'Update';
?>
<div class="unisender-list-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_frmUser', [
        'model' => $model,
    ]) ?>

</div>
