<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "batch_steps".
 *
 * @property integer $id
 * @property integer $batch_id
 * @property string $name
 * @property string $description
 * @property integer $step_count
 * @property string $step_dt
 * @property string $step_bage
 * @property integer $status_id
 * @property string $step_end_dt
 * @property string $finish_message
 * @property integer $visibility
 *
 * @property AgencyCsvBatch $batch
 */
class BatchSteps extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'batch_steps';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['batch_id', 'step_count', 'status_id', 'visibility'], 'integer'],
            [['description', 'step_bage', 'finish_message'], 'string'],
            [['step_dt', 'step_end_dt'], 'safe'],
            [['name'], 'string', 'max' => 450]
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
            'name' => 'Name',
            'description' => 'Description',
            'step_count' => 'Step Count',
            'step_dt' => 'Step Dt',
            'step_bage' => 'Step Bage',
            'status_id' => 'Status ID',
            'step_end_dt' => 'Step End Dt',
            'finish_message' => 'Finish Message',
            'visibility' => 'Visibility',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBatch()
    {
        return $this->hasOne(AgencyCsvBatch::className(), ['id' => 'batch_id']);
    }
    
    
    
    
    private static function _getStepBage($step_count){
        $badge = '';
        switch ($step_count) {
            case 1:
                $badge = '<div class="timeline-badge"><i class="fa fa-floppy-o"></i></div>';
                break;
            case 2:
                $badge = '<div class="timeline-badge warning"><i class="fa fa-credit-card"></i></div>';
                break;
            case 3:
            case 4:
                $badge = '<div class="timeline-badge danger"><i class="fa fa-bomb"></i></div>';
                break;
            case 5:
                $badge = '<div class="timeline-badge warning"><i class="fa fa-list"></i></div>';
                break;
            case 6:
                $badge = '<div class="timeline-badge success"><i class="fa fa-bar-chart"></i></div>';
                break;
            case 101:
                $badge = '<div class="timeline-badge success"><i class="fa fa-bar-chart"></i></div>';
                break;
            default:
               $badge = '<div class="timeline-badge success"><i class="fa fa-desktop"></i></div>';
            }
        return $badge;    
        
    }


    public static function saveStep($batch_id,$name, $description, $step_count, $status_id=0){
        
        
        
        $model = new BatchSteps();
        
         
        $model->batch_id = $batch_id;
        $model->name = $name;
        $model->description = $description;
        $model->step_count = $step_count;
        $model->step_dt = date('Y-m-d H:i:s');
        $model->step_bage = self::_getStepBage($step_count);
        $model->status_id = $status_id;
        
        $model->save();
        
        AgencyCsvBatch::setCurrentStep($batch_id, $step_count);
        
        return 1;
        
        
    }
    
    
    
    
    
    public static function runEmailValidation($batch_id){
        
        $batch = AgencyCsvBatch::find()->where(['id'=>$batch_id])->one();
        $list_title = $batch['campaign_name'];
        $count = $batch['string_count'];
         
        $h = ceil($count/1500);
        
        $step_limit = 25;
        
        $status_id = 1;
        if( AgencySettings::getAgencyParamValue(2, 'suspend_load') == 1){
            $status_id = -3; 
            
            
        } else {
           $res =  CrmUsers::addCrmTasksChain($batch_id);
           $ret =  BatchSteps::saveStep($batch_id, 'Передача данных в CRM', "В очередь поставлена цепочка задач для передачи данных CRM по данной загрузке ", 101, 1);
        }
        
        $res =  CronTask::addTask('ValidateEmail', $batch_id, $step_limit, $list_id=0, 
                                    $validate_statuses='', 
                                    $list_count = 0, 
                                    $status_id);
        
               
        
        
        $ret =  BatchSteps::saveStep($batch_id, 'Запуск валидации', "Приблизительное время валидации в часах : $h ", 2, 1);
        
        $ret =   AgencyCsvBatch::setCurrentStep($batch_id, 2);
        
        return $res;
    }
    
    
    
    
    public static function runSecondCampaign($batch_id, $list_id){
        
        $batch = AgencyCsvBatch::find()->where(['id'=>$batch_id])->one();
        
        $agency_id = 2;
        $list_title = $batch['campaign_name'];
        $confirm_email = 3;
        $campaign_id = 35;
        
        
        
        
        
             $email_tpl =  EmailTemplate::find()->where(['id'=> $batch['email_tpl_id']])->one();

             $email_answer = UnisenderEmail::newEmail($list_title , $list_id,   
                                                    $email_tpl['email_subject'],   $email_tpl['email_text'],  
                                                    $email_tpl['email_from_name'], $email_tpl['email_from_email'],
                                                    $agency_id );
             
             $message = 'batch_id = '.$batch_id.' - ' . implode(',',$email_answer);
             CronLog::saveLog($message);
        
             
             if($email_answer['id']){
                $ret =     BatchSteps::saveStep($batch_id, 'Создание письма повторной валидации', "Создано письмо для спсиска <b>$list_title</b> ", 5, 1);
                
                ///запуск рассылки
             
                    $email = UnisenderEmail::find()->where(['id'=>$email_answer['id']])
                                                ->one();
                        $answ =   UnisenderCampaign::newCampaign($email['name'], $email['id'], 2);
                         $message = 'batch_id = '.$batch_id.' - ' . implode(',',$answ);
                         CronLog::saveLog($message);


                       if($answ['id']){
                        $ret =   BatchSteps::saveStep($batch_id, 'Запуск рассылки повторной валидации', "Рассылка <b>$email->name</b>  запущена", 6, 1);
                      } else {
                       $ret =    BatchSteps::saveStep($batch_id, 'Ошибка запуска рассылки', $answ['message'], 6, 3);
                      }
                
                
                
                
             } else {
                $ret =     BatchSteps::saveStep($batch_id, 'Ошибка создания письма'.$list_id, $email_answer['message'], 5, 3);
             }
             
             
             
        
        
    }


    
    public static function checkExportStatus($batch_id, $mode = 0){
        
        $task_name = 'SendContactsByStatus';
        if($mode==1){
            $task_name = 'SendContactsRepeat';
        }
        
        
        
       $is_still_run =   CronTask::find()->where(['batch_id'=>$batch_id, 
                                                  'task_name'=> $task_name, 
                                                  'status_id'=> 1])->count();
        
       if ($is_still_run == 0){
           
           $camp = AgencyCsvBatch::find()->where(['id'=>$batch_id])->one();
           
           if ($camp['campaign_id']== 35){
           
                    $lists =   CronTask::find()->where(['batch_id'=>$batch_id, 'task_name'=> $task_name, 'status_id'=> 0])->orderBy(['id' => SORT_DESC])->all();

                    foreach ($lists as $list){
                        $email = UnisenderEmail::find()
                                                ->where(['list_id'=>$list->list_id])
                                                ->orderBy(['id' => SORT_DESC])
                                                ->one();
                        $answ =   UnisenderCampaign::newCampaign($email['name'], $email['id'], 2);
                         $message = 'batch_id = '.$batch_id.' - ' . implode(',',$answ);
                         CronLog::saveLog($message);


                       if($answ['id']){
                        $ret =   BatchSteps::saveStep($batch_id, 'Запуск рассылки', "Рассылка <b>$email->name</b>  запущена", 6, 1);
                      } else {
                       $ret =    BatchSteps::saveStep($batch_id, 'Ошибка запуска рассылки', $answ['message'], 6, 3);
                      } 
                    }
                    
                    
           
           }
           
           
            
            $count_unknown =  CsvBatches::find()->where(['batch_id'=>$batch_id, 'validate_status'=>'unknown'])->count();
           
//           
//           $was_run =   CronTask::find()->where(['batch_id'=>$batch_id,
//                                                 'task_name'=> 'SendContactsRepeat'])->count();
//           
//           if ($was_run == 0){
//              $sss=  self::autoPrepareForUnknown($batch_id);
//           }
           
            
            if ($count_unknown > 0){
                $sss=  self::autoPrepareForUnknown($batch_id);
            }
           
           
           
       }
        
        return 1;
    }
    
    
    public  static function testRunPrepare($batch_id){
        $sss=  self::autoPrepareForUnknown($batch_id);
    }
    
    
    
    //// Automatization
    private static function autoPrepareForUnknown($batch_id){
            $agency_id = 2; 
            $list_title = $batch_id.' - Unknown '.' ( auto  '.crypt($batch_id.'rasmuslerdorf'.rand(0, 9999), date('s')).')';
            $validate_status = 'all';
            $ret =   UnisenderList::newListWithConfirmEmail($list_title, $agency_id, $batch_id, $validate_status);
    
             $db = Yii::$app->db;
             $SQL = "SELECT * FROM westbtl.unisender_list
                        where batch_id = $batch_id
                        order by 1 desc
                        limit 1";

             $db = Yii::$app->db;
             $step =  $db->createCommand($SQL)->queryOne();
             
             $list_id = $step['id'];
            
             $db = Yii::$app->db;
                        

             $SQL = "update westbtl.csv_batches
                       set list_id = $list_id,
                           status_id = 3
                           where batch_id = $batch_id
                               and validate_status = 'unknown'
                     ";

               $db = Yii::$app->db;
               $steps =  $db->createCommand($SQL)->execute();
               
               
               
               
               
             /// prepare task for subscribe
             $db = Yii::$app->db;
             $SQL = "SELECT * FROM westbtl.unisender_list
                        where batch_id = $batch_id
                        order by 1 desc
                        limit 1";

            $db = Yii::$app->db;
            $step_sub =  $db->createCommand($SQL)->queryOne();
            
            $batch_rows_count =  CsvBatches::find()->where(['list_id'=>$list_id])->count();
            
            $limit =60;
            $res =  CronTask::addTask('subscribeContactsUnknown', 
                                        $batch_id, 
                                        $limit, 
                                        $step_sub['id'],
                                        '',  //$validate_statuses  
                                        0,   //$list_count = 
                                        1,   //$status_id 
                                        $batch_rows_count //$row_count
                    
                    );
        
            
            
            ///put task for export 
            $limit = 500;
            
            
            $res =  CronTask::addTask('UnisenderExportContacts', 
                                       $batch_id, 
                                       $limit, 
                                       $step_sub['id'],
                                       '',  //$validate_statuses  
                                       0,   //$list_count = 
                                       1,   //$status_id 
                                       $batch_rows_count //$row_count
                    );
                      
            
                                    
            
            //put new task for campaign
         //   $s = BatchSteps::runListCreation($batch_id, 1);
            
            $ret =  BatchSteps::saveStep($batch_id, 'Запуск повтороной валидации', "Потворная валидация.", 2, 1);
            
            return 1;
            
    }


    
    

        //// Automatization end
    
    
    
    
    
    
    
    public static function checkEmailValidation($batch_id){
        
        
       $is_still_run =   CronTask::find()->where(['batch_id'=>$batch_id, 'task_name'=> 'ValidateEmail', 'status_id'=> 1])->count();
        
       if ($is_still_run == 0){ 
           self::runListCreation($batch_id);
           
       }
        
       return 1;
        
    }
    
    public static function reRunValidation($batch_id, $validate_statuses= 'error'){
        
        if($validate_statuses == 'all'){
            
        CsvBatches::updateAll(['status_id'=> 1,
                              ],  "batch_id = $batch_id  ");  
        
        $count =  CsvBatches::find()->where(['batch_id'=>$batch_id])->count();
        
         
        $h = ceil($count/1500);
        $list_id=0;
        $step_limit = 25;
        //addTask($task_name, $batch_id,  $step_limit, $list_id=0, $validate_statuses='', $list_count = 0){
        $res =  CronTask::addTask('ReRunValidation', $batch_id, $step_limit, $list_id, $validate_statuses);
        
        }else {
            
            CsvBatches::updateAll(['status_id'=> 1,
                                  ],  "batch_id = $batch_id and validate_status= 'error' ");  

            $count =  CsvBatches::find()->where(['batch_id'=>$batch_id, 'validate_status'=>'error'])->count();


            $h = ceil($count/1500);

            $step_limit = 25;

            $res =  CronTask::addTask('ReRunValidation', $batch_id, $step_limit);
        }
        
        $ret =  BatchSteps::saveStep($batch_id, 'Запуск валидации (re)', "Приблизительное время валидации в часах : $h ", 2, 1);
        
        $ret =   AgencyCsvBatch::setCurrentStep($batch_id, 2);
        return $count;
    }


    public static function runListCreation($batch_id, $mode=0 ){

        $passed = 'no';
        $unknown = 'no';
        
        $ret=[];
        $passed = BatchSteps::_addListToBatchSimple($batch_id, 'passed', $mode);
        
       //$unknown =     self::_addListToBatchSimple($batch_id, 'unknown');
       
       $ret = [ 'passed'=>$passed,
                'unknown'=>$unknown
               ];
       
       return $ret;
        
        
//        
//        
//        $validate_status = 'unknown';
//        $list_limit = 2000;
//         $batch_rows_count = CsvBatches::find()->where(['batch_id'=>$batch_id])
//                    ->andWhere(['in',  'validate_status', explode(',', $validate_status)])
//                    ->count();
//        
//         
//         $step_count = ceil($batch_rows_count/$list_limit);
//         
//         for 
//         
//          //  $answer = self::newList($list_title, $agency_id, $confirm_email, $campaign_id, $batch_id, $validate_status);
//        
//        
        
            
        
        
    }

   
    


    private static function _addListToBatchSimple($batch_id = 0, $validate_status = 'passed', $mode=0){
      
        $batch = AgencyCsvBatch::find()->where(['id'=>$batch_id])->one();
        
        $agency_id = 2;
        $list_title = $batch['campaign_name'];
        $confirm_email = 3;
        $campaign_id = 35;
        
        
        $list_title = $list_title.'('.$validate_status.') - '.crypt($list_title.'('.$validate_status.') - rasmuslerdorf'.rand(1, 9999), date('s'));
//        if ($mode ==1){
//            $list_title = $list_title.'(Unknown validation) - '.crypt($list_title.'(Unknown validation) - rasmuslerdorf'.rand(1, 9999), date('s'));
//        }
        $answer = UnisenderList::newList($list_title, $agency_id, $confirm_email, $campaign_id, $batch_id, $validate_status);
        
        $message = 'batch_id = '.$batch_id.' - ' . implode(',',$answer);
        CronLog::saveLog($message);
        
        
        $list_id =$answer['id'];
        
        
        if ($list_id!=0){
        
            $ret =  BatchSteps::saveStep($batch_id, 'Создание списка', "Список контактов <b>$list_title</b> создан", 3, 1);
            
            if ($mode ==0){
                $res =  CronTask::addTask('SendContactsByStatus', $batch_id, 200, $list_id, $validate_status);
//            } else {
//                $res =  CronTask::addTask('SendContactsRepeat', $batch_id, 200, $list_id, $validate_status);
            }
           $ret =  BatchSteps::saveStep($batch_id, 'Загрузка', "Началась загрузка контактов по списку  <b>$list_title</b> ", 4, 1);


             $email_tpl =  EmailTemplate::find()->where(['id'=> $batch['email_tpl_id']])->one();

             $email_answer = UnisenderEmail::newEmail($list_title , $list_id,   $email_tpl['email_subject'],   $email_tpl['email_text'],  $email_tpl['email_from_name'], $email_tpl['email_from_email'], $agency_id );
             
             $message = 'batch_id = '.$batch_id.' - ' . implode(',',$email_answer);
             CronLog::saveLog($message);
        
             
             if($email_answer['id']){
                $ret =     BatchSteps::saveStep($batch_id, 'Создание письма', "Создано письмо для спсиска <b>$list_title</b> ", 5, 1);
             } else {
                $ret =     BatchSteps::saveStep($batch_id, 'Ошибка создания письма'.$list_id, $answer['message'], 5, 3);
             } 
               
        
        }  else {
          $ret =    BatchSteps::saveStep($batch_id, 'Ошибка создание списка', $answer['message'], 3, 3);   
        }
        

        
        return  $answer;  
        
    }
    
    
    
    
}
