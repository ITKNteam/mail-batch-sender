<?php

/* 
 * @created at 2016
 * @author Kirill Shavrak.
 * kirill@shavrak.ru
 */


use kartik\grid\GridView;

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;


$e =  app\models\UnisenderCampaign::find()
              ->leftJoin('unisender_email', '`unisender_campaign`.`message_id` = `unisender_email`.`id`')
              ->where(['unisender_email.list_id'=>$model->id])->one();

$campaign_id = @$e->campaign_id;
$campaign_name = @$e->name;
$agency_id = @$e->agency_id;

if ($campaign_id){
$api_key = app\models\AgencySettings::getApiKey($agency_id);
$res = \Yii::$app->unisender->getCampaignAggregateStats($campaign_id, $api_key);

?>


    <div class="col-lg-12">
         <div class="panel panel-primary">
             <div class="panel-heading">
                 Отчёт о статусах доставки сообщений  рассылки <?=$campaign_name?>
             </div>
             <div class="panel-body">
                            <div class="row">
                   
            <div class="col-xs-6 text-right">
            <?=  \dosamigos\chartjs\ChartJs::widget([
                'type' => 'Doughnut',
                'id'=>'ddd_'.$campaign_id,
                
                'options' => [
                    'defaults'=> 'defaultConfig',
                   
                    'height' => 300,
                    'width' => 250
                ],
                'data' => [
                    [
                    'value'=> @$res['UnisenderAnswer']->result->data->ok_delivered,
                    'color'=>"#46BFB0",
                    'highlight'=> "#5AD3D1",
                    'label'=> "Доставленно : ".@$res['UnisenderAnswer']->result->data->ok_delivered
                ],
                    [
                    'value'=> @$res['UnisenderAnswer']->result->data->ok_read,
                    'color'=>"#F6B1BD",
                    'highlight'=> "#FAD3D1",
                    'label'=> "Прочитано : ". @$res['UnisenderAnswer']->result->data->ok_read
                ],
                    [
                    'value'=> @$res['UnisenderAnswer']->result->data->ok_unsubscribed,
                    'color'=>"#460FBD",
                    'highlight'=> "#460FB0",
                    'label'=> "Отписались : ". @$res['UnisenderAnswer']->result->data->ok_unsubscribed
                ],
                    [
                    'value'=> @$res['UnisenderAnswer']->result->data->err_user_unknown,
                    'color'=>"#F7464A",
                    'highlight'=> "#F74610",
                    'label'=> "Адрес не существует : ". @$res['UnisenderAnswer']->result->data->err_user_unknown
                ],
                    [
                    'value'=> @$res['UnisenderAnswer']->result->data->err_user_inactive,
                    'color'=>"#F7004A",
                    'highlight'=> "#5A00D1",
                    'label'=> "Адрес когда-то существовал, но сейчас отключен : ". @$res['UnisenderAnswer']->result->data->err_user_inactive
                ],
                    [
                    'value'=> @$res['UnisenderAnswer']->result->data->err_delivery_failed,
                    'color'=>"#ff22AA",
                    'highlight'=> "#5A22ff",
                    'label'=> "Доставка не удалась по иным причинам : ". @$res['UnisenderAnswer']->result->data->err_delivery_failed
                ],
                    [
                    'value'=> @$res['UnisenderAnswer']->result->data->err_will_retry,
                    'color'=>"#BBc08D",
                    'highlight'=> "#5AD3D1",
                       'label'=> "Попытки продолжаются : ". @$res['UnisenderAnswer']->result->data->err_will_retry
                      
                ],
                  
                  



                ]
            ]);
            ?>
                 </div>
              <div class="col-xs-6">
                 <div id="js-legend_ddd_<?= $campaign_id?>" class="chart-legend"></div>
             </div>
             </div>
         </div>
        
    </div>
    </div>

<?php } else { ?>

<div class="col-lg-6">
         <div class="panel panel-primary">
             <div class="panel-heading">
                 Отчёт о статусах доставки сообщений  рассылки <?=$campaign_name?>
             </div>
             <div class="panel-body">
                            <div class="row">
                   
            <div class="col-xs-6 text-right">
                 Пока не получен
            </div>
              <div class="col-xs-6">
                 <div id="js-legend_ddd_<?= $campaign_id?>" class="chart-legend"></div>
             </div>
             </div>
         </div>
        
    </div>
    </div>
<?php }  ?>