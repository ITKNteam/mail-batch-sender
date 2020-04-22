<?php

namespace app\models;

use yii\helpers\ArrayHelper;
use app\components\ShugarCrmConnector;

use Yii;

/**
 * This is the model class for table "crm_users".
 *
 * @property integer $id
 * @property integer $batch_id
 * @property integer $row_id
 * @property string $email
 * @property string $phone
 * @property string $f_name
 * @property string $l_name
 * @property string $p_name
 * @property string $age
 * @property string $gender
 * @property string $priority_mark1
 * @property string $priority_mark2
 * @property string $hostess_id
 * @property string $activity_dt
 * @property string $activity_loc
 * @property string $test_res
 * @property integer $test_id
 * @property integer $activity_type
 * @property integer $activity_id
 * @property string $advanced_data
 * @property string $user_key
 * @property integer $rec_statis
 * @property string $last_dt
 *
 * @property CrmLog[] $crmLogs
 * @property AgencyCsvBatch $batch
 */
class CrmUsers extends \yii\db\ActiveRecord
{
    
    public static function  getLimit(){
        return 10;
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'crm_users';
    }

    /**
     * @inheritdoc
     */
    
    
    
//    ALTER TABLE `westbtl`.`crm_users` 
//    ADD COLUMN `valid_email` INT NULL COMMENT '1 - valid\n0- not valid' AFTER `app`;


