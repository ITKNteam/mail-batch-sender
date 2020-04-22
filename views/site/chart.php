<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use dosamigos\chartjs\ChartJs;
use app\models\Agency;



$this->title = 'chart';
?>

<pre>
<?php 


echo \app\models\User::getRoleName();

$api_key = 'dd';
$email_list_ids = '1111';

$POST['api_key'] = $api_key;
$POST['email_list_ids'] = $email_list_ids;
$POST['field_names[0]']= 'email';
//array_push($POST, "'field_names[0]'=>'email'"  );
for ($k = 0, $j = 5; $k < $j; $k++) {
    $POST['field_names['.$k.']']= 'email'.$k;
     //array_push($POST, 'email'.$k  );
}

print_r($POST);


print_r(Agency::getList());

?>    

</pre>
<?= dosamigos\chartjs\ChartJs::widget([
    'type' => 'Line',
    'options' => [
        'height' => 400,
        'width' => 400
    ],
    'data' => [
        'labels' => ["January", "February", "March", "April", "May", "June", "July"],
        'clientOptions'=>['legendTemplate' => "<ul><li>s</li></ul>",],
        
        
        'datasets' => [
            [
                'fillColor' => "rgba(220,220,220,0.5)",
                'strokeColor' => "rgba(220,220,220,1)",
                'pointColor' => "rgba(220,220,220,1)",
                'pointStrokeColor' => "#fff",
                'data' => [65, 59, 90, 81, 56, 55, 40],
                

            ],
            [
                'fillColor' => "rgba(151,187,205,0.5)",
                'strokeColor' => "rgba(151,187,205,1)",
                'pointColor' => "rgba(151,187,205,1)",
                'pointStrokeColor' => "#fff",
                'data' => [28, 48, 40, 19, 96, 27, 100]
            ]
        ]
    ]
]);
?>
