<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\UnisenderList */

$this->title = 'Новое поле';
$this->params['breadcrumbs'][] = ['label' => 'Шаблоны содержания писем', 'url' => ['mail-tpl']];
$this->params['breadcrumbs'][] = ['label' => ' Поля шаблона', 'url' => ['mail-fields?mail_tpl_id='.$mail_tpl_id]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="unisender-list-create">

    <h1><?= Html::encode($this->title) ?></h1>

     <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
     ?>
    
    <?= $this->render('_formFieldTpl', [
        'model' => $model,
    ]) ?>

</div>
