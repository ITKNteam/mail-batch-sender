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




class CsvUpload extends Model
{
   /**
     * @var UploadedFile file attribute
     */
    public $file;
    public $agency_id;
    public $delimer;
    public $campaign_id;
    public $campaign_name;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => ['txt', 'csv'], 'maxSize' => 1024 * 1024 * 90],
               [['agency_id'], 'safe'],
        ];
    }
   
    
    
    public function attributeLabels()
    {
        return [
            
            'agency_id' => 'Агентство',
            'delimer'=>'Разделитель в CSV файле',
            'campaign_id'=>'Промо кампания',
            'campaign_name'=>'Название'
            
        ];
    }
    
    
    
    
    
    
    public static function saveArrFile($model, $field, $group_id, $agency_id = 2, $delimer = ','){
        
            if(is_object($model->$field))
            {
               
                
               
                $path = UPLOAD_DIR.'file/';
                $file_name = hash('crc32', $model->$field->baseName.date("Y-m-d H:i:s")) . '.' . $model->$field->extension;
                
                  $full_path = $path . $file_name;
                if(!is_dir($path)){
                    @mkdir($path, 0755, true);
                }
                touch($path.'/index.htm');
                
                $model->$field->saveAs($full_path);   
                
                $batch = new AgencyCsvBatch();
                
                $batch->agency_id = $agency_id;
                $batch->user_id = Yii::$app->user->id;
                $batch->file_name = $file_name;
                $batch->batch_date = date("Y-m-d H:i:s");
                $batch->save();
                $batch_id = $batch->id;




              $RowsTemplate = (new \yii\db\Query())
                ->select(['agency_settings.id', 'sys_parametr.name'])
                ->from('agency_settings')
                ->join('LEFT JOIN', 'sys_parametr', 'sys_parametr.id = agency_settings.sys_parametr_id')
                ->where(['sys_parametr.group_id' => $group_id])
                ->andWhere(['agency_id' => $agency_id])
                ->orderBy('row_order')      
                ->all();

               $fp = fopen($full_path,'r') or die("can't open file");
                $handle = fopen('php://memory', 'w+');
                           // fwrite($handle, iconv('CP1251', 'UTF-8', file_get_contents($full_path)));
                            fwrite($handle,  file_get_contents($full_path));
                            rewind($handle);

                            $i=0;
                            $row_num = 0;
                            while($csv_line = fgetcsv($handle,0, $delimer)) {
                                //$data[] = $csv_line;
                                ++$row_num;    
                                 for ($k = 0, $j = count($RowsTemplate); $k < $j; $k++) {
                               //  for ($k = 0, $j = 17; $k < $j; $k++) {
                                   if ($csv_line[$k]==null) {
                                       $v_val ='';    
                                    }else {
                                       $v_val = $csv_line[$k];
                                    }

                                        $data[] = ['batch_id'=>$batch_id,
                                                  'param_id'=>$RowsTemplate[$i]['id'],
                                                  'row_order'=>$row_num,
                                                  'value'=>$v_val

                                        ]; 
                                       ++$i;        

                                }
                                $i=0;
                            }
                            $db = Yii::$app->db;
                            $sql = $db->queryBuilder->batchInsert(BatchData::tableName(), [
                                        'batch_id',
                                        'agency_parametr_id',
                                        'string_order',
                                        'value',
                                        ], $data);
                            $db->createCommand($sql )->execute();
                              fclose($handle);
                              
                              
                  $batch_u = AgencyCsvBatch::find()->where(['id'=>$batch_id])->one();    
                  $batch_u->string_count = $row_num;
                  $batch_u->status_id = 1;
                  $batch_u->sys_group_id = $group_id;
                  $batch_u->update(false);        
                
            }
            return $batch_id;
     }
        
        
    

    

    public static function saveHugeFile($model, $field, $agency_id = 2, $dilimer = ',', $campaign_id=null)
        {

         ini_set('memory_limit', '512M');
         ini_set('max_execution_time', '180');
            if(is_object($model->$field))
            {
                $path = UPLOAD_DIR.'file/';
               
                
                $file_name = hash('crc32', $model->$field->baseName.date("Y-m-d H:i:s")) . '.' . $model->$field->extension;
                $csv_file_name = $file_name;
                $batch_id = 0;
                $user_id  =Yii::$app->user->id; 
                $status_id = 1;
                $dt_load =  date("Y-m-d H:i:s");
                $f_name = '';
                $l_name = '';
                $p_name = '';
                $email = '';
                $phone = '';
                $age = '';
                $gender = '';
                $priority_mark1 = '';
                $priority_mark2 = '';
                $hostess_id = '';
                $activity_dt = '';
                $activity_loc = '';
                $test_res  = '';
                $test_id  = '';
                
                $activity_type  = '';
                $activity_id  = '';
                $hostess_name  = '';
                $advanced_data  = '';
                
                $validate_status  = '';
                $validate_detail  = '';
                
           
                
                
                $full_path = $path . $file_name;
                if(!is_dir($path)){
                    @mkdir($path, 0755, true);
                }
                touch($path.'/index.htm');
                
                $model->$field->saveAs($full_path);   
                $fp = fopen($full_path,'r') or die("can't open file");
                
                $batch = new AgencyCsvBatch();
                
                $batch->agency_id = $agency_id;
                $batch->user_id = Yii::$app->user->id;
                $batch->file_name = $file_name;
                $batch->batch_date = date("Y-m-d H:i:s");
                $batch->campaign_id = $campaign_id;
                $batch->save();
                $batch_id = $batch->id;
                
                
                $v_id = null;
                $i=0;
                $z=0;
               

                $codes = [];
                $handle = fopen('php://memory', 'w+');
               // fwrite($handle, iconv('CP1251', 'UTF-8', file_get_contents($full_path)));
                fwrite($handle,  file_get_contents($full_path));
                rewind($handle);
                 $row_num = 0;
                while($csv_line = fgetcsv($handle,0, $dilimer)) {
                 ++$row_num;   
                 ++$z;
                    for ($k = 0, $j = count($csv_line); $k < $j; $k++) {
                        if ($csv_line[$k]==null) {
                           $v_val ='';    
                        }else {
                           $v_val = $csv_line[$k];
                        }
                        
                            $i++;        
                            if ($i == 1){ $f_name  = $v_val; }
                            if ($i == 2){ $l_name  = $v_val; }
                            if ($i == 3){ $email  = $v_val; }
                            if ($i == 4){  $validate_status = $v_val;}
                            if ($i == 5){ $validate_detail  = $v_val; }
                          
                        
                       }
                     $i=0;
                  
                  
                  
                  
                  $data[] = [
                            $batch_id,
                            $user_id,
                            $status_id,
                            $dt_load,
                            $f_name,
                            $l_name,
                            $email,
                            $validate_status,
                            $validate_detail
                          ] ;

                  if ($z >= 1000){
                      $z = 0;
                       
                        $db = Yii::$app->db;
                        $sql = $db->queryBuilder->batchInsert(CsvBatches::tableName(), [
                                    'batch_id',
                                    'user_id',
                                    'status_id',
                                    'dt_load',
                                    'f_name',
                                    'l_name',
                                    'email',
                                    'validate_status',
                                    'validate_details',
                        

                            ], $data);
                        $db->createCommand($sql)->execute();
                        unset($data);  
                
                  }
                  
                }
                
                 $db = Yii::$app->db;
                        $sql = $db->queryBuilder->batchInsert(CsvBatches::tableName(), [
                                    'batch_id',
                                    'user_id',
                                    'status_id',
                                    'dt_load',
                                    'f_name',
                                    'l_name',
                                    'email',
                                    'validate_status',
                                    'validate_details',
                        

                            ], $data);
                        $db->createCommand($sql)->execute();
                        unset($data); 
                
               
 
               fclose($handle);
                  
               $batch_u = AgencyCsvBatch::find()->where(['id'=>$batch_id])->one();    
                  $batch_u->string_count = $row_num;
                  $batch_u->status_id = 1;
                  $batch_u->sys_group_id = 3;
                  $batch_u->update(false);   
                
                return $batch_id;
            }
        }
        
        
      
        
        
    public static function saveSimpleHugeFile($model, $field, $agency_id = 2, $dilimer = ',', $campaign_id=null, $campaign_name='')
        {

         ini_set('memory_limit', '512M');
         ini_set('max_execution_time', '180');
            if(is_object($model->$field))
            {
                $path = UPLOAD_DIR.'file/';
               
                
                $file_name = hash('crc32', $model->$field->baseName.date("Y-m-d H:i:s")) . '.' . $model->$field->extension;
                $csv_file_name = $file_name;
                $batch_id = 0;
                $user_id  =Yii::$app->user->id; 
                $status_id = 1;
                $dt_load =  date("Y-m-d H:i:s");
                $f_name = '';
                $l_name = '';
                $p_name = '';
                $email = '';
                $phone = '';
                $age = '';
                $gender = '';
                $priority_mark1 = '';
                $priority_mark2 = '';
                $hostess_id = '';
                $activity_dt = '';
                $activity_loc = '';
                $test_res  = '';
                $test_id  = '';
                
                $activity_type  = '';
                $activity_id  = '';
                $hostess_name  = '';
                $advanced_data  = '';
                
           
                
                
                $full_path = $path . $file_name;
                if(!is_dir($path)){
                    @mkdir($path, 0755, true);
                }
                touch($path.'/index.htm');
                
                $model->$field->saveAs($full_path);   
                $fp = fopen($full_path,'r') or die("can't open file");
                
                $batch = new AgencyCsvBatch();
                
                $batch->agency_id = $agency_id;
                $batch->user_id = Yii::$app->user->id;
                $batch->file_name = $file_name;
                $batch->batch_date = date("Y-m-d H:i:s");
                $batch->campaign_id = $campaign_id;
                $batch->campaign_name = $campaign_name;
                $batch->save();
                $batch_id = $batch->id;
                
                
                $v_id = null;
                $i=0;
                $z=0;
               

                $codes = [];
                $handle = fopen('php://memory', 'w+');
               // fwrite($handle, iconv('CP1251', 'UTF-8', file_get_contents($full_path)));
                fwrite($handle,  file_get_contents($full_path));
                rewind($handle);
                 $row_num = 0;
                while($csv_line = fgetcsv($handle,0, $dilimer)) {
                 ++$row_num;   
                 ++$z;
                    for ($k = 0, $j = count($csv_line); $k < $j; $k++) {
                        if ($csv_line[$k]==null) {
                           $v_val ='';    
                        }else {
                           $v_val = $csv_line[$k];
                        }
                        
                            $i++;        
                            if ($i == 1){ $f_name  = $v_val; }
                            if ($i == 2){ $l_name  = $v_val; }
                            if ($i == 3){ $p_name  = $v_val; }
                            if ($i == 4){ $email  = $v_val; }
                            if ($i == 5){ $phone  = $v_val; }
                            if ($i == 6){ $age  = $v_val; }
                            if ($i == 7){ $gender  = $v_val; }
                            if ($i == 8){ $priority_mark1  = $v_val; }
                            if ($i == 9){ $priority_mark2  = $v_val; }
                            if ($i == 10){ $hostess_id  = $v_val; }
                            if ($i == 11){ $activity_dt  = $v_val; }
                            if ($i == 12){ $activity_loc  = $v_val; }
                            if ($i == 13){ $test_res  = $v_val; }
                            if ($i == 14){ $test_id  = $v_val; }
                            if ($i == 15){ $activity_type  = $v_val; }
                            if ($i == 16){ $activity_id  = $v_val; }
                            if ($i == 17){ $advanced_data  = $v_val; }
                            if ($i == 18){ $hostess_name  = $v_val; }
                            
                        
                       }
                     $i=0;
                     $validate_status = 'unknown';
                  
//                  unset($validate_status);
//                  $validate_status = CsvBatches::getValidateStatusByEmail($email);
//                  if ($validate_status == 'passed'){
//                      $status_id = 3;
//                  }
//                  if ($validate_status == 'failed'){
//                      $status_id = 8;
//                  }
                  
                  $data[] = [
                            $batch_id,
                            $user_id,
                            $status_id,
                            $dt_load,
                            $f_name,
                            $l_name,
                            $p_name,
                            $email,
                            $phone,
                            $age,
                            $gender,
                            $priority_mark1,
                            $priority_mark2,
                            $hostess_id,
                            $activity_dt,
                            $activity_loc,
                            $test_res ,
                            $test_id,
                            $hostess_name,
                            $activity_type,
                            $activity_id,
                            $advanced_data,
                            $validate_status
                          
                          ] ;

                  if ($z >= 1000){
                      $z = 0;
                       
                        $db = Yii::$app->db;
                        $sql = $db->queryBuilder->batchInsert(CsvBatches::tableName(), [
                                    'batch_id',
                                    'user_id',
                                    'status_id',
                                    'dt_load',
                                    'f_name',
                                    'l_name',
                                    'p_name',
                                    'email',
                                    'phone',
                                    'age',
                                    'gender',
                                    'priority_mark1',
                                    'priority_mark2',
                                    'hostess_id',
                                    'activity_dt',
                                    'activity_loc',
                                    'test_res' ,
                                    'test_id',
                                    'hostess_name',
                                    'activity_type',
                                    'activity_id',
                                    'advanced_data',
                                    'validate_status'

                            ], $data);
                        $db->createCommand($sql)->execute();
                        unset($data);  
                
                  }
                  
                }
                
                 $db = Yii::$app->db;
                        $sql = $db->queryBuilder->batchInsert(CsvBatches::tableName(), [
                                    'batch_id',
                                    'user_id',
                                    'status_id',
                                    'dt_load',
                                    'f_name',
                                    'l_name',
                                    'p_name',
                                    'email',
                                    'phone',
                                    'age',
                                    'gender',
                                    'priority_mark1',
                                    'priority_mark2',
                                    'hostess_id',
                                    'activity_dt',
                                    'activity_loc',
                                    'test_res' ,
                                    'test_id',
                                    'hostess_name',
                                    'activity_type',
                                    'activity_id',
                                    'advanced_data',
                                    'validate_status'

                            ], $data);
                        $db->createCommand($sql)->execute();
                        unset($data); 
//                
               
 
               fclose($handle);
                  
               $batch_u = AgencyCsvBatch::find()->where(['id'=>$batch_id])->one();    
                  $batch_u->string_count = $row_num;
                  $batch_u->status_id = 1;
                  $batch_u->sys_group_id = 3;
                  $batch_u->update(false); 
                  
                  
                  BatchSteps::saveStep($batch_id, 'Загрузка файла', "Загрузили файл в которм $row_num строк.", 1, 2);
                
                return $batch_id;
            }
        }
        
        
        
    
   
        
}