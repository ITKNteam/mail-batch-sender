<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\UnisenderList */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Unisender Fileds', 'url' => ['fields']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unisender-list-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
       
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'agency_id',
            'new_field_name',
           
        ],
    ]) ?>

</div>
