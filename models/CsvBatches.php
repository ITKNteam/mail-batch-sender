<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "csv_batches".
 *
 * @property integer $id
 * @property integer $batch_id
 * @property integer $user_id
 * @property integer $status_id
 * @property string $dt_load
 * @property string $f_name
 * @property string $l_name
 * @property string $p_name
 * @property string $email
 * @property string $phone
 * @property string $age
 * @property string $gender
 * @property string $priority_mark1
 * @property string $priority_mark2
 * @property string $hostess_id
 * @property string $activity_dt
 * @property string $activity_loc
 * @property string $test_res
 * @property integer $test_id
 * @property string $hostess_name
 * @property string $activity_type
 * @property string $activity_id
 * @property string $advanced_data
 *
 * @property Users $user
 */
class CsvBatches extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'csv_batches';
    }
//
//    
//ALTER TABLE `westbtl`.`csv_batches` 
//ADD COLUMN `list_id` INT NULL AFTER `validate_dt`,
//ADD COLUMN `sub_person_id` VARCHAR(45) NULL AFTER `list_id`,
//ADD COLUMN `sub_code` VARCHAR(45) NULL AFTER `sub_person_id`,
//ADD COLUMN `sub_message` TEXT NULL AFTER `sub_code`,
//ADD INDEX `csv_batches_fk2_idx` (`list_id` ASC),
//ADD INDEX `csv_batches_inx9` (`sub_code` ASC),
//ADD INDEX `csv_batches_inx10` (`sub_person_id` ASC);
//ALTER TABLE `westbtl`.`csv_batches` 
//ADD CONSTRAINT `csv_batches_fk2`
//  FOREIGN KEY (`list_id`)
//  REFERENCES `westbtl`.`unisender_list` (`id`)
//  ON DELETE NO ACTION
//  ON UPDATE NO ACTION;

    
    
    
//    ALTER TABLE `westbtl`.`csv_batches` 
//ADD COLUMN `email_status` VARCHAR(100) NULL AFTER `sub_message`,
//ADD COLUMN `email_availability` VARCHAR(100) NULL AFTER `email_status`,
//ADD COLUMN `email_confirm_time` TIMESTAMP NULL AFTER `email_availability`,
//ADD INDEX `csv_batches_inx11` (`email_availability` ASC, `email_status` ASC);

    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['batch_id', 'user_id', 'status_id', 'test_id', 'list_id'], 'integer'],
            [['dt_load', 'age', 'activity_dt','validate_dt','email_confirm_time'], 'safe'],
            [['test_res', 'advanced_data', 'sub_message'], 'string'],
            [['f_name', 'l_name', 'p_name', 'email'], 'string', 'max' => 450],
            [['sub_person_id', 'sub_code'], 'string', 'max' => 45],
            [['email_status', 'email_availability'], 'string', 'max' => 100],
            [['phone', 'gender', 'hostess_id', 'activity_loc', 'activity_type', 'activity_id','validate_status','validate_event'], 'string', 'max' => 45],
            [['priority_mark1', 'priority_mark2'], 'string', 'max' => 90],
            [['hostess_name','validate_details'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'batch_id' => '',
            'user_id' => 'User ID',
            'status_id' => 'Status ID',
            'dt_load' => 'Dt Load',
            'f_name' => 'Имя',
            'l_name' => 'Фамилия',
            'p_name' => 'Отчество',
            'email' => 'Email',
            'phone' => 'номер телефона ',
            'age' => ' возраст (в формате dd.mm.yyyy)',
            'gender' => 'пол (m/f)',
            'gender_ar' => 'Пол',
            'priority_mark1' => 'Priority Mark1',
            'priority_mark2' => 'Priority Mark2',
            'hostess_id' => 'Hostess ID',
            'activity_dt' => 'Activity Dt',
            'activity_loc' => 'Города активности',
            'activity_loc_ar' => 'Города активности',
            'test_res' => 'Test Res',
            'test_id' => 'Test ID',
            'hostess_name' => 'Hostess Name',
            'hostess_name_ar' => 'Hostess Name',
            'activity_type' => 'Activity Type',
            'activity_id' => 'Activity ID',
            'advanced_data' => 'Advanced Data',
            'validate_status'=>'',
            'validate_event'=>'',
            'validate_details'=>'',
            'validate_dt'=>'',
            'email_status'=>'Статус e-mail адреса',
            'validate_status'=>'Статус валидации',
            'validate_status_ar'=>'Статус валидации',
            'email_availability'=>'Доступность e-mail адреса по результатам последних рассылок',
            
        ];
    }
    

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\QueryModels\CsvBatchesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\QueryModels\CsvBatchesQuery(get_called_class());
    }
    
    
    public static function getStatusList(){
        
        return [1=>'Загружен в систему', 2=>'Отправлен в Unisender'];
    }
    
    public static function getStatusName($id){
        
        $status = static::getStatusList();
        
        return $status[$id];
        
    }
    
    
    public static function getValidateStatusByEmail($email){
        unset($r);
        $r = CsvBatches::find()->where(['email'=>$email, 'validate_status'=>'passed'])->count();
        if ($r){
            return 'passed';
        } else {
            $r = CsvBatches::find()->where(['email'=>$email, 'validate_status'=>'failed'])->count();
            if ($r){
               return 'failed';
            }
        }
        return null;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBatch()
    {
        return $this->hasOne(AgencyCsvBatch::className(), ['id' => 'batch_id']);
    }
    
    
 
     public static function getSex(){
        return ['1'=>'МУЖСКОЙ', '2'=>'ЖЕНСКИЙ'];
    }
    
     public static function userSexName($sex) {
        $sex_ar = CsvBatches::getSex(); 
       // $sex_ar = [1=>'МУЖСКОЙ', 2=>'ЖЕНСКИЙ']; 
       if (array_key_exists($sex, $sex_ar)){ 
        $val = $sex_ar[$sex];
           return $val;
       }
         
        return 'Нет данных'; 
     }
    
    
}
