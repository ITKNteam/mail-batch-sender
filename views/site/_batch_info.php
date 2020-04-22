<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

$sql_city = "SELECT
  
                case 
               when e.value = '' then 'empty'
               when e.value != '' then 'have'
               end  as email,
               c.value as city,
               count(c.id)

               FROM westbtl.batch_data e,  westbtl.batch_data c

              where e.agency_parametr_id in (44)
              and e.batch_id = c.batch_id
              and e.string_order = c.string_order
              and c.agency_parametr_id in (52)

              and c.batch_id = 125

               group by email, city";
$sql_total = "SELECT
  
                case 
               when e.value = '' then 'empty'
               when e.value != '' then 'have'
               end  as email,
               count(c.id)

               FROM westbtl.batch_data e

              where e.agency_parametr_id in (44)
              and e.batch_id = c.batch_id
              and e.batch_id = 125

              group by email";




?>
<div class="col-lg-3">
<div class="panel panel-default">
        <div class="panel-heading">
            <?= $model->file_name; ?>
            
        </div>
        <div class="panel-body">
             
            <p>
               
            <?=  $model->string_count; ?>
            </p>
        </div>
        <div class="panel-footer">
              <?= Html::a(\Yii::t('app', $model->name), ['view?id='.$model->id]) ?>
        </div>
    </div>
    </div>
    
      
      
    
    
