<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "agency".
 *
 * @property integer $id
 * @property string $name
 * @property string $dt_create
 * @property integer $uid
 *
 * @property AgencySettings[] $agencySettings
 */
class Agency extends \yii\db\ActiveRecord
{
    
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'agency';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dt_create'], 'safe'],
            [['uid'], 'integer'],
            [['name'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'dt_create' => 'Dt Create',
            'uid' => 'Uid',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgencySettings()
    {
        return $this->hasMany(AgencySettings::className(), ['agency_id' => 'id']);
    }
    
    
    public static function getList(){
        $ret = [];
        $a = static::find()->select('id, name')->all();
        foreach ($a as $row)
            $ret[$row['id']]= $row['name'];
        
        return $ret;
        
    }
    
    public static function getName($id){
            
        $model = static::find()->where(['id'=>$id])->one();
        return $model['name'];
    }
    
    
    /**
     *  Даёт кусок пачки, который можно использовать для одного агентсва
     *  для проверки адресов mail.ru
     * 
     */
    
    public static function getAgencyBatchSlice($batch_id, $mode='Any'){
        $a_count =  Agency::find()->count();
        if ($mode='Any'){
            $b_count = CsvBatches::find()->where(['batch_id'=>$batch_id,
                                                   'validate_status'=>'unknown'])->count();
        } else {
            $b_count = CsvBatches::find()->where(['batch_id'=>$batch_id,
                                                   'validate_status'=>'unknown',
                                                   'status_id'=>'7' 
                                            ])->count();
        }
        
        $slice = ceil($b_count/$a_count);
        
        
        
        return $slice;
        
    }
    
    
    
    public static function setListsForBatch($batch_id){
          $slice  = self::getAgencyBatchSlice($batch_id);
          CsvBatches::updateAll(['list_id'=>null], 
                                " batch_id = $batch_id"); 
        
        $batch_info = AgencyCsvBatch::find()->where(['id'=>$batch_id])->one();
        
        $batch_name = $batch_info['campaign_name'];
        $validate_status = 'unknown';
        
        $a = self::find()->all();
        $i = 0;
          $off_set = 0;
        $ret = [];
        
        foreach ($a as $agency ){
                
            if($i)
                $off_set = $slice * $i;
           
                  $list_title = $batch_name.' '.$agency->name. ' slice='.$slice .' ('.crypt('rasmuslerdorf'.rand(0, 9999), date('s')).')';
                  $answer=   UnisenderList::newListWithConfirmEmail($list_title, 
                                               $agency->id, 
                                                $batch_id,
                                                $validate_status);
                 $list_id = $answer['id'];
                 $ret[$i] = $list_id;
                  
                 if($list_id){
                 
                        $b_count = CsvBatches::find()
                                ->where(['batch_id'=>$batch_id, 
                                         'validate_status'=>'unknown',
                                         'status_id'=>'3',
                                        
                                        ])
                                ->limit($slice)
                                ->offset($off_set)
                                ->all();

                        $db = Yii::$app->db;
                        $transaction = $db->beginTransaction();
                       try {   
                        foreach ($b_count as $batch ){

                            $SQL = "update westbtl.csv_batches
                                       set list_id = $list_id
                                           where id = $batch->id";

                               $db = Yii::$app->db;
                               $steps =  $db->createCommand($SQL)->execute();

                        }
                        $transaction->commit();
                        } catch (Exception $e) {

                           $transaction->rollBack();

                           return 'Error Transaction';

                       }
                  }      
                       
               ++$i; 
         
            
        }
        
        return $ret;
       
        
        
        
        
        
        
    }
    
    
}
