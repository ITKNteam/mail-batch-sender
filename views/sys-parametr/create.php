<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SysParametr */

$this->title = 'Create Sys Parametr';
$this->params['breadcrumbs'][] = ['label' => 'Sys Parametrs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-parametr-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
