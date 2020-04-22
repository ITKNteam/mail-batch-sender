<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CompareDataEmail */

$this->title = 'Create Compare Data Email';
$this->params['breadcrumbs'][] = ['label' => 'Compare Data Emails', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="compare-data-email-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
