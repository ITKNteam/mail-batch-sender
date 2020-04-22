<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;

use Yii;
use yii\BaseYii;
use yii\base\Model;
use yii\web\UploadedFile;

use yii\helpers\ArrayHelper;




class AceptBatch extends Model
{
   /**
     * @var UploadedFile file attribute
     */
    public $batch_id;
    public $list_id;
    public $agency_id;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['batch_id', 'list_id', 'agency_id'], 'safe'],
        ];
    }
    
    
    public function attributeLabels()
    {
        return [
            
            'batch_id' => 'Загрузка',
            'list_id' => 'Список контактов',
            'agency_id' => 'Агентство',
            
        ];
    }
    
    
        
        
    
    public static function sendContacts($list_id, $batch_id, $step, $limit){

       $message = '';
       $answer_id = 0;
       $answer = [];
       $total = 0;
       $inserted= 0;
       $updated= 0;
       $deleted= 0;
       $new_emails= 0;
       $invalid= 0;
       $log= [];
       
       
       if (!$limit)
          $limit = 500;
       $offset = 0;
       $k = $step-1;
       
       $cols_count = 16;
     
       
       
      $unisender_list_id = UnisenderList::find()->where(['id'=>$list_id])->select('list_id, agency_id')->one();
      
      $agency_batch = AgencyCsvBatch::find()->where(['id'=>$batch_id])->one();
     
      $count =  $agency_batch->string_count;
      $agency_id   =  $agency_batch->agency_id;
      $api_key = AgencySettings::getApiKey($agency_id);
      
      
   //   $steps =   ceil ($count/$limit);
        
    //  for ($k = 0, $j = $steps; $k < $j; $k++) {
            $contactss = [];
            
            if ($k)
             $offset = $k* $limit;  
            
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
           $test_res_param_id = AgencySettings::getAgencyParamKey($agency_id, 'test_res');
           $email_param_id = AgencySettings::getAgencyParamKey($agency_id, 'email');
           $name_param_id = AgencySettings::getAgencyParamKey($agency_id, 'f_name');

           foreach ($data as $row){
             ++$i;


              $add_data = CompareDataEmail::getData(@$row[$test_res_param_id],$test_res_param_id, $agency_id );
                $contactss[$i]['email']=@$row[$email_param_id];     
                $contactss[$i]['Name']=@$row[$name_param_id];     
               //$contactss[$i]['phone']=$row[13];     
               $contactss[$i]['phone']='';     
               $contactss[$i]['personal_message']=$add_data['personal_message'];     
               $contactss[$i]['britain_percent']=$add_data['britain_percent'];     
           }

            
            //$api_key = '5c4zsonozq3mbtwhgj883psnis4xyzkzaxuzbb3o';
            // $unisender_list_id = 5893658;  

            // $unisender_return = Yii::$app->unisender->importContacts($unisender_list_id, $contactss, $api_key );
               $unisender_return = Yii::$app->unisender->importContacts($unisender_list_id['list_id'], $contactss, $api_key );
            
              if (@$unisender_return['UnisenderAnswer']->result->total){
                  $u_contacts = new UnisenderContacts();


                   $total = @$unisender_return['UnisenderAnswer']->result->total;
                   $inserted = @$unisender_return['UnisenderAnswer']->result->inserted;
                   $updated = @$unisender_return['UnisenderAnswer']->result->updated;
                   $deleted = @$unisender_return['UnisenderAnswer']->result->deleted;
                   $new_emails = @$unisender_return['UnisenderAnswer']->result->new_emails;
                   $invalid = @$unisender_return['UnisenderAnswer']->result->invalid;
                   $u_contacts->agency_id = $agency_id;
                   $u_contacts->batch_id = $batch_id;
                   $u_contacts->total = $total;
                   $u_contacts->inserted = $inserted;
                   $u_contacts->updated = $updated;
                   $u_contacts->deleted = $deleted;
                   $u_contacts->new_emails = $new_emails;
                   $u_contacts->invalid = $invalid;
                   $u_contacts->list_id = $list_id;

                   $u_contacts->uid_create = Yii::$app->user->id;
                   $u_contacts->dt_create = date("Y-m-d H:i:s");
                   $u_contacts->save();

                   $message .= "<br><br>Операция выполнена успешно!"
                           . "<br>Всего контактов : $total"
                           . "<br>Вставленно контактов : $inserted"
                           . "<br>Обновленно контактов : $updated"
                           . "<br>Удаленно контактов : $deleted"
                           . "<br>Новых  email : $new_emails"
                           . "<br>Некоретных : $invalid"
                           . "";
                   
                   
                   $answer_id = $u_contacts->id;

                   $AgencyBatch = AgencyCsvBatch::find()->where(['id'=>$batch_id, 'agency_id'=>$agency_id])->one();
                   $AgencyBatch->status_id = 2;
                   $AgencyBatch->update(false);

               } else {
                   
                    $message .= @$unisender_return['UnisenderAnswer']->error;
                    $answer_id = 0;
                    
               }
       
               
             
      // }  
        
       $answer = ['id'=>$answer_id,
                   'message'=> $message  ];
        
        return  $answer;
          
        
    }
        
    
    public static function subscribeContacts($list_id, $batch_id, $step, $limit){

       $message = '';
       $answer_id = 0;
       $answer = [];
       $total = 0;
       $inserted= 0;
       $updated= 0;
       $deleted= 0;
       $new_emails= 0;
       $invalid= 0;
       $log= [];
       
       
       if (!$limit)
          $limit = 500;
       $offset = 0;
       $k = $step-1;
       
       $cols_count = 16;
     
       
       
      $unisender_list_id = UnisenderList::find()->where(['id'=>$list_id])->select('list_id, agency_id')->one();
      
      $agency_batch = AgencyCsvBatch::find()->where(['id'=>$batch_id])->one();
     
      $count =  $agency_batch->string_count;
      $agency_id   =  $agency_batch->agency_id;
      $api_key = AgencySettings::getApiKey($agency_id);
      
      
   //   $steps =   ceil ($count/$limit);
        
    //  for ($k = 0, $j = $steps; $k < $j; $k++) {
            $contactss = [];
            
            if ($k)
             $offset = $k* $limit;  
            
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
           $test_res_param_id = AgencySettings::getAgencyParamKey($agency_id, 'test_res');
           $email_param_id = AgencySettings::getAgencyParamKey($agency_id, 'email');
           $name_param_id = AgencySettings::getAgencyParamKey($agency_id, 'f_name');

           foreach ($data as $row){
             ++$i;


              $add_data = CompareDataEmail::getData(@$row[$test_res_param_id],$test_res_param_id, $agency_id );
                $contactss[$i]['email']=@$row[$email_param_id];     
                $contactss[$i]['Name']=@$row[$name_param_id];     
               //$contactss[$i]['phone']=$row[13];     
               $contactss[$i]['phone']='';     
               $contactss[$i]['personal_message']=$add_data['personal_message'];     
               $contactss[$i]['britain_percent']=$add_data['britain_percent'];     
            //   $contactss[$i]['personal_message']='test';     
            //   $contactss[$i]['britain_percent']='25';     
           }

           
         //  print_r($contactss);
           
           foreach ($contactss as $contact){
           
               $email =  $contact['email'];
               
               $unisender_return =  Yii::$app->unisender->subscribe(
                       $unisender_list_id['list_id'], 
                       //6085870,
                        $contact['email'], $contact['Name'], 
                        $contact['phone'], $contact['personal_message'], 
                        $contact['britain_percent'], 0, $api_key);  
               
              //  print_r($unisender_return);
                     
            
              if (@$unisender_return['message']){
                  $u_contacts = new UnisenderSubscribe();


                   $unisender_message = @$unisender_return['message'];
                   $person_id = @$unisender_return['UnisenderAnswer']->result->person_id;
                   $code = @$unisender_return['UnisenderAnswer']->code;
                   
                   $u_contacts->agency_id = $agency_id;
                   $u_contacts->batch_id = $batch_id;
                   $u_contacts->message = $unisender_message;
                   $u_contacts->person_id = $person_id;
                   $u_contacts->code = $code;
                   $u_contacts->email = $email;
                   
                   $u_contacts->list_id = $list_id;

                   $u_contacts->dt_create = date("Y-m-d H:i:s");
                   $u_contacts->save(false);

                   $message .= @$unisender_return['message'];
                   
                   $answer_id = $u_contacts->id;


               } else {
                   
                    $message .= @$unisender_return['UnisenderAnswer']->error;
                    $answer_id = 0;
                    
               }
               
               
                 
             }
             
               $AgencyBatch = AgencyCsvBatch::find()->where(['id'=>$batch_id, 'agency_id'=>$agency_id])->one();
                   $AgencyBatch->status_id = 2;
                   $AgencyBatch->update(false);
               
             
      // }  
        
       $answer = ['id'=>$answer_id,
                   'message'=> $message  ];
        
        return  $answer;
          
        
    }
   
    
    
      
    /* Данная функция используется для проверки валидности контактов со статусом unknown
    * 
    */
    public static function subscribeContactsUnknown($batch_id,  $list_id, $limit){
        
       
       $message = '';
       $answer_id = 0;
       
       
      // $list_id= 506;
     //  for ($i = 1; $i <= $limit; $i++){
       
//       
//                $unisender_list_id = UnisenderList::find()->where(['id'=>$list_id])->select('list_id, agency_id')->one();
//
//                $agency_batch = AgencyCsvBatch::find()->where(['id'=>$batch_id])->one();
//                $campaign_id = $agency_batch->campaign_id;
//
//                $agency_id   =  $unisender_list_id->agency_id;
//                $api_key = AgencySettings::getApiKey($agency_id);
//
//

                
//       if ($batch_id < 0){
//                  $data = CsvBatches::find()->where(['status_id'=> 3, 
//                                                     'list_id'=>$list_id,
//                                            ])
//                         ->limit($limit)
//                         ->all();
//       }          
//       else
           if ($list_id == 989){
         $data = CsvBatches::find()->where(['status_id'=> 3, 
                                                     'list_id'=>$list_id,
                                            ])
                         ->limit($limit)
                         ->all();  
                
       } else {
           $data = CsvBatches::find()->where(['batch_id'=>$batch_id, 
                                                     'status_id'=> 3, 
                                                     'list_id'=>$list_id,
                                            ])
                         ->limit($limit)
                         ->all();
           
       }

                if (count($data)){

                  foreach ($data as $csv_rows) {  

                      //$list_id = $csv_rows->list_id;
                      $unisender_list_id = UnisenderList::find()->where(['id'=>$list_id])->select('list_id, agency_id')->one();
                      $agency_id   =  $unisender_list_id->agency_id;
                      
                      $email = $csv_rows->email;
                      
                      $api_key = AgencySettings::getApiKey($agency_id);

                          $fields = [
                               'fields[email]'=>$csv_rows->email,
                               'fields[Name]'=> $csv_rows->f_name,
//                               'fields[phone]'=> $csv_rows->phone,

                           ];


                           
                         $double_optin = 0;       
                         $unisender_return =  Yii::$app->unisender->subscribe2(
                                 $unisender_list_id['list_id'], 
                                  $fields,
                                  $double_optin, $api_key);  
                         
                        //  print_r($unisender_return);


                        if (@$unisender_return['message']){
                            $u_contacts = new UnisenderSubscribe();


                             $unisender_message = @$unisender_return['message'];
                             $person_id = @$unisender_return['UnisenderAnswer']->result->person_id;
                             $code = @$unisender_return['UnisenderAnswer']->code;

                             $u_contacts->agency_id = $agency_id;
                             $u_contacts->batch_id = $batch_id;
                             $u_contacts->message = $unisender_message;
                             $u_contacts->person_id = $person_id;
                             $u_contacts->code = $code;
                             $u_contacts->email = $email;

                             $u_contacts->list_id = $list_id;

                             $u_contacts->dt_create = date("Y-m-d H:i:s");
                             $u_contacts->save(false);

                             $message .= @$unisender_return['message'];

                             $answer_id = $u_contacts->id;


                         } else {

                              $message .= @$unisender_return['UnisenderAnswer']->error;
                              $answer_id = 0;

                         }
                         
                          CsvBatches::updateAll(['status_id'=>7,
                                                'dt_load'=> date("Y-m-d H:i:s"),
                                                'sub_person_id'  => $person_id,  
                                                'sub_code'  => $code,
                                                'sub_message'=>$message
                                        ],  "id = $csv_rows->id");    
                         
                        
                         

                          
                         $message = '';

                   }

                   

                             
                }              

                 $answer = ['id'=>$answer_id,
                             'message'=> $message  ];
                 
    //   }         
        return  $answer;
          
        
    }
    
    /* Данная функция отличается от родительской, 
    *  тем что использует таблицу csv_batches для выборки контактов
    * 
    */
    public static function subscribeContacts2($list_id, $batch_id, $step, $limit){
        
       $data_insert = []; 
       $message = '';
       $answer_id = 0;
       $answer = [];
       $total = 0;
       $inserted= 0;
       $updated= 0;
       $deleted= 0;
       $new_emails= 0;
       $invalid= 0;
       $log= [];
       $unisender_subscribe_limit = 50;
       
       if (!$limit)
          $limit = 5;
       $offset = 0;
       $k = $step-1;
       
       $cols_count = 16;
     
     //  for ($i = 1; $i <= $limit; $i++){
       
       
                $unisender_list_id = UnisenderList::find()->where(['id'=>$list_id])->select('list_id, agency_id')->one();

                $agency_batch = AgencyCsvBatch::find()->where(['id'=>$batch_id])->one();
                $campaign_id = $agency_batch->campaign_id;

                $agency_id   =  $unisender_list_id->agency_id;
                $api_key = AgencySettings::getApiKey($agency_id);




                  $cityes =   UnisenderListAvailableCityes::find()->where(['unisender_list_id'=>$list_id])->select(['city'])->asArray()->all();

                  if ($cityes){

                      $data = CsvBatches::find()->where(['batch_id'=>$batch_id,'status_id'=> 1])
                              ->andWhere(['in',  'activity_loc', ArrayHelper::getColumn($cityes, 'city')])
                             ->andWhere(['like',  'email', ['@'] ])
                             ->limit($limit)
                          //   ->offset($offset)
                             ->all();
                  } 
                  else{

                  $data = CsvBatches::find()->where(['batch_id'=>$batch_id, 'status_id'=> 1])
                          ->andWhere(['like',  'email', ['@'] ])
                          ->limit($limit)
                        // ->offset($offset)
                         ->all();
                  }

                if (count($data)){

                  foreach ($data as $csv_rows) {  

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
                                      $hostess_name = $csv_rows->hostess_name;
                                     $user_key ='';
                                     $rec_status = 0;
                                     $last_dt = date("Y-m-d H:i:s");


                          $data_insert[] = [
                                      $batch_id,

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
                                      $hostess_name

                                    ] ;

                          $f = [];

                          $fields = [
                               'fields[email]'=>$csv_rows->email,
                               'fields[Name]'=> $csv_rows->l_name,
                               'fields[phone]'=> $csv_rows->phone,


                           ];


                             $сfields = CompareDataEmail::find()->where(
                                  ['campaign_id'=>$campaign_id])->all();    
                              foreach ($сfields as $row){
                                 $row_name = $row->param->sysParametr->sys_name;
                                 //$field_id =  $row->unisenderMailTplFileds->u_field_id;
                                 $field_name =  @$row->unisenderMailTplFileds->uField->new_field_name;
                                 $compare_value =  $row->value;
                                 $tpl_value =  @$row->mailTpl->mail_body;
                                 $data1 = CsvBatches::find()->where(['id'=>$csv_rows->id])->one();
                               //  foreach ($data1 as $dt){
                                      //echo    $field_name.' '. $data1->$row_name. ' - '. $compare_value. '<br>';
                                      if ($data1->$row_name == $compare_value && $field_name)
                                        $f = array_merge($f, ["fields[$field_name]"=>$tpl_value]);
                                 //}


                              }

                              $fields = array_merge($fields, $f);
                              unset($f);




                         $double_optin = 3;       
                         $unisender_return =  Yii::$app->unisender->subscribe2(
                                 $unisender_list_id['list_id'], 
                                  $fields,
                                  $double_optin, $api_key);  
                         unset($fields);
                        //  print_r($unisender_return);


                        if (@$unisender_return['message']){
                            $u_contacts = new UnisenderSubscribe();


                             $unisender_message = @$unisender_return['message'];
                             $person_id = @$unisender_return['UnisenderAnswer']->result->person_id;
                             $code = @$unisender_return['UnisenderAnswer']->code;

                             $u_contacts->agency_id = $agency_id;
                             $u_contacts->batch_id = $batch_id;
                             $u_contacts->message = $unisender_message;
                             $u_contacts->person_id = $person_id;
                             $u_contacts->code = $code;
                             $u_contacts->email = $email;

                             $u_contacts->list_id = $list_id;

                             $u_contacts->dt_create = date("Y-m-d H:i:s");
                             $u_contacts->save(false);

                             $message .= @$unisender_return['message'];

                             $answer_id = $u_contacts->id;


                         } else {

                              $message .= @$unisender_return['UnisenderAnswer']->error;
                              $answer_id = 0;

                         }



                         //$data->status_id = 2;
                         //$data->update(false);

//                        $condition = "id = $task_id";
//                        static::updateAll(['current_step'=> $step+1 ,
//                            'last_dt'=>date('Y-m-d H:i:s')
//                        ], $condition);
//         
                         
                         CsvBatches::updateAll(['status_id'=>2, 'dt_load'=> date("Y-m-d H:i:s")], 
                                                "id = $csv_rows->id");      


                   }

                   if (isset($data_insert)){
                       //вставка в таблицу
                          $db = Yii::$app->db;
                          $sql = $db->queryBuilder->batchInsert(CrmUsers::tableName(), [
                                      'batch_id',
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
                                      'hostess_name'], $data_insert);
                        $res =  $db->createCommand($sql )->execute();
                   }
                         $AgencyBatch = AgencyCsvBatch::find()->where(['id'=>$batch_id, 'agency_id'=>$agency_id])->one();
                             $AgencyBatch->status_id = 2;
                             $AgencyBatch->update(false);

                             
                }              

                 $answer = ['id'=>$answer_id,
                             'message'=> $message  ];
                 unset($data_insert);
                 unset($answer);
        
    //   }         
        return  1;
          
        
    }
    
    
    
    
    ////Сегментация по стаусам
     public static function subscribeContactsStatusSegment($list_id, $batch_id, $step, $limit, $status){
        
       $data_insert = []; 
       $message = '';
       $answer_id = 0;
       $answer = [];
       $total = 0;
       $inserted= 0;
       $updated= 0;
       $deleted= 0;
       $new_emails= 0;
       $invalid= 0;
       $log= [];
       $unisender_subscribe_limit = 50;
       
       if (!$limit)
          $limit = 5;
       $offset = 0;
       $k = $step-1;
       
       $cols_count = 16;
     
     //  for ($i = 1; $i <= $limit; $i++){
       
       
                $unisender_list_id = UnisenderList::find()->where(['id'=>$list_id])->select('list_id, agency_id')->one();

                $agency_batch = AgencyCsvBatch::find()->where(['id'=>$batch_id])->one();
                $campaign_id = $agency_batch->campaign_id;

                $agency_id   =  $unisender_list_id->agency_id;
                $api_key = AgencySettings::getApiKey($agency_id);




                 $validate_event = explode(',', $status);
                        $data = CsvBatches::find()->where(['batch_id'=>$batch_id,'status_id'=> 3])
                              ->andWhere(['in',  'validate_event', $validate_event])
                             
                             ->limit($limit)
                          //   ->offset($offset)
                             ->all();

                if (count($data)){

                  foreach ($data as $csv_rows) {  

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
                                      $hostess_name = $csv_rows->hostess_name;
                                     $user_key ='';
                                     $rec_status = 0;
                                     $last_dt = date("Y-m-d H:i:s");


                          $data_insert[] = [
                                      $batch_id,

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
                                      $hostess_name

                                    ] ;

                          $f = [];
                          
                          if ($csv_rows->test_res = 25) {
                              $value_balcan_personal_message = '<img src="http://westbtl.dev.shavrak.ru/data/uploads/ps_email_Balkanka/ps_email/letter_25_6/img/image1.png" alt="25% Британец" style="display: block; width: 100%;" border="0" />';
                          }
                          if ($csv_rows->test_res = 65) {
                              $value_balcan_personal_message = '<img src="http://westbtl.dev.shavrak.ru/data/uploads/ps_email_Balkanka/ps_email/letter_65_blue/img/image1.png" alt="65% Британец" style="display: block; width: 100%;" border="0" />';
                          }
                          if ($csv_rows->test_res = 90) {
                              $value_balcan_personal_message = '<img src="http://westbtl.dev.shavrak.ru/data/uploads/ps_email_Balkanka/ps_email/letter_95_blue/img/image1.png" alt="90% Британец" style="display: block; width: 100%;" border="0" />';
                          }
                          
                          $fields = [
                               'fields[email]'=>$csv_rows->email,
                               'fields[Name]'=> $csv_rows->l_name,
                               'fields[phone]'=> $csv_rows->phone,
                               'fields[balcan_personal_message]'=>$value_balcan_personal_message 

                           ];


//                             $сfields = CompareDataEmail::find()->where(
//                                  ['campaign_id'=>$campaign_id])->all();    
//                              foreach ($сfields as $row){
//                                 $row_name = $row->param->sysParametr->sys_name;
//                                 //$field_id =  $row->unisenderMailTplFileds->u_field_id;
//                                 $field_name =  @$row->unisenderMailTplFileds->uField->new_field_name;
//                                 $compare_value =  $row->value;
//                                 $tpl_value =  @$row->mailTpl->mail_body;
//                                 $data1 = CsvBatches::find()->where(['id'=>$csv_rows->id])->one();
//                               //  foreach ($data1 as $dt){
//                                      //echo    $field_name.' '. $data1->$row_name. ' - '. $compare_value. '<br>';
//                                      if ($data1->$row_name == $compare_value && $field_name)
//                                        $f = array_merge($f, ["fields[$field_name]"=>$tpl_value]);
//                                 //}
//
//
//                              }
//                              
//                              
//                              
//
//                              $fields = array_merge($fields, $f);
//                              unset($f);




                         $double_optin = 3;       
                         $unisender_return =  Yii::$app->unisender->subscribe2(
                                 $unisender_list_id['list_id'], 
                                  $fields,
                                  $double_optin, $api_key);  
                         unset($fields);
                        //  print_r($unisender_return);


                        if (@$unisender_return['message']){
                            $u_contacts = new UnisenderSubscribe();


                             $unisender_message = @$unisender_return['message'];
                             $person_id = @$unisender_return['UnisenderAnswer']->result->person_id;
                             $code = @$unisender_return['UnisenderAnswer']->code;

                             $u_contacts->agency_id = $agency_id;
                             $u_contacts->batch_id = $batch_id;
                             $u_contacts->message = $unisender_message;
                             $u_contacts->person_id = $person_id;
                             $u_contacts->code = $code;
                             $u_contacts->email = $email;

                             $u_contacts->list_id = $list_id;

                             $u_contacts->dt_create = date("Y-m-d H:i:s");
                             $u_contacts->save(false);

                             $message .= @$unisender_return['message'];

                             $answer_id = $u_contacts->id;


                         } else {

                              $message .= @$unisender_return['UnisenderAnswer']->error;
                              $answer_id = 0;

                         }



                         //$data->status_id = 2;
                         //$data->update(false);

//                        $condition = "id = $task_id";
//                        static::updateAll(['current_step'=> $step+1 ,
//                            'last_dt'=>date('Y-m-d H:i:s')
//                        ], $condition);
//         
                         
                         CsvBatches::updateAll(['status_id'=>4, 'dt_load'=> date("Y-m-d H:i:s")], 
                                                "id = $csv_rows->id");      


                   }

                   if (isset($data_insert)){
                       //вставка в таблицу
                          $db = Yii::$app->db;
                          $sql = $db->queryBuilder->batchInsert(CrmUsers::tableName(), [
                                      'batch_id',
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
                                      'hostess_name'], $data_insert);
                        $res =  $db->createCommand($sql )->execute();
                   }
                         $AgencyBatch = AgencyCsvBatch::find()->where(['id'=>$batch_id, 'agency_id'=>$agency_id])->one();
                             $AgencyBatch->status_id = 2;
                             $AgencyBatch->update(false);

                             
                }              

                 $answer = ['id'=>$answer_id,
                             'message'=> $message  ];
                 unset($data_insert);
                 unset($answer);
        
    //   }         
        return  1;
          
        
    }
    
    
    
    
    public static function validateEmail($batch_id=0, $limit = 25){
        
        
        $data = CsvBatches::find()->where(['batch_id'=>$batch_id, 'status_id'=> 1])
                       //       ->andWhere(['in',  'activity_loc', ArrayHelper::getColumn($cityes, 'city')])
                             ->andWhere(['like',  'email', ['@'] ])
                         //    ->andWhere(['like',  'email', ['@'] ])
                             ->limit($limit)
                          //   ->offset($offset)
                             ->all();
        
        
        $count = CsvBatches::find()->where(['batch_id'=>$batch_id, 'status_id'=> 1])
                             ->andWhere(['like',  'email', ['@'] ])
                             ->count();
        
        foreach ($data as $csv_rows) {  
            $result = \app\components\BulkEmailChecker::check($csv_rows->email);
                   $status   = $result['status'];
                   $event    = $result['event'];
                   $details  = $result['details'];
        
               if (empty($status)) {
                   $status = 'unknown';
               }
                   
                   
                  CsvBatches::updateAll(['status_id'=>3, 
                                        'validate_status'=>$status,
                                        'validate_event'=>$event,
                                        'validate_details'=>$details,    
                                        'validate_dt'=> date("Y-m-d H:i:s")], 
                                         "id = $csv_rows->id");   
               
                 unset($result);
        }
        
        return $count;
    }
    
    
    
    public static function validateEmailError(){
        
        
        $data = CsvBatches::find()->where(['validate_status'=>'error'])
                              ->limit(25)
                          
                             ->all();
        
        
       
        
        foreach ($data as $csv_rows) {  
            $result = \app\components\BulkEmailChecker::check($csv_rows->email);
                   $status   = $result['status'];
                   $event    = $result['event'];
                   $details  = $result['details'];
        
               
                  CsvBatches::updateAll(['status_id'=>3, 
                                        'validate_status'=>$status,
                                        'validate_event'=>$event,
                                        'validate_details'=>$details,    
                                        'validate_dt'=> date("Y-m-d H:i:s")], 
                                         "id = $csv_rows->id");   
               
                 unset($result);
        }
        
        return 1;
    }
    
    
    /*
     * $mode = 0  - выборка passed со статусом 3 - что означает, первичную валидацию через bulemailchecker
     * $mode = 1  - выборка passed со статусом 8 - что означает, почты сос статусом unknown прошли валидацию через Unisender
     */
    
    public static function sendContactsSimple($list_id, $batch_id, $step, $limit, $status, $mode=0){
    
        
        
        

       $message = '';
       $answer_id = 0;
       $answer = [];
       $total = 0;
       $inserted= 0;
       $updated= 0;
       $deleted= 0;
       $new_emails= 0;
       $invalid= 0;
       $log= [];
       $value_balcan_personal_message = '';
       
       
       if (!$limit)
          $limit = 4;
       $offset = 0;
       $k = $step-1;
       
       $cols_count = 16;
     
      $i=0;
       
      $unisender_list_id = UnisenderList::find()->where(['id'=>$list_id])->select('list_id, agency_id')->one();
      
      $agency_batch = AgencyCsvBatch::find()->where(['id'=>$batch_id])->one();
     
      $count =  $agency_batch->string_count;
      $agency_id   =  $agency_batch->agency_id;
      $api_key = AgencySettings::getApiKey($agency_id);
      
      
   //   $steps =   ceil ($count/$limit);
        
    //  for ($k = 0, $j = $steps; $k < $j; $k++) {
            $contactss = [];
            
                        $validate_status = explode(',', $status);
                        
                        $staus_id = 3;
                        if ($mode==1){
                            $staus_id = 8;
                        }
                        
                        $data = CsvBatches::find()->where(['batch_id'=>$batch_id,
                                                           'status_id'=> $staus_id])
                              ->andWhere(['in',  'validate_status', $validate_status])
                             
                             ->limit($limit)
                          //   ->offset($offset)
                             ->all();
            
            

           foreach ($data as $csv_rows){
             ++$i;

             
             
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
                                      $hostess_name = $csv_rows->hostess_name;
                                     $user_key ='';
                                     $rec_status = 0;
                                     $last_dt = date("Y-m-d H:i:s");


                          $data_insert[] = [
                                      $batch_id,

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
                                      $hostess_name

                                    ] ;

                          
                          
                          if ($csv_rows->test_res == 25) {
                              $value_balcan_personal_message = '<img src="http://westbtl.dev.shavrak.ru/data/uploads/ps_email_Balkanka/ps_email/letter_25_6/img/image1.png" alt="25% Британец" style="display: block; width: 100%;" border="0" />';
                          }
                          if ($csv_rows->test_res == 65) {
                              $value_balcan_personal_message = '<img src="http://westbtl.dev.shavrak.ru/data/uploads/ps_email_Balkanka/ps_email/letter_65_blue/img/image1.png" alt="65% Британец" style="display: block; width: 100%;" border="0" />';
                          }
                          if ($csv_rows->test_res == 90) {
                              $value_balcan_personal_message = '<img src="http://westbtl.dev.shavrak.ru/data/uploads/ps_email_Balkanka/ps_email/letter_95_blue/img/image1.png" alt="90% Британец" style="display: block; width: 100%;" border="0" />';
                          }
                          
                       
             

              
                $contactss[$i]['email']= $email;     
                $contactss[$i]['Name']= $f_name;     
                $contactss[$i]['phone']= '';     
               
               // $contactss[$i]['balcan_personal_message']= "''";     
                $contactss[$i]['balcan_personal_message']= $value_balcan_personal_message;     
           
                CsvBatches::updateAll(['status_id'=>4, 'dt_load'=> date("Y-m-d H:i:s")], 
                                                "id = $csv_rows->id");      
               
            }

                        
                        
                   
            
               $unisender_return = Yii::$app->unisender->importContacts($unisender_list_id['list_id'], $contactss, $api_key );
            
              if (@$unisender_return['UnisenderAnswer']->result->total){
                  $u_contacts = new UnisenderContacts();


                   $total = @$unisender_return['UnisenderAnswer']->result->total;
                   $inserted = @$unisender_return['UnisenderAnswer']->result->inserted;
                   $updated = @$unisender_return['UnisenderAnswer']->result->updated;
                   $deleted = @$unisender_return['UnisenderAnswer']->result->deleted;
                   $new_emails = @$unisender_return['UnisenderAnswer']->result->new_emails;
                   $invalid = @$unisender_return['UnisenderAnswer']->result->invalid;
                   $u_contacts->agency_id = $agency_id;
                   $u_contacts->batch_id = $batch_id;
                   $u_contacts->total = $total;
                   $u_contacts->inserted = $inserted;
                   $u_contacts->updated = $updated;
                   $u_contacts->deleted = $deleted;
                   $u_contacts->new_emails = $new_emails;
                   $u_contacts->invalid = $invalid;
                   $u_contacts->list_id = $list_id;

                   //$u_contacts->uid_create = Yii::$app->user->id;
                   $u_contacts->dt_create = date("Y-m-d H:i:s");
                   $u_contacts->save(false);

                   $message .= "<br><br>Операция выполнена успешно!"
                           . "<br>Всего контактов : $total"
                           . "<br>Вставленно контактов : $inserted"
                           . "<br>Обновленно контактов : $updated"
                           . "<br>Удаленно контактов : $deleted"
                           . "<br>Новых  email : $new_emails"
                           . "<br>Некоретных : $invalid"
                           . "";
                   
                   
                   $answer_id = $u_contacts->id;

                   $AgencyBatch = AgencyCsvBatch::find()->where(['id'=>$batch_id, 'agency_id'=>$agency_id])->one();
                   $AgencyBatch->status_id = 2;
                   $AgencyBatch->update(false);
                   
                   
                   
                   
                   if (isset($data_insert)){
                       //вставка в таблицу
                          $db = Yii::$app->db;
                          $sql = $db->queryBuilder->batchInsert(CrmUsers::tableName(), [
                                      'batch_id',
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
                                      'hostess_name'], $data_insert);
                        $res =  $db->createCommand($sql )->execute();
                   }
                         
                   
                   

               } else {
                   
                    $message .= @$unisender_return['UnisenderAnswer']->error;
                    $answer_id = 0;
                    
               }
       
                
             
      // }  
        
       $answer = ['id'=>$answer_id,
                   'message'=> $message  ];
        
        return  $answer;
        
          
        
    }
        
    
    
      /*
     * $mode = 0  - выборка passed со статусом 3 - что означает, первичную валидацию через bulemailchecker
     * $mode = 1  - выборка passed со статусом 8 - что означает, почты сос статусом unknown прошли валидацию через Unisender
     */
    
    public static function sendContactsUnknown($list_id, $batch_id, $step, $limit, $status='unknown', $mode=0){
    
        
        
        

       $message = '';
       $answer_id = 0;
       $answer = [];
       $total = 0;
       $inserted= 0;
       $updated= 0;
       $deleted= 0;
       $new_emails= 0;
       $invalid= 0;
       $log= [];
       $value_balcan_personal_message = '';
       
       
       if (!$limit)
          $limit = 4;
       $offset = 0;
       $k = $step-1;
       
       $cols_count = 16;
     
      $i=0;
       
      $unisender_list_id = UnisenderList::find()->where(['id'=>$list_id])->select('list_id, agency_id')->one();
      
      $agency_batch = AgencyCsvBatch::find()->where(['id'=>$batch_id])->one();
     
      $count =  $agency_batch->string_count;
      $agency_id   =  $agency_batch->agency_id;
      $api_key = AgencySettings::getApiKey($agency_id);
      
      
   //   $steps =   ceil ($count/$limit);
        
    //  for ($k = 0, $j = $steps; $k < $j; $k++) {
            $contactss = [];
            
                        $validate_status = explode(',', $status);
                        
                        $staus_id = 3;
                        if ($mode==1){
                            $staus_id = 8;
                        }
                        
                        $data = CsvBatches::find()->where(['batch_id'=>$batch_id,'status_id'=> $staus_id])
                              ->andWhere(['in',  'validate_status', $validate_status])
                             
                             ->limit($limit)
                          //   ->offset($offset)
                             ->all();
            
            

           foreach ($data as $csv_rows){
             ++$i;

             
             
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
                                      $hostess_name = $csv_rows->hostess_name;
                                     $user_key ='';
                                     $rec_status = 0;
                                     $last_dt = date("Y-m-d H:i:s");


                          $data_insert[] = [
                                      $batch_id,

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
                                      $hostess_name

                                    ] ;

                          
                          
                          if ($csv_rows->test_res == 25) {
                              $value_balcan_personal_message = '<img src="http://westbtl.dev.shavrak.ru/data/uploads/ps_email_Balkanka/ps_email/letter_25_6/img/image1.png" alt="25% Британец" style="display: block; width: 100%;" border="0" />';
                          }
                          if ($csv_rows->test_res == 65) {
                              $value_balcan_personal_message = '<img src="http://westbtl.dev.shavrak.ru/data/uploads/ps_email_Balkanka/ps_email/letter_65_blue/img/image1.png" alt="65% Британец" style="display: block; width: 100%;" border="0" />';
                          }
                          if ($csv_rows->test_res == 90) {
                              $value_balcan_personal_message = '<img src="http://westbtl.dev.shavrak.ru/data/uploads/ps_email_Balkanka/ps_email/letter_95_blue/img/image1.png" alt="90% Британец" style="display: block; width: 100%;" border="0" />';
                          }
                          
                       
             

              
                $contactss[$i]['email']= $email;     
                $contactss[$i]['Name']= $f_name;     
                $contactss[$i]['phone']= '';     
               
               // $contactss[$i]['balcan_personal_message']= "''";     
                $contactss[$i]['balcan_personal_message']= $value_balcan_personal_message;     
           
                CsvBatches::updateAll(['status_id'=>4, 'dt_load'=> date("Y-m-d H:i:s")], 
                                                "id = $csv_rows->id");      
               
            }

                        
                        
                   
            
               $unisender_return = Yii::$app->unisender->importContacts($unisender_list_id['list_id'], $contactss, $api_key );
            
              if (@$unisender_return['UnisenderAnswer']->result->total){
                  $u_contacts = new UnisenderContacts();


                   $total = @$unisender_return['UnisenderAnswer']->result->total;
                   $inserted = @$unisender_return['UnisenderAnswer']->result->inserted;
                   $updated = @$unisender_return['UnisenderAnswer']->result->updated;
                   $deleted = @$unisender_return['UnisenderAnswer']->result->deleted;
                   $new_emails = @$unisender_return['UnisenderAnswer']->result->new_emails;
                   $invalid = @$unisender_return['UnisenderAnswer']->result->invalid;
                   $u_contacts->agency_id = $agency_id;
                   $u_contacts->batch_id = $batch_id;
                   $u_contacts->total = $total;
                   $u_contacts->inserted = $inserted;
                   $u_contacts->updated = $updated;
                   $u_contacts->deleted = $deleted;
                   $u_contacts->new_emails = $new_emails;
                   $u_contacts->invalid = $invalid;
                   $u_contacts->list_id = $list_id;

                   //$u_contacts->uid_create = Yii::$app->user->id;
                   $u_contacts->dt_create = date("Y-m-d H:i:s");
                   $u_contacts->save(false);

                   $message .= "<br><br>Операция выполнена успешно!"
                           . "<br>Всего контактов : $total"
                           . "<br>Вставленно контактов : $inserted"
                           . "<br>Обновленно контактов : $updated"
                           . "<br>Удаленно контактов : $deleted"
                           . "<br>Новых  email : $new_emails"
                           . "<br>Некоретных : $invalid"
                           . "";
                   
                   
                   $answer_id = $u_contacts->id;

                   $AgencyBatch = AgencyCsvBatch::find()->where(['id'=>$batch_id, 'agency_id'=>$agency_id])->one();
                   $AgencyBatch->status_id = 2;
                   $AgencyBatch->update(false);
                   
                   
                   
                  

               } else {
                   
                    $message .= @$unisender_return['UnisenderAnswer']->error;
                    $answer_id = 0;
                    
               }
       
                
             
      // }  
        
       $answer = ['id'=>$answer_id,
                   'message'=> $message  ];
        
        return  $answer;
        
          
        
    }
    
    
}