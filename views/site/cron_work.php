<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'last work at '.date('H:i:s d.m.Y');
$this->params['breadcrumbs'][] = $this->title;
?>
<meta http-equiv="refresh" content="60" />
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
       Work every 60 sec.
    </p>

    
</div>
