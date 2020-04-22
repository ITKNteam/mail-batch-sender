<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
?>
<div class="hndb_name">
      <?= Html::a(\Yii::t('app', $model->name), ['/sys/handbooks?id='.$model->id], ['class'=>'list-group-item']) ?>
    
</div>