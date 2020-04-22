<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
?>
    <?= $model->step_bage ?>
    <div class="timeline-panel">
        <div class="timeline-heading">
            <h4 class="timeline-title"><?= $model->name?></h4>
              <p><small class="text-muted"><i class="fa fa-clock-o"></i> <?= $model->step_dt?> </small>
            </p>
        </div>
        <div class="timeline-body">
           <?= $model->description?>
        </div>
    </div>