    public function rules()
    {
        return [
            [['batch_id', 'row_id', 'test_id', 'activity_type', 'activity_id', 'rec_status', 'unisender_letter_id', 'valid_email'], 'integer'],
            [['last_dt', 'unisender_last_update'], 'safe'],
            [['email', 'advanced_data', 'user_key'], 'string', 'max' => 450],
            [['last_externalId',], 'string', 'max' => 245],
            [['hostess_name',], 'string', 'max' => 100],
            [['phone', 'f_name', 'l_name', 'p_name', 
                'age', 'gender', 'priority_mark1', 'priority_mark2', 
                'hostess_id', 'activity_dt', 'activity_loc', 
                'test_res', 'unisender_send_result', 'app'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'batch_id' => 'Batch ID',
            'row_id' => 'Row ID',
            'email' => 'Email',
            'phone' => 'Phone',
            'f_name' => 'F Name',
            'l_name' => 'L Name',
            'p_name' => 'P Name',
            'hostess_name'=>'hostess_name',
            'age' => 'Age',
            'gender' => 'Gender',
            'priority_mark1' => 'Priority Mark1',
            'priority_mark2' => 'Priority Mark2',
            'hostess_id' => 'Hostess ID',
            'activity_dt' => 'Activity Dt',
            'activity_loc' => 'Activity Loc',
            'test_res' => 'Test Res',
            'test_id' => 'Test ID',
            'activity_type' => 'Activity Type',
            'activity_id' => 'Activity ID',
            'advanced_data' => 'Advanced Data',
            'user_key' => 'User Key',
            'rec_status' => 'Rec Statis',
            'last_dt' => 'Last Dt',
            'unisender_send_result'=>'Результат рассылки',
            'valid_email'=>'valid email'    
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCrmLogs()
    {
        return $this->hasMany(CrmLog::className(), ['crm_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBatch()
    {
        return $this->hasOne(AgencyCsvBatch::className(), ['id' => 'batch_id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\QueryModels\CrmUsersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\QueryModels\CrmUsersQuery(get_called_class());
    }
    
    
    
    public static function getListName(){
        $ret = [];
        $model = static::find()->select('id, list_title')->all();
        foreach ($model as $row)
            $ret[$row['id']]= $row['list_title'];
        
        return $ret;
    }
    
    public static function statusList(){
        $ret = [0=>'Не обработано',
                1=>'CheckEmailAvailability',
                -1=>'CheckEmailAvailability Error',
                2=>'RegisterBasic',
                -2=>'RegisterBasic Error',
                3=>'UpdateProfile',
                -3=>'UpdateProfile Error',
                4=>'FillSmokingHabbits',
                -4=>'FillSmokingHabbits Error',
                8=>'Массовая регистрация',
                -8=>'Массовая регистрация Error',
           //     5=>'setAgencySurvey',
                ];
         return $ret;
         
    }
    public static function statusListName($id){
       $ret = self::statusList();
         return $ret[$id];
         
    }
    
    
    public static function insertUsers($batch_id, $step =1, $limit = 1000){
        
         
       $cols_count = 16;
       if (!$limit)
          $limit = static::getLimit();
       $offset = 0;
       $k = $step-1;
       
       if ($k != 0)
         $offset = $k* $limit;  
       
       
       
      $agency_batch = AgencyCsvBatch::find()->where(['id'=>$batch_id])->one();
     
      $count =  $agency_batch->string_count;
      $agency_id   =  $agency_batch->agency_id;
      $steps =   ceil ($count/$limit);  
        
        
            $batch = BatchData::find()->select('s.name as param_name, s.sys_name, agency_parametr_id, string_order, value, batch_date')
                                     ->where(['batch_id'=>$batch_id])
                                     ->andWhere(['agency_id'=>$agency_id])
                                     ->leftJoin('sys_parametr s', 'agency_parametr_id = s.id')
                                     ->leftJoin('agency_csv_batch acb', 'batch_id = acb.id')
                                      ->limit($limit*$cols_count)
                                      ->offset($offset*$cols_count)    
                                     ->all();
           $data =   ArrayHelper::map($batch,    'agency_parametr_id', 'value',  'string_order');

           $i=0;
           
           
           $email_param_id = AgencySettings::getAgencyParamKey($agency_id, 'email');
           $phone_param_id = AgencySettings::getAgencyParamKey($agency_id, 'phone');
           $fname_param_id = AgencySettings::getAgencyParamKey($agency_id, 'f_name');
           $lname_param_id = AgencySettings::getAgencyParamKey($agency_id, 'l_name');
           $pname_param_id = AgencySettings::getAgencyParamKey($agency_id, 'p_name');
           $age_param_id = AgencySettings::getAgencyParamKey($agency_id, 'age');
           $gender_param_id = AgencySettings::getAgencyParamKey($agency_id, 'gender');
           $priority_mark1_param_id = AgencySettings::getAgencyParamKey($agency_id, 'priority_mark1');
           $priority_mark2_param_id = AgencySettings::getAgencyParamKey($agency_id, 'priority_mark2');
           $hostess_id_param_id = AgencySettings::getAgencyParamKey($agency_id, 'hostess_id');
           $activity_dt_param_id = AgencySettings::getAgencyParamKey($agency_id, 'activity_dt');
           $activity_loc_param_id = AgencySettings::getAgencyParamKey($agency_id, 'activity_loc');
           
           $test_res_param_id = AgencySettings::getAgencyParamKey($agency_id, 'test_res');
           $test_id_param_id = AgencySettings::getAgencyParamKey($agency_id, 'test_id');
           $activity_type_param_id = AgencySettings::getAgencyParamKey($agency_id, 'activity_type');
           $activity_id_param_id = AgencySettings::getAgencyParamKey($agency_id, 'activity_id');

           
         
           
           
           foreach ($data as $row){
             ++$i;

               
               $row_id = $i;
               $email = @$row[$email_param_id];
               $phone = @$row[$phone_param_id];
               $f_name = @$row[$fname_param_id];
               $l_name = @$row[$lname_param_id];
               $p_name = @$row[$pname_param_id];
               $age =    @$row[$age_param_id];
               $gender = @$row[$gender_param_id];
               $priority_mark1 = @$row[$priority_mark1_param_id];
               $priority_mark2 = @$row[$priority_mark2_param_id];
               $hostess_id = @$row[$hostess_id_param_id];
               $activity_dt = @$row[$activity_dt_param_id];
               $activity_loc = @$row[$activity_loc_param_id];
               $test_res = @$row[$test_res_param_id];
               $test_id = @$row[$test_id_param_id];
               $activity_type = @$row[$activity_type_param_id];
               $activity_id = @$row[$activity_id_param_id];
               $advanced_data = '';
               $user_key ='';
               $rec_status = 0;
               $last_dt = date("Y-m-d H:i:s");
             
             
               
               
             
             $data_insert[] = [
                            $batch_id,
                            $row_id,
                            $email,
                            $phone,
                            $f_name,
                            $l_name,
                            $p_name,
                            $age,
                            $gender,
                            $priority_mark1,
                            $priority_mark2,
                            $hostess_id,
                            $activity_dt,
                            $activity_loc,
                            $test_res,
                            $test_id,
                            $activity_type,
                            $activity_id,
                            $advanced_data,
                            $user_key,
                            $rec_status,
                            $last_dt
                          
                          ] ;
              
               
              
           }
           
           
           
             
           
           
                $db = Yii::$app->db;
                $sql = $db->queryBuilder->batchInsert(CrmUsers::tableName(), [
                            'batch_id',
                            'row_id',
                            'email',
                            'phone',
                            'f_name',
                            'l_name',
                            'p_name',
                            'age',
                            'gender',
                            'priority_mark1',
                            'priority_mark2',
                            'hostess_id',
                            'activity_dt',
                            'activity_loc',
                            'test_res' ,
                            'test_id',
                            'activity_type',
                            'activity_id',
                            'advanced_data',
                            'user_key',
                            'rec_status',
                            'last_dt'], $data_insert);
              $res =  $db->createCommand($sql )->execute();
        return $res;
    }
    
    
    /* Данная функция отличается от родительской, 
    *  тем что использует таблицу csv_batches для выборки контактов
    * 
    */
    public static function insertUsers2($batch_id,  $limit = 1000, $app= 'PS'){
        
         
      
            
        $count = CsvBatches::find()->where(['batch_id'=>$batch_id])->count();
        
        $steps = ceil($count/$limit);
        
        $data_insert = [];
        for($i = 1; $i <= $steps; ++$i ){
        
      
        $offset =  0;
        if ($i > 1){
            $offset = ($i-1)* $limit;
        }
        
             $data = CsvBatches::find()->where(['batch_id'=>$batch_id, 
                                                     
                                            ])
                          ->limit($limit)
                            ->offset($offset)   
      
                         ->all();
           
           foreach ($data as $csv_rows){
               
               
                $row_id = 0;
               
                $email =  $csv_rows->email;

                                      $phone=  $csv_rows->phone;
                                      $f_name=  $csv_rows->f_name;
                                      $l_name=  $csv_rows->l_name;
                                      $p_name=  $csv_rows->p_name;
                                      $age=  $csv_rows->age;
                                      $gender=  $csv_rows->gender;
                                      $priority_mark1=  $csv_rows->priority_mark1;
                                      $priority_mark2=  $csv_rows->priority_mark2;
                                      $hostess_id=  $csv_rows->hostess_id;
                                      $activity_dt=  $csv_rows->activity_dt;
                                      $activity_loc=  $csv_rows->activity_loc;
                                      $test_res=  $csv_rows->test_res;
                                      $test_id=  $csv_rows->test_id;
                                      $activity_type=  $csv_rows->activity_type;
                                      $activity_id=  $csv_rows->activity_id;
                                      $advanced_data = $csv_rows->advanced_data;
                                      $valid_email = 0;
                                      
                                      if($csv_rows->validate_status =='passed')
                                        $valid_email = 1;
                                      
                                      
                                      
                                     $user_key ='';
                                     $rec_status = 0;
                                     if ($email=='\N'){
                                        $rec_status = 1;    
                                     }
                                     
                                     $last_dt = date("Y-m-d H:i:s");
                                     
             
             
             
             $data_insert[] = [
                            $batch_id,
                            $row_id,
                            $email,
                            $phone,
                            $f_name,
                            $l_name,
                            $p_name,
                            $age,
                            $gender,
                            $priority_mark1,
                            $priority_mark2,
                            $hostess_id,
                            $activity_dt,
                            $activity_loc,
                            $test_res,
                            $test_id,
                            $activity_type,
                            $activity_id,
                            $advanced_data,
                            $user_key,
                            $rec_status,
                            $last_dt,
                            $app,
                            $valid_email
                          
                          ] ;
              
 
              
           }
           
           if (isset($data_insert)){
                $db = Yii::$app->db;
                $sql = $db->queryBuilder->batchInsert(CrmUsers::tableName(), [
                            'batch_id',
                            'row_id',
                            'email',
                            'phone',
                            'f_name',
                            'l_name',
                            'p_name',
                            'age',
                            'gender',
                            'priority_mark1',
                            'priority_mark2',
                            'hostess_id',
                            'activity_dt',
                            'activity_loc',
                            'test_res' ,
                            'test_id',
                            'activity_type',
                            'activity_id',
                            'advanced_data',
                            'user_key',
                            'rec_status',
                            'last_dt',
                            'app','valid_email'], $data_insert);
              $res =  $db->createCommand($sql )->execute();
            unset($data_insert);
                unset($data);   
           }
           }
            unset($data_insert);
                unset($data);
            
                 
        return $res;
    }
    
    
    public static function checkEmailAvability($batch_id, $step=1){
          
       $limit = static::getLimit();
       $offset = 0;
       $k = $step-1;
//       
//       if ($k != 0)
//         $offset = $k* $limit;  
        
        $users = static::find()->where(['batch_id'=>$batch_id, 'rec_status'=>0])
                 ->limit($limit)
                 //->offset($offset)
                 ->all();
        
        if ($users){
        
            $connector = new ShugarCrmConnector;
            $rec_status = -1; 


            foreach ($users as $row){
                $externalId = time().rand(0, 100);
                $response = $connector->CheckEmailAvailability( $row->email, $row->email, $row->app );

                $rec_status = 1; 
                if ($response['isSuccessfully'] == 0){
                    $rec_status = -1; 
                } 

                $log_data[] = [
                    $row->id,
                    'CheckEmailAvailability',
                    $response['failedCauseOf'],    
                    $response['isSuccessfully'],    
                    $response['details'],    
                    $response['entity'],
                    date("Y-m-d H:i:s"),
                    $externalId
                 ];
                 static::updateAll(['rec_status' => $rec_status], 'id ='.$row->id);
            }



            $res =  static::crmLog($log_data);
            return $res;
        
        } else {
            return 0;
        }
        
               
        
    }
    public static function RegisterBasic($batch_id, $step=1){
        
       $limit = static::getLimit();
       $offset = 0;
       $k = $step-1;
//       
//       if ($k != 0)
//         $offset = $k* $limit;  
        
        $users = static::find()->where(['batch_id'=>$batch_id, 'rec_status'=>1])
                 ->limit($limit)
                 //->offset($offset)
                 ->all();
        
        if ($users){
        
            $connector = new ShugarCrmConnector;
            $rec_status = -1; 


            foreach ($users as $row){
                
               
                
                $externalId = time().rand(0, 100);
                
                $address = $row->email;
                $alias = $row->phone;
                $birthDate = $row->age;
                $password = User::generatePassword();
                $httpCookie = 1;
                $httpReferrer = 1;
                $invitationLinkKey = 1;
                $familyName = $row->f_name;
                $givenName = $row->l_name;
                $patronymic = $row->p_name;
                $utmCampaign = 1;
                $utmContent = 1;
                $utmMedium = 1;
                $utmSource = 1;
                $utmTerm = 1;
                
                $app = $row->app;
                
                $response = $connector->RegisterBasic( $birthDate, $address, $alias, $externalId, $httpCookie, $httpReferrer, $invitationLinkKey,
                                   $familyName, $givenName, $patronymic, $utmCampaign, $utmContent, $utmMedium,
                                   $utmSource, $utmTerm, $password, 
                                   $app);
                
                
                
                
                $rec_status = 2; 
                if ($response['isSuccessfully'] == 0){
                    $rec_status = -2; 
                } 
                
                $log_data[] = [
                    $row->id,
                    'RegisterBasic',
                    $response['failedCauseOf'],    
                    $response['isSuccessfully'],    
                    $response['details'],    
                    $response['entity'],
                    date("Y-m-d H:i:s"),
                    $externalId
                 ];
                
                
                $userKey = $response['entity'];
                
                $response_log = $connector->LogEvents('User - '.$row->email, 'RegisterBasic', date("Y-m-d"), 0, $externalId, 0, 0, $userKey, $app);
                
                 $log_data[] = [
                    $row->id,
                    'LogEvents',
                    @$response_log['failedCauseOf'],    
                    @$response_log['isSuccessfully'],    
                    @$response_log['details'],    
                    @$response_log['entity'],
                    date("Y-m-d H:i:s"), 
                    $externalId 
                 ];
                
                 static::updateAll(['rec_status' => $rec_status, 'last_externalId'=>$externalId, 'user_key'=>$userKey], 'id ='.$row->id);
            }

            

            $ret =  static::crmLog($log_data);
            return $ret /2 ;
        
        } else {
            return 0;
        }
        
               
        
    }
    
    
    
     public static function UpdateProfile($batch_id, $step=1){
       $limit = static::getLimit();
       $offset = 0;
       $k = $step-1;
       
       if ($k != 0)
         $offset = $k* $limit;  
        
        $users = static::find()->where(['batch_id'=>$batch_id, 'rec_status'=>2])
                 ->limit($limit)
                 ->offset($offset)->all();
        
        if ($users){
        
            $connector = new ShugarCrmConnector;
            $rec_status = -1; 


            foreach ($users as $row){
                
                 // Пол, значения: Male/Female
             
                
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
                $fullNumber = $row->phone;
                
                $birthDate = $row->age;
                $password = User::generatePassword();
                
                $familyName = $row->f_name;
                $givenName = $row->l_name;
                $patronymic = $row->p_name;
                $notificationAllow = 1;
                $smoker = 1;
                
                
                $response = $connector->UpdateProfile($userKey, $birthDate, $address, 
                                                      $alias, $gender, $fullNumber, $familyName, $givenName, $patronymic,
                                                      $notificationAllow,  $smoker, $password);
                
                
                
                
                $rec_status = 3; 
                if ($response['isSuccessfully'] == 0){
                    $rec_status = -3; 
                } 
                
                $log_data[] = [
                    $row->id,
                    'UpdateProfile',
                    $response['failedCauseOf'],    
                    $response['isSuccessfully'],    
                    $response['details'],    
                    $response['entity'],
                    date("Y-m-d H:i:s"),
                    $externalId
                 ];
                
                
                $userKey = $response['entity'];
                
                $response_log = $connector->LogEvents('User - '.$userKey, 'UpdateProfile', date("Y-m-d"), 0, $externalId, 0, 0, $userKey);
                
                 $log_data[] = [
                    $row->id,
                    'LogEvents',
                    $response_log['failedCauseOf'],    
                    $response_log['isSuccessfully'],    
                    $response_log['details'],    
                    $response_log['entity'],
                    date("Y-m-d H:i:s"), 
                    $externalId 
                 ];
                
                 static::updateAll(['rec_status' => $rec_status, 'last_externalId'=>$externalId], 'id ='.$row->id);
            }



             $ret =  static::crmLog($log_data);
            return $ret /2 ;
        
        } else {
            return 0;
        
        }
         
        
    }
     public static function FillSmokingHabbits($batch_id, $step=1){
       $limit = static::getLimit();
       $offset = 0;
       $k = $step-1;
       
       if ($k != 0)
         $offset = $k* $limit;  
        
        $users = static::find()->where(['batch_id'=>$batch_id, 'rec_status'=>3])
                 ->limit($limit)
                 ->offset($offset)->all();
        
        if ($users){
        
            $connector = new ShugarCrmConnector;
            $rec_status = -1; 


            foreach ($users as $row){
                
                 // Пол, значения: Male/Female
                $gender = 'Male';
                if($row->gender==2){
                    $gender = 'Female';
                } 
                
               
                
                
                $externalId = time().rand(0, 100);
                $userKey = $row->user_key;
                $primaryCigaretteBrand = $row->priority_mark1;
                $secondaryCigaretteBrand = $row->priority_mark2;
                $primaryCigarettePacksAmount =1;
                $primaryCigaretteSmokingExperience  =1;
                $primaryCigaretteType  =1;
                $secondaryCigarettePacksAmount  =1;
                $secondaryCigaretteSmokingExperience  =1;
                $secondaryCigaretteType =1;
                
                
                $response = $connector->FillSmokingHabbits($userKey, $primaryCigaretteBrand, $primaryCigarettePacksAmount, $primaryCigaretteSmokingExperience,
                                       $primaryCigaretteType, $secondaryCigaretteBrand, $secondaryCigarettePacksAmount, $secondaryCigaretteSmokingExperience,
                                       $secondaryCigaretteType);
                
                
                
                
                $rec_status = 4; 
                if ($response['isSuccessfully'] == 0){
                    $rec_status = -4; 
                } 
                
                $log_data[] = [
                    $row->id,
                    'FillSmokingHabbits',
                    $response['failedCauseOf'],    
                    $response['isSuccessfully'],    
                    $response['details'],    
                    $response['entity'],
                    date("Y-m-d H:i:s"),
                    $externalId
                 ];
                
                
                $userKey = $response['entity'];
                
                $response_log = $connector->LogEvents('User - '.$userKey, 'FillSmokingHabbits', date("Y-m-d"), 0, $externalId, 0, 0, $userKey);
                
                 $log_data[] = [
                    $row->id,
                    'LogEvents',
                    $response_log['failedCauseOf'],    
                    $response_log['isSuccessfully'],    
                    $response_log['details'],    
                    $response_log['entity'],
                    date("Y-m-d H:i:s"), 
                    $externalId 
                 ];
                
                 static::updateAll(['rec_status' => $rec_status, 'last_externalId'=>$externalId], 'id ='.$row->id);
            }


            $ret =  static::crmLog($log_data);
            return $ret /2 ;
        
        } else {
            return 0;
        
        }
         
        
    }
     
    
    public static function RegisterBasicMassive($batch_id, $step=1, $limit){
       //$limit = static::getLimit();
       $offset = -1;
       $k = $step-1;
       $batch_limit = 100;
       $i = 0;
        $p_array = [];
       
       if ($k != 0)
         $offset = $k* $limit;  
        
         $users = static::find()->where(['batch_id'=>$batch_id])
                 ->limit($limit)
                 ->offset($offset)
                 ->all();
        
        if ($users){
        
            $connector = new ShugarCrmConnector;
            $rec_status = 8; 


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
                 if($i == $batch_limit){
                        //$start_dt =  new \DateTime('now');
                        $s_array = json_encode($p_array, JSON_PRETTY_PRINT);
                        $connector = new ShugarCrmConnector;
                        $response = $connector->RegisterBasicMassive($s_array);
                       // $finish_dt = new \DateTime('now');
                        
                         
                         $rec_status = 8; 
//                            if ($response['isSuccessfully'] == 0){
//                                $rec_status = -8; 
//                            } 

//                            $log_data[] = [
//                                $row->id,
//                                'FillSmokingHabbits',
//                                $response['failedCauseOf'],    
//                                $response['isSuccessfully'],    
//                                $response['details'],    
//                                $response['entity'],
//                                date("Y-m-d H:i:s"),
//                                $externalId
//                             ];
                        
                        
                        
                        
                        
//                        
//                        $seconds_diff = $finish_dt->getTimestamp() - $start_dt->getTimestamp();
//                         
//                         
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
                             unset($connector);
                             unset($p_array);
                        $i =0 ;
                 }
                 
                unset($familyName);
                unset($givenName);
                unset($birthDate);
                
                unset($notificationAllow);
                unset($smoker);
                unset($externalId);
                unset($patronymic);
                unset($password);
                unset($fullNumber);
                unset($address);
                
               
                
               
                
                 static::updateAll(['rec_status' => $rec_status, 'last_externalId'=>'massive register'], 'id ='.$row->id);
            }
            
            if (isset($p_array)) {
                $s_array = json_encode($p_array, JSON_PRETTY_PRINT);
                $connector = new ShugarCrmConnector;
                $response = $connector->RegisterBasicMassive($s_array);
               // $finish_dt = new \DateTime('now');


                 $rec_status = 8; 
//                    if ($response['isSuccessfully'] == 0){
//                        $rec_status = -8; 
//                    } 
                 unset($connector);
                 unset($p_array);
            }

            //$ret =  static::crmLog($log_data);
            return 1;
        
        } else {
            return 0;
        
        }
         
        
    }
    
    public static function crmLog($log_data){
        $db = Yii::$app->db;
        $sql = $db->queryBuilder->batchInsert(CrmLog::tableName(), [
                    'crm_user_id',
                    'method_name',
                    'failedCauseOf',
                    'isSuccessfully',
                    'details',
                    'entity',
                    'action_dt',
                    'externalId'], $log_data);
        $res =  $db->createCommand($sql )->execute();
        
        return $res;
    }
    
    
    
    
    public static function updateStatus_not_use_old($batch_id){
        
        $batch = AgencyCsvBatch::find()->where(['id'=> $batch_id])->one();
        $list_id = $batch->last_list_id;
        $agency_id = $batch->agency_id;
        $unisender_messages  = UnisenderEmail::find()->where(['list_id'=>$list_id])->one();
        $message_id = @$unisender_messages->message_id;
        $local_message_id = @$unisender_messages->id;
        
        if ($message_id){
            
            $campaign = UnisenderCampaign::find()->where(['message_id'=>$local_message_id])->one(); 
        
            UnisenderDeliveryStatus::saveStatus($campaign->campaign_id, $agency_id);
            
            
            $delivery_statuses = UnisenderDeliveryStatus::find()->where(['letter_id' => $message_id])->all();
            
            foreach ($delivery_statuses as $stat){
                $crm_user = CrmUsers::find()->where([
                    // 'batch_id'=>$batch_id,
                    'email'=>$stat->email])->one();
                $crm_user->unisender_send_result = $stat->send_result;
                $crm_user->unisender_letter_id = $stat->letter_id;
                $crm_user->unisender_last_update = $stat->last_update;
                $crm_user->update();
                
            }
            
//            $SQL = "UPDATE crm_users AS c1, unisender_delivery_status AS c2 
//                SET c1.unisender_send_result = c2.send_result,
//                c1.unisender_letter_id = c2.letter_id,
//                c1.unisender_last_update= c2.last_update
//            WHERE c2.email = c1.email
//                and c2.letter_id = $message_id
//                and c1.batch_id = $batch_id;";
        
//        
//        $db = Yii::$app->db;
//        $command =  $db->createCommand($SQL);
//        $command->execute();
        
          return 1;
        
        } 
        
        return 0;
        
    }
    public static function updateStatus($local_campaign_id){
        
        
        $inf =   \app\models\UnisenderCampaign::getFullInfo($local_campaign_id);
        

        
            //UnisenderDeliveryStatus::saveStatus($inf['campaign']['unisender_id'], $inf['campaign']['agency_id']);
            
            
            $delivery_statuses = UnisenderDeliveryStatus::find()->where(['campaign_id' => $local_campaign_id])->batch();
            
          
            if ($delivery_statuses){
                foreach ($delivery_statuses as $stat){
                    $crm_user = CrmUsers::find()->where([
                    //     'batch_id'=>[implode(',', $inf['campaign']['batches_id'])],
                        'email'=>@$stat['email']])->one();
                    if ($crm_user){
                        $crm_user->unisender_send_result = @$stat->send_result;
                        $crm_user->unisender_letter_id = @$stat->letter_id;
                        $crm_user->unisender_last_update = @$stat->last_update;
                        $crm_user->update(false);
                    }

                }
            }
//            $SQL = "UPDATE crm_users AS c1, unisender_delivery_status AS c2 
//                SET c1.unisender_send_result = c2.send_result,
//                c1.unisender_letter_id = c2.letter_id,
//                c1.unisender_last_update= c2.last_update
//            WHERE c2.email = c1.email
//                and c2.letter_id = $message_id
//                and c1.batch_id = $batch_id;";
        
//        
//        $db = Yii::$app->db;
//        $command =  $db->createCommand($SQL);
//        $command->execute();
        
          return 1;
        
        
        
        
        
    }
    
    
    
    
    public static function addCrmTasksChain($batch_id){
        
            $count_source = CsvBatches::find()->where(['batch_id'=>$batch_id])->count();
            
            
            if ($count_source){
              
                $count =  CrmUsers::find()->where(['batch_id'=>$batch_id])->count();
                    if ($count ==0){
                        $data_insert = CrmUsers::insertUsers2($batch_id);
                    }

                     //$res =  CronTask::addTask('UnisenderExportContacts', $batch_id, $limit, $list_id);

                     //CronTask::addTask($task_name, $batch_id, $step_limit, $list_id, $validate_statuses, $list_count, $status_id, $row_count)
                    
                     $res =  CronTask::addTask('FillSmokingHabbits', 
                                      $batch_id, 
                                        10, 
                                        0, 
                                        0, 
                                        0, 
                                        -4, $count);
                    
//                   $res =  CronTask::addTask('checkEmailAvability', 
//                                      $batch_id,
//                                        10, 
//                                        0, 
//                                        0, 
//                                        0, 
//                                        1, $count);
//                   $res =  CronTask::addTask('RegisterBasic', 
//                                      $batch_id, 
//                                        10, 
//                                        0, 
//                                        0, 
//                                        0, 
//                                        -4, $count);
//                   $res =  CronTask::addTask('UpdateProfile', 
//                                      $batch_id, 
//                                        10, 
//                                        0, 
//                                        0, 
//                                        0, 
//                                        -4, $count);
//                   $res =  CronTask::addTask('FillSmokingHabbits', 
//                                      $batch_id, 
//                                        10, 
//                                        0, 
//                                        0, 
//                                        0, 
//                                        -4, $count);
                   
            }
           return 1;
             
        
    }
    
    
    
}
