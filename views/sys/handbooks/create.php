<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Post */

$this->title = \Yii::t('app', 'conf_header');

$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'sysadmin_header'), 'url' => ['/sysadmin']];

$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'conf_header'), 'url' => ['/sysadmin/conf']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
