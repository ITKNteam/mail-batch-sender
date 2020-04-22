<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "agency_csv_batch".
 *
 * @property integer $id
 * @property integer $agency_id
 * @property integer $user_id
 * @property string $file_name
 * @property integer $string_count
 * @property string $batch_date
 * @property integer $status_id
 * @property integer $sys_group_id
 *
 * @property SysParametrGroup $sysGroup
 * @property BatchData[] $batchDatas
 */
class AgencyCsvBatch extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'agency_csv_batch';
    }

    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['agency_id', 'user_id', 'string_count', 'status_id',
                'sys_group_id', 'last_list_id', 'campaign_id', 'current_step', 'email_tpl_id'], 'integer'],
            [['batch_date', 'batch_comment'], 'safe'],
            [['file_name', 'campaign_name'], 'string', 'max' => 450]
        ];
    }

    
    
      
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agency_id' => 'Агентсво',
            'user_id' => 'Загрузил',
            'file_name' => 'Файл',
            'string_count' => 'Кол-во строк',
            'batch_date' => 'Дата загрузки',
            'status_id' => 'Статус',
            'campaign_id' => 'Промо кампания',
            'sys_group_id' => 'Sys Group ID',
            'campaign_name' => 'Название рассылки',
            'email_tpl_id' => 'Шаблон письма',
            'batch_comment' => 'Комментарий к загрузке',
        ];
    }
    
    
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgency()
    {
        return $this->hasOne(Agency::className(), ['id' => 'agency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreateU()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSysGroup()
    {
        return $this->hasOne(SysParametrGroup::className(), ['id' => 'sys_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBatchDatas()
    {
        return $this->hasMany(BatchData::className(), ['batch_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return AgencyCsvBatchQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\QueryModels\AgencyCsvBatchQuery(get_called_class());
    }
    
    
    
    public static function getStatusList(){
        
        return ['1'=>'Загружено',
                '2'=>'Валидируется',
                '3'=>'Создание списков',
                '4'=>'Загрузка в Unisender',
                '5'=>'Создание письма',
                '6'=>'Проверено',
                '7'=>'Созданы списки для валидированых',
                '8'=>'Загрузка контактов валидированых',
                '9'=>'Получение результатов по валидированным',
                '10'=>'Запуск рассылки по валидированным',
               ];
        
    }
    public static function getStatus($status_id){
        $status = static::getStatusList();
        return $status[$status_id];
        
    }
    
    
    public static function getLastListId($batch_id){
        $batch = static::find()->where(['id'=>$batch_id])->one();
        
        return $batch->last_list_id;
        
    }
    public static function getBatchesList(){
      $list = [];
      
      
        $handbook = static::find()
                //->where(['hand_name_id'=>$hndbook_id])
                ->orderBy(['id' => SORT_DESC])
                ->all();
       
        
            foreach ($handbook as $row){
                $batch_name = $row->campaign_name ? $row->campaign_name : $row->file_name;
                $list[$row->id]= $row->id.' - '. $batch_name. ' ('.$row->string_count.' строк) '.$row->batch_date;
            }
       
        
        return $list;
    }
    
    
    public static function setCurrentStep($batch_id, $step){
        
        
          $condition = "id = $batch_id";
           static::updateAll(['current_step'=> $step 
                        ], $condition);
           
                return 1;
    }
   
    
    public static function setCampaignInfo($batch_id, $campaign_name, $email_tpl_id, $campaign_id=35){
        
        
           $condition = "id = $batch_id";
           static::updateAll(['campaign_name'=> $campaign_name,
                              'email_tpl_id'=> $email_tpl_id,
                              'campaign_id'=> $campaign_id,
                        ], $condition);
    }
    
    
    
    
    
    
    
}
