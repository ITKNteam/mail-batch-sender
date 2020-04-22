<?php

/* @var $this yii\web\View */


use app\models\AgencySettings;
use app\models\BatchData;
use app\models\AceptBatch;
use app\models\User;
use app\models\AgencyCsvBatch;
use app\models\UserAvailableCityes;
use app\models\UnisenderListAvailableCityes;
use app\models\CsvBatches;
use app\models\CronTask;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;

use app\components\ShugarCrmConnector;

?>


<?php
$batch_id = 161;
$limit = 200;
//
$i = 0;
//
// $count = CsvBatches::find()->where(['batch_id'=>$batch_id])->count();
// 
//   $ins =   \app\models\CrmUsers::insertUsers2($batch_id);
// 
// //$count = 560;
//        $steps = ceil($count/$limit);
//
//
//        echo '$ins ' .$ins;
//        echo '$steps ' .$steps;
//        echo '<br>$count ' .$count;
//        
//         for($i = 1; $i <= $steps; ++$i ){
//        
//            
//            
//                $offset =  0;
//               
//            
//            
//            if ($i > 1){
//                $offset = ($i-1)* $limit;
//            }
//            
//            $data = CsvBatches::find()->where(['batch_id'=>$batch_id, 
//                                                     
//                                            ])
//                          ->limit($limit)
//                            ->offset($offset)   
//      
//                         ->all();
//            
//
//                echo "<br>step : $i , offset $offset limit $limit  count".count($data);
//        
//         
//        
//        
//        
//         }
         
//
  $users = app\models\CrmUsers::find()->where(['batch_id'=>$batch_id])
                ->limit(200)
                 //->offset(1001)
                 ->all();
        

            foreach ($users as $row){
                
               
                
               
                 $gender  = null;
                if ($row->gender=='1'){
                   $gender  = false;
                } if ($row->gender=='2'){
                   $gender  = true;
                }
            
                $familyName = $row->f_name;
                $givenName = $row->l_name;
                $birthDate = $row->age;
                
                $notificationAllow = 1;
                $smoker = 1;
                $externalId = time().rand(0, 100);
                $patronymic = $row->p_name;
                $password = User::generatePassword();
                $fullNumber = $row->phone;
                $address = $row->email;
                $city = $row->activity_loc;
                $valid_email = $row->valid_email;
                
                 $p_array[] = [
                    'familyName'=>$familyName,
                    'givenName'=>$givenName,
                    'birthDate'=>$birthDate,
                    'gender'=>$gender,
                    'notificationAllow'=>$notificationAllow,
                    'smoker'=>$smoker,
                    'externalId'=>$externalId,
                    'patronymic'=>$patronymic,
                    'password'=>$password,
                    'fullNumber'=>$fullNumber,
                    'address' =>$address,
                           'city'=> $city,
                    'valid_email'=>$valid_email 
                  
              ];
                 
                 
                 ++$i;
                 if($i == $limit){
                      $start_dt =  new \DateTime('now');
                        $s_array = json_encode($p_array, JSON_PRETTY_PRINT);
//                        $connector = new ShugarCrmConnector;
//                        $response = $connector->RegisterBasicMassive($s_array);
//                        $finish_dt = new \DateTime('now');
//                        
//                         $seconds_diff = $finish_dt->getTimestamp() - $start_dt->getTimestamp();
//                         $milliseconds_diff = $seconds_diff * 1000;
//                             print '<div class="span2">Rows send :'.$i.'</div>';
//                             print '<div class="span10">';
//                             echo   '| Execute time in ms:'.$milliseconds_diff;
//                         
//                             print '<pre>';
//                             
//                             
//                             print_r($response);
//                             print '</pre>';
//                             print '</div>';
//                             unset($connector);
//                             unset($p_array);
                        $i =0 ;
                 }
                 }
                
                
                
//                $fullNumber = $row->phone;
//                $birthDate = $row->age;
//                $password = User::generatePassword();
//                $httpCookie = 1;
//                $httpReferrer = 1;
//                $invitationLinkKey = 1;
//                $familyName = $row->f_name;
//                $givenName = $row->l_name;
//                $patronymic = $row->p_name;
//                $utmCampaign = 1;
//                $utmContent = 1;
//                $utmMedium = 1;
//                $utmSource = 1;
//                $utmTerm = 1;
                
               
                
//                $response = $connector->RegisterBasic ( $birthDate, $address, $alias, $externalId, $httpCookie, $httpReferrer, $invitationLinkKey,
//                                   $familyName, $givenName, $patronymic, $utmCampaign, $utmContent, $utmMedium,
//                                   $utmSource, $utmTerm, $password, 
//                                   $app);
//                
//                
//                
//                
//                $rec_status = 2; 
//                if ($response['isSuccessfully'] == 0){
//                    $rec_status = -2; 
//                } 
//                
//                $log_data[] = [
//                    $row->id,
//                    'RegisterBasic',
//                    $response['failedCauseOf'],    
//                    $response['isSuccessfully'],    
//                    $response['details'],    
//                    $response['entity'],
//                    date("Y-m-d H:i:s"),
//                    $externalId
//                 ];
//                
//                
//                $userKey = $response['entity'];
//                
//                $response_log = $connector->LogEvents('User - '.$row->email, 'RegisterBasic', date("Y-m-d"), 0, $externalId, 0, 0, $userKey, $app);
//                
//                 $log_data[] = [
//                    $row->id,
//                    'LogEvents',
//                    @$response_log['failedCauseOf'],    
//                    @$response_log['isSuccessfully'],    
//                    @$response_log['details'],    
//                    @$response_log['entity'],
//                    date("Y-m-d H:i:s"), 
//                    $externalId 
//                 ];
//                
//                 static::updateAll(['rec_status' => $rec_status, 'last_externalId'=>$externalId, 'user_key'=>$userKey], 'id ='.$row->id);
     //       }


?>



<pre>
    <?php
   
    print_r($s_array);
    
//    
//    // print_r(json_encode($p_array, JSON_PRETTY_PRINT)); 
//     
//     
//     $p_array = json_encode($p_array, JSON_PRETTY_PRINT);
//        $connector = new ShugarCrmConnector;
//        $response = $connector->RegisterBasicMassive($p_array);
//     
//        print_r($response); 
     
     ?>

                
</pre>
 
