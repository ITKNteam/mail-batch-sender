<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "unisender_export_contacts".
 *
 * @property integer $id
 * @property integer $list_id
 * @property string $email
 * @property string $email_status
 * @property string $email_availability
 * @property string $email_add_time
 * @property string $email_confirm_time
 * @property string $email_subscribe_times
 * @property string $email_unsubscribed_list_ids
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 */
class UnisenderExportContacts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'unisender_export_contacts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['list_id', 'created_by', 'updated_by'], 'integer'],
            [['email_add_time', 'email_confirm_time', 'created_at', 'updated_at'], 'safe'],
            [['email_subscribe_times', 'email_unsubscribed_list_ids'], 'string'],
            [['email'], 'string', 'max' => 250],
            [['email_status', 'email_availability'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'list_id' => 'List ID',
            'email' => 'Email',
            'email_status' => 'Email Status',
            'email_availability' => 'Email Availability',
            'email_add_time' => 'Email Add Time',
            'email_confirm_time' => 'Email Confirm Time',
            'email_subscribe_times' => 'Email Subscribe Times',
            'email_unsubscribed_list_ids' => 'Email Unsubscribed List Ids',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
    
      public function behaviors()
    {
        return [
         //   'timestamp' => \yii\behaviors\TimestampBehavior::className(),
             'blame' => \yii\behaviors\BlameableBehavior::className(),
             'timestamp' => [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                    'createdAtAttribute' => 'created_at',
                    'updatedAtAttribute' => 'updated_at',
                   
                ],
                   'value' => new \yii\db\Expression('NOW()'),
            ],
            
        ];
    }

    /**
     * @inheritdoc
     * @return \app\models\QueryModels\UnisenderExportContactsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\QueryModels\UnisenderExportContactsQuery(get_called_class());
    }
    
    
    public static function export($list_id,  $agency_id){
        $api_key = AgencySettings::getApiKey($agency_id);
         static::deleteAll(['list_id'=>$list_id]);
         
         $local_list_id = UnisenderList::find()->where(['list_id'=>$list_id])->one();
         
        $SQL = "SELECT ceil(sum(string_count)/1000) as steps FROM agency_csv_batch
                where last_list_id =".$local_list_id->id;

        $db = Yii::$app->db;
        $steps =  $db->createCommand($SQL)->queryScalar();
        
        $batches  = AgencyCsvBatch::find()->select('id')->where(['last_list_id'=>$local_list_id->id])->asArray(); 
     
          $data=[];
        
     for ($i=0;$i<$steps; ++$i){
         $offset = 0;
         if ($i)
           $offset = $i * 1000;
         $res = Yii::$app->unisender->exportContacts($list_id, $api_key, $offset);
         
         if ($res['UnisenderAnswer']->result->data){
          foreach ($res['UnisenderAnswer']->result->data as $one) {
         //     $user =  CrmUsers::find()->where(['batch_id'=>$batches,'email'=>$one[0]])->one();
              
                            $user = CsvBatches::find()->where(['list_id'=>  $list_id, 
                                                 'email'=>$one[0]])->one();
              
     //print_r($user->activity_loc);
              
              $data[] = [
                    $list_id,
                    $one[0], 
                    $one[1], 
                    $one[2], 
                    $one[3], 
                    $one[5], 
                    $one[8], 
                    $one[9], 
                    @$user->activity_loc
               
                 ];
              }
            }
              
         
      };
      if ($data){
       $db = Yii::$app->db;
                $sql = $db->queryBuilder->batchInsert(UnisenderExportContacts::tableName(), [
                   'list_id',
                    'email',
                    'email_status',
                    'email_availability',
                    'email_add_time',
                    'email_confirm_time',
                    'email_subscribe_times',
                    'email_unsubscribed_list_ids',
                    'city',
            ], $data);
         $res =  $db->createCommand($sql )->execute();
         
      }
        
        
        
        
    }
    
    
    
     public static function export2($list_id, $slice_count, $step_count){
         $unisender_list_id = UnisenderList::find()->where(['id'=>$list_id])->select('list_id, agency_id')->one();
         $agency_id   =  $unisender_list_id->agency_id;
         
        $api_key = AgencySettings::getApiKey($agency_id);
        //static::deleteAll(['list_id'=>$list_id]);
         
         $list= UnisenderList::find()->where(['id'=>$list_id])->one();
         $data=[];
        
        //$steps =  ceil($slice_count/1000);
        
        //$batches  = AgencyCsvBatch::find()->select('id')->where(['last_list_id'=>$local_list_id->id])->asArray(); 
     
        
        $field_names = ['email',
                    'email_status',
                    'email_availability',
                    'email_add_time',
                    'email_confirm_time',
                    'email_subscribe_times',
                    'email_unsubscribed_list_ids'];
         
     //for ($i=0;$i<$steps;++$i){
         $step_count = $step_count -1;
         $offset = 0;
         if ($step_count)
           $offset = $step_count * 1000;
         
         $res = Yii::$app->unisender->exportContacts($list->list_id, $api_key, $offset, $limit=1000, $field_names);
         
         if (!empty( $res['UnisenderAnswer']->result->data)){
          foreach ($res['UnisenderAnswer']->result->data as $one) {
         
              
              
             // $user =  CrmUsers::find()->where(['batch_id'=>$batches,'email'=>$one[0]])->one();
              $user = CsvBatches::find()->where(['list_id'=>  $list_id, 
                                                 'email'=>$one[0]])->one();
              
              
              if ($user){
              if( isset($one[1])) 
                 $user->email_status = $one[1];
              
              if (!is_null($one[2]))
                 $user->email_availability =  $one[2];
              
              if (!is_null($one[5]))
                $user->email_confirm_time = $one[5];
              
              $user->status_id = 8;
              
//                when email_availability is null then 'данные не поступали'
//                when email_availability  = 'available' then 'адрес доступен'
//                when 	email_availability  = 'unreachable' then 'адрес недоступен'
//                when 	email_availability  = 'temp_unreachable' then 'адрес временно недоступен'
//                when 	email_availability  = 'mailbox_full' then 'почтовый ящик переполнен'
//                when 	email_availability  = 'spam_rejected' then 'письмо сочтено спамом сервером получателя. Через несколько дней этот статус будет снят.'
//                when 	email_availability  = 'spam_folder' then 'письмо помещено в спам самим получателем.'
              
              if ($one[2] == 'available'){
                $user->validate_status = 'passed';
              }
                elseif ($one[2] == 'mailbox_full') {

                $user->validate_status = 'passed';
                             } 
                elseif ($one[2] == 'spam_folder') {

                            $user->validate_status = 'passed';
                 } 
                             elseif ($one[2] == 'spam_rejected') {

                                $user->validate_status = 'passed';
                             } 
             
              else {
                  $user->validate_status = 'failed';
              }
              
              
              $user->update(false);     
              }
     
              $data[] = [
                    $list_id,
                    $one[0], // email 
                    $one[1], // email_status
                    $one[2], // email_availability
                    $one[3],  // email_add_time
                    $one[5],  // email_confirm_time
                    $one[8], // email_unsubscribed_list_ids
                    $one[9],  // email_unsubscribed_list_ids
                    @$user->activity_loc
               
                 ];
              }
            } else {
                return $res;
            }
              
         
   //   }
      
      if ($data){
       $db = Yii::$app->db;
                $sql = $db->queryBuilder->batchInsert(UnisenderExportContacts::tableName(), [
                   'list_id',
                    'email',
                    'email_status',
                    'email_availability',
                    'email_add_time',
                    'email_confirm_time',
                    'email_subscribe_times',
                    'email_unsubscribed_list_ids',
                    'city',
            ], $data);
         $res =  $db->createCommand($sql )->execute();
         
      }
        
        
     return  $data;
        
    }
}
