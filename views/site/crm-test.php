<?php

/* @var $this yii\web\View */


use app\components\ShugarCrmConnector;


use yii\helpers\ArrayHelper;

$this->title = 'CRM test';
?>

<?php
 $address ='vitas12@mail.ru';
    $alias = 'vitas12@mail.ru';
    $birthDate = '1980-01-03';
    $userKey = '';
    
    $externalId = time().rand(0, 100);; 
    $httpCookie= 'test';
    $httpReferrer= 'test';
    $invitationLinkKey = 'test';
    $familyName= 'Test2';
    $givenName= 'SSS2';
    $patronymic= 'Ivanovich2';
    $utmCampaign= 'test';
    $utmContent= 'test';
    $utmMedium= 'test';
    $utmSource= 'test';
    $utmTerm= 'test';
    $password= 'hgkhjgjh';
    $gender = true;
    $fullNumber = '89261234567';
    $notificationAllow = 1;
    $smoker = 1;
    
    
     $userKey = "35917391-ba80-2899-338c-5641c75b8188";
     
      $hostess_id = '1'; 
      $activity_dt = '2015-11-10';
      $activity_loc = 'Москва';
      $test_res = '65'; 
      $test_id = '1';
      $activity_type = '1';
      $activity_id = '2';
      $advanced_data = json_encode(['data1'=>'aaaa', 'data2'=>'bbbb', 'data3'=>'cccc']);
     
    
      $start_dt =  new DateTime('now');
    $connector = new ShugarCrmConnector;
//    $response = $connector->RegisterBasic(  $userKey, $birthDate, $address, $alias, $externalId, $httpCookie, $httpReferrer, $invitationLinkKey,
//                                   $familyName, $givenName, $patronymic, $utmCampaign, $utmContent, $utmMedium, $utmSource, $utmTerm, $password);
 //   $response = $connector->CheckEmailAvailability( $address, $alias );
    $response = $connector->UpdateProfile($userKey, $birthDate, $address, $alias, $gender, $fullNumber, $familyName, $givenName, $patronymic,
                                             $notificationAllow,  $smoker, $password);
 
   //$response = $connector->ClubProfileData($userKey );
//   $response = $connector->setAgencySurvey($userKey, 
//           $hostess_id, $activity_dt, $activity_loc,
//                                       $test_res, $test_id, $activity_type, $activity_id,
//                                       $advanced_data
//           
//           );
//    
    
    ?>

<pre>
    <?php
     print_r($response);
 
    ?>
</pre>
