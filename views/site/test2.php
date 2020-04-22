<?php

/* @var $this yii\web\View */


use app\models\AgencySettings;
use app\models\UnisenderMailTplFileds;
use app\models\BatchData;
use app\models\AgencyCsvBatch;
use app\models\UnisenderList;
use app\models\CronTask;
use app\models\CompareDataEmail;
use app\models\UnisenderSubscribe;
use app\models\CsvBatches;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;

use yii\data\SqlDataProvider;

use app\components\ShugarCrmConnector;
?>

<pre>
<?php 

$user_id = '43a06214-8fd7-0cef-b2ca-573efd0e658a';



 $users = \app\models\CrmUsers::find()->where(['user_key'=>$user_id])->all();
   $connector = new ShugarCrmConnector;
 
 foreach ($users as $row){
               $gender  = null;
                if ($row->gender=='1'){
                   $gender  = false;
                } if ($row->gender=='2'){
                   $gender  = true;
                }
                
               
                
                
                $externalId = time().rand(0, 100);
                $userKey = $row->user_key;
                $address = $row->email;
                $alias = $row->email;
                $fullNumber = '9265678999';
                
                $birthDate = $row->age;
                
                
                $password = app\models\User::generatePassword();
                
                $familyName = $row->f_name;
                $givenName = $row->l_name;
                $patronymic = $row->p_name;
                $notificationAllow = 1;
                $smoker = 1;
                
                
                $response = $connector->UpdateProfile($userKey, $birthDate, $address, 
                                                      $alias, $gender, $fullNumber, $familyName, $givenName, $patronymic,
                                                      $notificationAllow,  $smoker, $password);
                
                
                
                
                
                print_r($response);
 }                

 echo '<br>';
 
 $user_id = '3f7c7cf1-d35f-f6ef-e8be-573efdfc1500';
 
 $response = $connector->ClubProfileData($user_id);
       print_r($response);

   ?>

</pre>
                             

