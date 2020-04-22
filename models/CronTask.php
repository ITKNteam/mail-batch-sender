<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
/**
 * This is the model class for table "cron_task".
 *
 * @property integer $id
 * @property string $task_name
 * @property integer $batch_id
 * @property integer $batch_rows_count
 * @property integer $step_limit
 * @property integer $steps_count
 * @property integer $current_step
 * @property string $last_dt
 * @property integer $status_id
 *
 * @property AgencyCsvBatch $batch
 */
class CronTask extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cron_task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['batch_id', 'batch_rows_count', 'step_limit', 'steps_count', 'current_step', 'status_id', 'list_id'], 'integer'],
            [['last_dt','validate_statuses'], 'safe'],
            [['task_name'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_name' => 'Название задания',
            'batch_id' => 'Пачка',
            'batch_rows_count' => 'Кол-во строк в пачке',
            'step_limit' => 'Step Limit',
            'steps_count' => 'Steps Count',
            'current_step' => 'Current Step',
            'last_dt' => 'Дата обновления',
            'status_id' => 'Статус задания',
            'validate_statuses'=>'Статусы валидации email '
        ];
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
     * @return \app\models\QueryModels\CronTaskQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\QueryModels\CronTaskQuery(get_called_class());
    }
    
    
    
    
    public static function addTask($task_name, 
                                    $batch_id,  
                                    $step_limit, 
                                    $list_id=0, 
                                    $validate_statuses='', 
                                    $list_count = 0, 
                                    $status_id = 1, 
                                    $row_count = 0
                                    ){
        
        /* status :
         * -4 - pending
         * -3 - paused 
         *  0 - done
         *  1 - active 
         * 
         *  
         */
        
        
        $batch = AgencyCsvBatch::findOne(['id'=>$batch_id]);
        $batch_rows_count = $batch['string_count'];
        
//        if($task_name == 'SubscribeContacts2')
//            $step_limit = $step_limit*50;

        $step_count = ceil($batch_rows_count/ $step_limit);
        
        if($task_name == 'SubscribeContacts2'){
            if (self::checkActiveTask()){   
                    $status_id = -4;
            }     
            
            $batch_rows_count = CsvBatches::find()->where(['batch_id'=>$batch_id])
                    ->andWhere(['like',  'email', ['@'] ])
                    ->count();
            $step_count = ceil($batch_rows_count/$step_limit);
         
        } 
        if($task_name == 'SubscribeByStatus'){
            if (self::checkActiveTask()){   
                    $status_id = -4;
            }     
            $batch_rows_count = CsvBatches::find()->where(['batch_id'=>$batch_id,'status_id'=> 3 ])
                    ->andWhere(['in',  'validate_event', explode(',', $validate_statuses)])
                    ->count();
            $step_count = ceil($batch_rows_count/$step_limit);
         
        } 
        if($task_name == 'SendContactsByStatus'){
            if (self::checkActiveTask()){   
                    $status_id = -4;
            }     
            $batch_rows_count = CsvBatches::find()->where(['batch_id'=>$batch_id,'status_id'=> 1 ])
                    ->andWhere(['in',  'validate_status', explode(',', $validate_statuses)])
                    ->count();
            $step_count = ceil($batch_rows_count/$step_limit);
         
        }
        if($task_name == 'SendContactsRepeat'){
            if (self::checkActiveTask()){   
                    $status_id = -4;
            }     
            //$batch_rows_count = CsvBatches::find()->where(['batch_id'=>$batch_id,'status_id'=> 8 ])
            $batch_rows_count = CsvBatches::find()->where(['batch_id'=>$batch_id ])
                    ->andWhere(['in',  'validate_status', 'passed'])
                    ->count();
            $step_count = ceil($batch_rows_count/$step_limit);
         
        }
        
        if($task_name == 'ReRunValidation'){
            
            
             if (self::checkActiveTask('BulkValidator') && $status_id != -3){   
                    $status_id = -4;
            } 
            
            
            $step_limit = 25;
            $task_name = 'ValidateEmail';
            if ($validate_statuses == 'all'){
                $batch_rows_count =  CsvBatches::find()->where(['batch_id'=>$batch_id])->count();
            } else {
                $batch_rows_count =  CsvBatches::find()->where(['batch_id'=>$batch_id, 'validate_status'=>'error'])->count();
            }
               
        
            
            $step_count = ceil($batch_rows_count/$step_limit);
         
        }
        if($task_name == 'subscribeContactsUnknown'){
            if (self::checkActiveTask()){   
                    $status_id = -4;
            }     
            
            //$batch_rows_count = Agency::getAgencyBatchSlice($batch_id);
            $batch_rows_count =  CsvBatches::find()->where(['batch_id'=>$batch_id, 'status_id'=>3])->count();
        
            if ($row_count){
                $step_count = ceil($row_count/$step_limit);
            } else {
                $step_count = ceil($batch_rows_count/$step_limit);
            }
            
         
        }
        
    if($task_name == 'UnisenderExportContacts'){
        if (self::checkActiveTask()){   
                    $status_id = -4;
            }     
            
            //$batch_rows_count = Agency::getAgencyBatchSlice($batch_id, 'export');
            $batch_rows_count =  CsvBatches::find()->where(['batch_id'=>$batch_id, 'status_id'=>7])->count();
            
            //$step_limit = ceil($step_limit/1000);
            
           // $step_count = ceil($batch_rows_count/($step_limit*1000));
            if ($row_count){
                $batch_rows_count = $row_count;
                $step_count = ceil($row_count/$step_limit);
            } else {
                $step_count = ceil($batch_rows_count/$step_limit);
            }
            
            self::CRMMassiveLoad($batch_id);
         
        }
        
        
        $task = self::findOne(['task_name'=>$task_name, 'batch_id'=>$batch_id, 'list_id'=>$list_id ]);
        if (!$task){
           $task = new CronTask();
           $task->task_name = $task_name;
            $task->batch_id = $batch_id;
            $task->batch_rows_count = $batch_rows_count;
            $task->step_limit = $step_limit;
            $task->steps_count = $step_count;
            $task->current_step = 1;
            $task->status_id = $status_id;
            $task->list_id = $list_id;
            $task->last_dt = date('Y-m-d H:i:s');
            $task->validate_statuses = $validate_statuses;

            $task->save();
        }  else {
            $task->task_name = $task_name;
            $task->batch_id = $batch_id;
            $task->batch_rows_count = $batch_rows_count;
            $task->step_limit = $step_limit;
            $task->steps_count = $step_count;
            $task->current_step = 1;
            $task->status_id = $status_id;
            $task->list_id = $list_id;
            $task->last_dt = date('Y-m-d H:i:s');
            $task->validate_statuses = $validate_statuses;

            $task->update();
        }
        
        
        
        
        return 1;
    }
    
    
    
    
    
    
    private static function _changeStatus($task_id, $staus_id){
        $condition = "id = $task_id";
        $ret = static::updateAll(['status_id'=>$staus_id], $condition);
        //запускаем триггерные операции
             
            
        
        
        return $ret;
        
    }
    
    public static function startTask($task_id){
        $ret = static::_changeStatus($task_id, 1);
        return $ret;
        
    }
    
    public static function stopTask($task_id){
        $ret = static::_changeStatus($task_id, 0);
             
             $task = CronTask::findOne(['id'=>$task_id]);
             if(in_array($task['task_name'], [ 'SubscribeContacts',
                                                            'SubscribeContacts2',
                                                             'SubscribeByStatus',
                                                             'SendContactsByStatus',
                                                             'SendContactsRepeat',
                                                             'subscribeContactsUnknown',
                                                              'UnisenderExportContacts',
                                                               'RegisterBasicMassive'  ]) )
                {
                $task_z =   static::find()->where(['status_id'=>-4,
                                          'task_name'=>[ 'SubscribeContacts',
                                                            'SubscribeContacts2',
                                                             'SubscribeByStatus',
                                                             'SendContactsByStatus',
                                                             'SendContactsRepeat',
                                                             'subscribeContactsUnknown',
                                                              'UnisenderExportContacts',
                                                              'RegisterBasicMassive'
                                                              ]])
                         ->orderBy(['id'=>SORT_ASC])
                         ->one();
                if ($task_z){
                    $task_z->status_id = 1; 
                    $task_z->update();
                }                 
            }                 
            
            if(in_array($task['task_name'], [ 'checkEmailAvability',
                                                'RegisterBasicMassive',
                                                'RegisterBasic',
                                                 'UpdateProfile',
                                                 'FillSmokingHabbits', ]) )
                {
                $task_z =   static::find()->where(['status_id'=>-4,
                                                    'task_name'=>[ 'checkEmailAvability',
                                                                   'RegisterBasicMassive',
                                                                   'RegisterBasic',
                                                                   'UpdateProfile',
                                                                    'FillSmokingHabbits',  ]])
                         ->orderBy(['id'=>SORT_ASC])
                         ->one();
                if ($task_z){
                    $task_z->status_id = 1; 
                    $task_z->update();
                }                 
            }                 
                  
             
             if ($task['task_name']=='SendContactsByStatus'){
             
                 $task_s = CronTask::findOne(['id'=>$task_id]);
                 
                 $ret = BatchSteps::checkExportStatus($task_s['batch_id']);
                 $message = 'stopTask - task_id = '.$task_id.' - SendContactsByStatus/ ret'.$ret; 
                 $ret = CronLog::saveLog($message);
             }
//             if ($task['task_name']=='SendContactsRepeat'){
//                 $mode = 1;
//                 $ret = BatchSteps::checkExportStatus($task['batch_id'], $mode);
//                 $message = 'stopTask - task_id = '.$task_id.' - SendContactsRepeat/ ret'.$ret; 
//                 $ret = CronLog::saveLog($message);
//             }
             if ($task['task_name']=='subscribeContactsUnknown'){
                 $mode = 1;
                 $ret = BatchSteps::runSecondCampaign($task['batch_id'],  $task['list_id']);
                 $message = 'stopTask - task_id = '.$task_id.' - subscribeContactsUnknown/ ret'.$ret; 
                 $ret = CronLog::saveLog($message);
             }
             if ($task['task_name']=='ValidateEmail'){
                 

                    $task_l =   static::find()->where(['status_id'=>-4,
                                               'task_name'=>[ 'ValidateEmail' ]])
                              ->orderBy(['id'=>SORT_DESC])
                              ->one();
                    if ($task_l){
                        $task_l->status_id = 1; 
                        $task_l->update();
                    }     

                      $ret =  BatchSteps::checkEmailValidation($task['batch_id']);
                      $message = 'stopTask - task_id = '.$task_id.' - ValidateEmail / ret'.$ret; 
                      $ret = CronLog::saveLog($message);
                 
             }
           
        
        return $ret;
        
    }
    
    
    
    public static function CRMMassiveLoad($batch_id){
            $count =  CrmUsers::find()->where(['batch_id'=>$batch_id])->count();
            $del = CrmUsers::deleteAll(['batch_id'=>$batch_id]);
            $data_insert = CrmUsers::insertUsers2($batch_id);
            $res =  CronTask::addTask('RegisterBasicMassive', 
                                      $batch_id, 
                                        1000, 
                                        0, 
                                        0, 
                                        0, 
                                        1, $count);
            
            return $res;
        
    }
            
            
            
    
    public static function getPercentContacts($batch_id, $list_id, $task_name=''){
        $staus_id = 1;
        $task = static::find()->where(['batch_id'=>$batch_id, 'list_id'=>$list_id, 'status_id'=>$staus_id, 'task_name'=>$task_name])->one();
        if (!$task)
            return 100;
        
        $percent =  ceil( ($task->current_step/$task->steps_count)*100);
        
        if ($percent >= 100)
            return 100;
        
        return $percent;
    }


    public static function work(){
        
        //run Watchdog
        self::_watchDog();
        
        
        $tasks = static::find()->where(['status_id'=>1])->limit(100)
                ->orderBy(['batch_id' => SORT_ASC])->all();
        
        foreach ($tasks as $task){
            switch ($task['task_name']) {
            case  'checkEmailAvability'  : self::_taskCheckEmailAvability($task['id'], $task['batch_id'], $task['current_step'], $task['steps_count']);
                    break;
            case  'RegisterBasic'  : self::_taskRegisterBasic($task['id'], $task['batch_id'], $task['current_step'], $task['steps_count']);
                    break;
            case  'UpdateProfile'  : self::_taskUpdateProfile($task['id'], $task['batch_id'], $task['current_step'], $task['steps_count']);
                    break;
            case  'FillSmokingHabbits'  : self::_taskFillSmokingHabbits($task['id'], $task['batch_id'], $task['current_step'], $task['steps_count']);
                    break;
            case  'RegisterBasicMassive'  : self::_taskRegisterBasicMassive($task['id'], $task['batch_id'], $task['current_step'], $task['steps_count'],  $task['step_limit']);
                    break;
            case  'insertUsers'  : self::_taskInsertUsers($task['id'], $task['batch_id'], $task['current_step'], $task['steps_count'], $task['step_limit']);
                    break;
            case  'insertUsers2'  : self::_taskInsertUsers2($task['id'], $task['batch_id'], $task['current_step'], $task['steps_count'], $task['step_limit']);
                    break;
            case  'SendContacts'  : self::_taskSendContacts($task['id'], $task['batch_id'], $task['current_step'], $task['steps_count'], $task['step_limit'], $task['list_id']);
                    break;
            case  'SubscribeContacts'  : self::_taskSubscribeContacts($task['id'], $task['batch_id'], $task['current_step'], $task['steps_count'], $task['step_limit'], $task['list_id']);
                    break;
            case  'SubscribeContacts2'  : self::_taskSubscribeContacts2($task['id'], $task['batch_id'], $task['current_step'], $task['steps_count'], $task['step_limit'], $task['list_id']);
                    break;
            case  'ValidateEmail'  : self::_taskValidateEmail($task['id'], $task['batch_id'], $task['current_step'], $task['steps_count'], $task['step_limit'], $task['list_id']);
                    break;
            case  'SubscribeByStatus' : self::_taskSubscribeByStatus($task['id'], $task['batch_id'], $task['current_step'], $task['steps_count'], $task['step_limit'], $task['list_id'], $task['validate_statuses']);
                    break;
            case  'SendContactsByStatus' : self::_taskSendContactsByStatus($task['id'], $task['batch_id'], $task['current_step'], $task['steps_count'], $task['step_limit'], $task['list_id'], $task['validate_statuses']);
                    break;
            case  'SendContactsRepeat' : self::_taskSendContactsRepeat($task['id'], $task['batch_id'], $task['current_step'], $task['steps_count'], $task['step_limit'], $task['list_id'], $task['validate_statuses']);
                    break;
            
            case  'subscribeContactsUnknown' : self::_taskSubscribeContactsUnknown($task['id'], $task['batch_id'], $task['current_step'], $task['steps_count'], $task['step_limit'], $task['list_id']);
                    break;
            case  'UnisenderExportContacts' : self::_taskUnisenderExportContacts($task['id'], $task['batch_id'], $task['current_step'], $task['steps_count'], $task['step_limit'], $task['list_id']);
                    break;
            };
            
           
        }
        
        return 'ok';
    }
    
    
    
    
    private static function _watchDog(){
      
        $hours = date('H');
 
         $SQL = "SELECT 
                    DATE_FORMAT(TIMEDIFF(now(), max(dt_create)),'%i') as last
                    FROM westbtl.unisender_subscribe";
        
        
             $db = Yii::$app->db;
             $step =  $db->createCommand($SQL)->queryOne();
             $last = $step['last'];  
             $minutes = 7;
             
             if ($last === $minutes){
                 
                 self::_subsccribeAlert($minutes);
             }
 
        if ($hours == 1){
            
            $SQL_C  = "SELECT  count(*) as c FROM westbtl.csv_batches
                            where 
                            status_id != 3
                             and substr(sub_message, 1, 55)  in (
                            'API access error',
                            'An error occured: OB20142205-02 [Api call limit exceede',
                            'An error occured: OB20142205-01 [Api call limit exceede',
                            'An error occured: DG120429-01 [DataObject insert failed')
                            ";
               $db = Yii::$app->db;
             $step =  $db->createCommand($SQL_C)->queryOne();
             $c = $step['c']; 
             
             if ($c > 0){
             
                    self::_UpdateLimitHundred($c);
             }
        }
    }

    
    
    private static function _subsccribeAlert($minutes){
        $SQL = "SELECT 
                    DATE_FORMAT(TIMEDIFF(now(), max(dt_create)),'%i') as last
                    FROM westbtl.unisender_subscribe";
        
        
             $db = Yii::$app->db;
             $step =  $db->createCommand($SQL)->queryOne();
             
             
             
            $users = User::findAll(['id'=>[11, 23, 15]]); 
             
            foreach ($users as $user ){
              $body = '<h3>Метод Subscribe не вызывался в тчении '.$minutes.' минут </h3>'
                        . '<br>Возможно, всё хорошо и все задачи выполнены.'
                        . '<br>Но всё же, стоит взглянуть на очередь задач в системе.'
                        . '<br><br><b>Ссылка к системным задачам</b>: '.Url::base(1).'/sys/all-cron-tasks';
              
                 $mailer = Yii::$app->get('mail');
                 $message = $mailer->compose()
                 ->setFrom('support@simpsons.ru')
                 ->setTo($user->email)
                 ->setHtmlBody($body)       
                 ->setSubject('Houston, we have a problems!')
                 ->send();
            }    
                 
        
    }

    

    private static function _UpdateLimitHundred($row_count){
        
        $batch_id = -3;
        
        
        $agency_id = 2; 
        $list_title = $batch_id.' - LIMIT 100 '.' ( auto  '.crypt($batch_id.'rasmuslerdorf'.rand(0, 9999), date('s')).')';
        $validate_status = 'all';
        $ret =   UnisenderList::newListWithConfirmEmail($list_title, $agency_id, $batch_id, $validate_status);
        
        $list_id = $ret['id'];
        
        
        $SQL = "update  westbtl.csv_batches
                set status_id = 3,
                 list_id = $list_id
                
                where substr(sub_message, 1, 55)  in (
                'API access error',
                'An error occured: OB20142205-02 [Api call limit exceede',
                'An error occured: OB20142205-01 [Api call limit exceede',
                'An error occured: DG120429-01 [DataObject insert failed')";
        
    
        $db = Yii::$app->db;
        $steps =  $db->createCommand($SQL)->execute();
       
      
//         CronTask::addTask(
//        $task_name, 
//        $batch_id,  
//        $step_limit, 
//        $list_id=0, 
//        $validate_statuses='', 
//        $list_count = 0, 
//        $status_id = 1, 
//        $row_count = 0)
     
       
        $limit =60;
        $res =  CronTask::addTask('subscribeContactsUnknown', $batch_id, $limit, $list_id, '', 0, 1, $row_count);



        ///put task for export 
        $limit = 1000;
        $res =  CronTask::addTask('UnisenderExportContacts', $batch_id, $limit, $list_id, '', 0, 1, $row_count);
       
       
       return true;
        
    }
    
    
    
    
    



    public static function checkActiveTask($task_type='Unisender', $status_id=1, $task_name=''){
        
         $ret = 0;   
            
         
        if ($task_type == 'Unisender' ){ 
         $active = static::find()->where(['status_id'=>$status_id,
                                          'task_name'=>[ 'SubscribeContacts',
                                                            'SubscribeContacts2',
                                                             'SubscribeByStatus',
                                                             'SendContactsByStatus',
                                                             'SendContactsRepeat',
                                                             'subscribeContactsUnknown',
                                                              'UnisenderExportContacts'  ]])
                                  ->count();   
        }elseif ($task_type == 'BulkValidator' ) {
              $active = static::find()->where(['status_id'=>$status_id,
                                          'task_name'=>[ 'ValidateEmail',
                                                            ]])
                                  ->count();   
        } else {
            $active = static::find()->where(['status_id'=>$status_id,
                                          'task_name'=>$task_name])
                                  ->count();
        } 
        
         
         if ($active> 0){
             $ret = 1;   
         }
            
         return $ret;
     
    }


    
    private static function checkTask($task_id,  $step, $step_count){
        
         
         $condition = "id = $task_id";
         static::updateAll(['current_step'=> $step+1 ,
                            'last_dt'=>date('Y-m-d H:i:s')
                        ], $condition);
         
         if ($step >= $step_count){
             
             
             static::stopTask ($task_id);
         }
    }

    
    
  //TO DO переключить на insertUsers2
   private static function _taskInsertUsers($task_id, $batch_id, $step, $step_count, $limit){
         $res = CrmUsers::insertUsers($batch_id,  $step, $limit);
         static::checkTask($task_id, $step, $step_count) ;
         return true;
        
    }
    
  //TO DO переключить на insertUsers2
   private static function _taskInsertUsers2($task_id, $batch_id, $step, $step_count, $limit){
         $res = CrmUsers::insertUsers2($batch_id,  $step, $limit);
         static::checkTask($task_id, $step, $step_count) ;
         return true;
        
    }
    
    
    //////////повторная валидация
    
  //Загрузка контактов во все аккаунты Unisender
   private static function _taskSubscribeContactsUnknown($task_id, $batch_id, $step, $step_count, $limit, $list_id){
         $res = AceptBatch::subscribeContactsUnknown($batch_id, $list_id, $limit);
         static::checkTask($task_id, $step, $step_count) ;
         return true;
        
    }
    
    
    
       
  //Экспорт контатков из Unisender
   private static function _taskUnisenderExportContacts($task_id, $batch_id, $step, $step_count, $limit, $list_id){
         $res = UnisenderExportContacts::export2($list_id, $limit, $step);
         static::checkTask($task_id, $step, $step_count) ;
         return true;
        
    }
  
    
    
    
    private static function _taskSendContactsRepeat($task_id, $batch_id, $step, $step_count, $limit, $list_id, $statuses){
        
            $mode = 1;
            $res = AceptBatch::sendContactsSimple($list_id, $batch_id, $step, $limit, $statuses, $mode);
       
             $message = 'stopTask - task_id = '.$task_id.' - sendContactsSimple / ret'.$res['message']; 
             $ret = CronLog::saveLog($message);
                
            
         //$res = CrmUsers::insertUsers($batch_id,  $step, $limit);
         static::checkTask($task_id, $step, $step_count) ;
         return true;
        
    }
    
    private static function _taskSendContactsUnknown($task_id, $batch_id, $step, $step_count, $limit, $list_id, $statuses){
        
            $mode = 1;
            $res = AceptBatch::sendContactsUnknown($list_id, $batch_id, $step, $limit);
       
             $message = 'stopTask - task_id = '.$task_id.' - ContactsUnknow / ret'.$res['message']; 
             $ret = CronLog::saveLog($message);
                
            
         //$res = CrmUsers::insertUsers($batch_id,  $step, $limit);
         static::checkTask($task_id, $step, $step_count) ;
         return true;
        
    }
    
    
    //////////////КОНЕЦ повторная валидация
    
    
   //TO DO переключить на subscribeContacts2 
    private static function _taskSubscribeContacts($task_id, $batch_id, $step, $step_count, $limit, $list_id){
            $res = AceptBatch::subscribeContacts($list_id, $batch_id, $step, $limit);
       
         //$res = CrmUsers::insertUsers($batch_id,  $step, $limit);
         static::checkTask($task_id, $step, $step_count) ;
         return true;
        
    }
   //TO DO переключить на subscribeContacts2 
    private static function _taskSubscribeContacts2($task_id, $batch_id, $step, $step_count, $limit, $list_id){
            $res = AceptBatch::subscribeContacts2($list_id, $batch_id, $step, $limit);
       
         //$res = CrmUsers::insertUsers($batch_id,  $step, $limit);
         static::checkTask($task_id, $step, $step_count) ;
         return true;
        
    }
  
    
   private static function _taskSendContacts($task_id, $batch_id, $step, $step_count, $limit, $list_id){
            $res = AceptBatch::sendContacts($list_id, $batch_id, $step, $limit);
       
         //$res = CrmUsers::insertUsers($batch_id,  $step, $limit);
         static::checkTask($task_id, $step, $step_count) ;
         return true;
        
    }
   
    
    private static function _taskValidateEmail($task_id, $batch_id, $step, $step_count, $limit, $list_id){
            $res = AceptBatch::validateEmail($batch_id, $limit);
       
         //$res = CrmUsers::insertUsers($batch_id,  $step, $limit);
         static::checkTask($task_id, $step, $step_count) ;
         return true;
        
    }
    
    private static function _taskSubscribeByStatus($task_id, $batch_id, $step, $step_count, $limit, $list_id, $statuses){
            $res = AceptBatch::subscribeContactsStatusSegment($list_id, $batch_id, $step, $limit, $statuses);
       
         //$res = CrmUsers::insertUsers($batch_id,  $step, $limit);
         static::checkTask($task_id, $step, $step_count) ;
         return true;
        
    }
    
    
    private static function _taskSendContactsByStatus($task_id, $batch_id, $step, $step_count, $limit, $list_id, $statuses){
            $res = AceptBatch::sendContactsSimple($list_id, $batch_id, $step, $limit, $statuses);
       
             $message = 'stopTask - task_id = '.$task_id.' - sendContactsSimple / ret'.$res['message']; 
             $ret = CronLog::saveLog($message);
                
            
         //$res = CrmUsers::insertUsers($batch_id,  $step, $limit);
         static::checkTask($task_id, $step, $step_count) ;
         return true;
        
    }
    
    
   
    
    
      //передача данных в CRM
     private static function _taskCheckEmailAvability($task_id, $batch_id, $step, $step_count){
         $res = CrmUsers::checkEmailAvability($batch_id,  $step);
         static::checkTask($task_id, $step, $step_count) ;
         return true;
        
    }
   
      //передача данных в CRM
     private static function _taskRegisterBasic($task_id, $batch_id, $step, $step_count){
         $res = CrmUsers::RegisterBasic($batch_id,  $step);
         static::checkTask($task_id, $step, $step_count) ;
         return true;
        
    }
      //передача данных в CRM
     private static function _taskUpdateProfile($task_id, $batch_id, $step, $step_count){
         $res = CrmUsers::UpdateProfile($batch_id,  $step);
         static::checkTask($task_id, $step, $step_count) ;
         return true;
        
    }
      //передача данных в CRM
     private static function _taskFillSmokingHabbits($task_id, $batch_id, $step, $step_count){
         $res = CrmUsers::FillSmokingHabbits($batch_id,  $step);
         static::checkTask($task_id, $step, $step_count) ;
         return true;
        
    }
      //передача данных в CRM
     private static function _taskRegisterBasicMassive($task_id, $batch_id, $step, $step_count, $limit){
         $res = CrmUsers::RegisterBasicMassive($batch_id,  $step, $limit);
         static::checkTask($task_id, $step, $step_count) ;
         return true;
        
    }
   
    
}
