<?php

/* 
 * @created at 2016
 * @author Kirill Shavrak.
 * kirill@shavrak.ru
 */

use kartik\grid\GridView;

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\data\ArrayDataProvider;


$this->title = 'Общая статистика';

$btl_cancel = 0;
$total_emails = 0;
$valid_sends_emails = 0;
$not_valid_emails = 0;

$mail_ru = 0;
$mail_ru_welcome = 0;
$mail_ru_spam = 0;
$mail_ru_not_valid = 0;
$mail_ru_return_validation = 0;
$mail_ru_av = 0;








foreach ($stat as $row){
    if ($row['total'] == 'btl_request_canceled'){
       $btl_cancel =  $btl_cancel + $row['q'];
    }
    
    if ($row['total'] != 'email_not_set'){
       $total_emails =  $total_emails + $row['q'];
    }
    
    if ($row['total'] == 'all_sent'){
       $valid_sends_emails =  $valid_sends_emails + $row['q'];
    }
    
//    if ($row['total'] == 'unprocessed'){
//       $not_valid_emails =  $not_valid_emails + $row['q'];
//    }
    
    if ($row['validate_status']  == 'failed'  
           // && $row['email_availability']=='unreachable'
            ){
       $not_valid_emails =  $not_valid_emails + $row['q'];
    }
//    if ($row['validate_status']  == 'failed'  && 
//        $row['email_availability']=='temp_unreachable'){
//       $not_valid_emails =  $not_valid_emails + $row['q'];
//    }
    
   
    //$mail_ru
    if ($row['validate_status']  == 'failed' ){
       $mail_ru =  $mail_ru + $row['q'];
    }
    if ($row['validate_status']  == 'passed'  && $row['email_availability']=='spam_folder'){
       $mail_ru =  $mail_ru + $row['q'];
    }
   
    if ($row['validate_status']  == 'passed'  && $row['email_availability']=='available'){
       $mail_ru =  $mail_ru + $row['q'];
    } 
    if ($row['validate_status']  == 'unknown' ){
       $mail_ru =  $mail_ru + $row['q'];
    }
    
    //end mail_ru
    
    if ($row['validate_status']  == 'passed'  && $row['email_availability']=='available'){
       $mail_ru_welcome =  $mail_ru_welcome + $row['q'];
    }
    
    
    //mail ru spam
    


    
    if ($row['validate_status']  == 'failed'  && 
        $row['email_availability']=='spam_rejected'){
       $mail_ru_spam =  $mail_ru_spam + $row['q'];
    }
    if ($row['validate_status']  == 'failed'  && 
        $row['email_availability']=='spam_folder'){
       $mail_ru_spam =  $mail_ru_spam + $row['q'];
    }
    if ($row['validate_status']  == 'failed'  && 
        $row['email_availability']=='mailbox_full'){
       $mail_ru_spam =  $mail_ru_spam + $row['q'];
    }

//    if ($row['validate_status']  == 'passed'  && $row['email_availability']=='unreachable'){
//       $mail_ru =  $mail_ru + $row['q'];
//    }
//    if ($row['validate_status']  == 'passed'  && $row['email_availability']=='temp_unreachable'){
//       $mail_ru =  $mail_ru + $row['q'];
//    }
//    if ($row['validate_status']  == 'passed'  && $row['email_availability']=='spam_rejected'){
//       $mail_ru =  $mail_ru + $row['q'];
//    }
//    if ($row['validate_status']  == 'passed'  && $row['email_availability']=='mailbox_full'){
//       $mail_ru =  $mail_ru + $row['q'];
//    }
    


    
   
    
    if ($row['validate_status']  == 'passed'  && 
        $row['email_availability']=='unreachable'){
       $mail_ru_av =  $mail_ru_av + $row['q'];
    }
    if ($row['validate_status']  == 'passed'  && 
        $row['email_availability']=='temp_unreachable'){
       $mail_ru_av =  $mail_ru_av + $row['q'];
    }
    if ($row['validate_status']  == 'passed'  && 
        $row['email_availability']=='spam_rejected'){
       $mail_ru_av =  $mail_ru_av + $row['q'];
    }
    if ($row['validate_status']  == 'passed'  && 
        $row['email_availability']=='spam_folder'){
       $mail_ru_av =  $mail_ru_av + $row['q'];
    }
    if ($row['validate_status']  == 'passed'  && 
        $row['email_availability']=='mailbox_full'){
       $mail_ru_av =  $mail_ru_av + $row['q'];
    }
    
    

    
    
    if ($row['validate_status']  == 'failed'  && 
    is_null( $row['email_availability'])){
       $mail_ru_spam =  $mail_ru_spam + $row['q'];
    }
    
    
    
    
    
    //mail ru spam
    
    
    //mail ru no valid
    if ($row['validate_status']  == 'failed'  && 
        $row['email_availability']=='unreachable'){
       $mail_ru_not_valid =  $mail_ru_not_valid + $row['q'];
    }
    if ($row['validate_status']  == 'failed'  && 
        $row['email_availability']=='temp_unreachable'){
       $mail_ru_not_valid =  $mail_ru_not_valid + $row['q'];
    }
    
    //end mail ru no valid
    
    
    if ($row['validate_status']  == 'unknown'  ){
       $mail_ru_return_validation =  $mail_ru_return_validation + $row['q'];
    }
}



?>


<div class="content-inner">

    
    
    
    

    
    <br>
    <br>
    <ul class="nav nav-tabs">
    <li  <?php if ($campaign_id== 35) {echo 'class="active"';}  ?> ><a href="/users/simple-stat?campaign_id=35">P&S </a></li>
    <li  <?php if ($campaign_id== 57) {echo 'class="active"';}  ?>><a href="/users/simple-stat?campaign_id=57">Балканская звезда</a></li>

  </ul>
    <br>
<h2><?= $this->title?></h2>
<?php



$valid_sends_emails = $valid_sends_emails + $mail_ru_av +$mail_ru_welcome;

  


  $data1 = [
    ['param_name' => 'Отменено по запросу BTL', 'param_value' => $btl_cancel],
    ['param_name' => 'Общее количество email', 'param_value' => $total_emails],
    ['param_name' => 'Валидные email', 'param_value' => $valid_sends_emails],
    ['param_name' => 'Невалидные email', 'param_value' => $not_valid_emails],
    ['param_name' => 'На повторной валидации', 'param_value' => $mail_ru_return_validation],
    
];

$provider1 = new ArrayDataProvider([
    'allModels' => $data1,
    'pagination' => [
        'pageSize' => 10,
    ],
    'sort' => [
        'attributes' => ['param_name', 'param_value'],
    ],
]);

  echo GridView::widget([
        'dataProvider' => $provider1,
        //'filterModel' => $searchModel,
        'columns' => [
            
               [
                 'label'=>'Параметр',
                 'format' => 'raw',
                  'value'=> 'param_name',
                 
             ],  
               [
                 'label'=>'Значение',
                 'format' => 'raw',
                  'value'=> 'param_value',
                 
             ],  
        ],
    ]); ?>






</div>