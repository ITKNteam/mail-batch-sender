<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "unisender_list".
 *
 * @property integer $id
 * @property integer $agency_id
 * @property string $list_title
 * @property string $before_subscribe_url
 * @property string $after_subscribe_url
 * @property integer $list_id
 * @property integer $create_uid
 * @property string $dt_create
 * @property integer $last_uid
 * @property string $dt_last
 *
 * @property Agency $agency
 * @property Users $createU
 * @property Users $lastU
 */
class UnisenderList extends \yii\db\ActiveRecord
{
    
    public $confirm_email = 1;
    public $cityes = [];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'unisender_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['agency_id', 'list_id', 'create_uid', 'last_uid', 'campaign_id', 'batch_id'], 'integer'],
            [['before_subscribe_url', 'after_subscribe_url'], 'string'],
            [['dt_create', 'dt_last', 'cityes'], 'safe'],
            [['list_title'], 'string', 'max' => 2000],
            [['validate_status'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agency_id' => 'Агентстов',
            'list_title' => 'Название списка',
            'confirm_email'=>'Письмо поддтверждения',
            'campaign_id'=>'Промо кампания',
            'before_subscribe_url' => 'Before Subscribe Url',
            'after_subscribe_url' => 'After Subscribe Url',
            'list_id' => 'List ID',
            'create_uid' => 'Create Uid',
            'dt_create' => 'Dt Create',
            'last_uid' => 'Last Uid',
            'dt_last' => 'Dt Last',
            'batch_id' => ' Пачка контактов',
            'validate_status' => 'Тип валидации (passed/unknown)',
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
        return $this->hasOne(Users::className(), ['id' => 'create_uid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastU()
    {
        return $this->hasOne(Users::className(), ['id' => 'last_uid']);
    }

    /**
     * @inheritdoc
     * @return \app\models\QueryModels\UnisenderListQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\QueryModels\UnisenderListQuery(get_called_class());
    }
    
    public static function newList($list_title, $agency_id, $confirm_email, $campaign_id=null, $batch_id=0, $validate_status=''){
        
        
        
        $answer = [];
        $api_key = AgencySettings::getApiKey($agency_id);
        
        
       
        $unisender_return = Yii::$app->unisender->createLists($list_title, $api_key);
        if (@$unisender_return['UnisenderAnswer']->result->id){
              $list = new UnisenderList();
        
             $list_id = @$unisender_return['UnisenderAnswer']->result->id;
             $list->agency_id = $agency_id;
             $list->list_title = $list_title;
             $list->list_id = $list_id;
             $list->campaign_id = $campaign_id;
             //$list->before_subscribe_url = $this->before_subscribe_url;
            // $list->after_subscribe_url = $this->after_subscribe_url;
            // $list->create_uid = @Yii::$app->user->id;
             $list->dt_create = date("Y-m-d H:i:s");
          //   $list->last_uid = @Yii::$app->user->id;
             $list->dt_last = date("Y-m-d H:i:s");
             $list->batch_id = $batch_id;
             $list->validate_status = $validate_status;
             $list->save(false);
             
             $id = $list->id;
           
//             
//             $confirm_email_body = UnisenderConfirmEmailTxt::find()->where(['id'=>$confirm_email])->one();
//             
//             $email_from_name = $confirm_email_body['sender_name'];
//             $email_from_email= $confirm_email_body['sender_email']; 
//             $email_subject= $confirm_email_body['subject'];
//             $email_text= $confirm_email_body['body'];
//             
//              $unisender_return2 = Yii::$app->unisender->updateOptInEmail($list_id, $email_from_name, $email_from_email, $email_subject, $email_text,  $api_key );
//           
//              $answer = ['id'=>$id,
//                         'message'=> 'Операция выполнена успешно!<br>'.@$unisender_return2['message']    ];
//             
              $answer = ['id'=>$id,
                         'message'=> 'Операция выполнена успешно!<br>'.@$unisender_return['message']    ];
             
              
             
            
         } else {
              $answer = ['id'=>0,
                        'message'=>  'Ошибка ! '.  @$unisender_return['UnisenderAnswer']->error];
         }
         
         $message = 'UnisenderList.NewList = '.$batch_id.' - ' . implode(',',$answer)
                 .'$list_title = '. $list_title
                 .'$agency_id = '.$agency_id
                 .'$confirm_email = '.$confirm_email
                 .'$campaign_id = '.$campaign_id
                 .'$batch_id = '.$batch_id
                 .'$validate_status = '.$validate_status;
        CronLog::saveLog($message);
        
        
        return  $answer;
    }
    public static function newListWithConfirmEmail($list_title, 
                                                   $agency_id, 
                                                   $batch_id, 
                                                   $validate_status,  
                                                   $campaign_id=null){
        
        
        
        $answer = [];
        $api_key = AgencySettings::getApiKey($agency_id);
        
        
       
        $unisender_return = Yii::$app->unisender->createLists($list_title, $api_key);
         CronLog::saveLog('UnisenderList.newListWithConfirmEmail createLists UNISENDER-MESSAGE ='.@$unisender_return['message']);
        if (@$unisender_return['UnisenderAnswer']->result->id){
              $list = new UnisenderList();
        
             $list_id = @$unisender_return['UnisenderAnswer']->result->id;
             $list->agency_id = $agency_id;
             $list->list_title = $list_title;
             $list->list_id = $list_id;
             $list->campaign_id = $campaign_id;
             //$list->before_subscribe_url = $this->before_subscribe_url;
            // $list->after_subscribe_url = $this->after_subscribe_url;
            // $list->create_uid = @Yii::$app->user->id;
             $list->dt_create = date("Y-m-d H:i:s");
          //   $list->last_uid = @Yii::$app->user->id;
             $list->dt_last = date("Y-m-d H:i:s");
             $list->batch_id = $batch_id;
             $list->validate_status = $validate_status;
             $list->save(false);
             
             $id = $list->id;
           
//             
             $confirm_email_body = UnisenderConfirmEmailTxt::find()->where(['agency_id'=>$agency_id])->one();
             
             $email_from_name = $confirm_email_body['sender_name'];
             $email_from_email= $confirm_email_body['sender_email']; 
             $email_subject= $confirm_email_body['subject'];
             $email_text= $confirm_email_body['body'];
             
              $unisender_return2 = Yii::$app->unisender->updateOptInEmail($list_id, $email_from_name, $email_from_email, $email_subject, $email_text,  $api_key );
                 CronLog::saveLog('UnisenderList.newListWithConfirmEmail updateOptInEmail UNISENDER-MESSAGE ='.@$unisender_return2['message']);
              
              $answer = ['id'=>$id,
                         'message_email'=> 'Операция выполнена успешно!<br>'.@$unisender_return2['message']    ];
             
              $answer[] = ['message_list'=> 'Операция выполнена успешно!<br>'.@$unisender_return['message']    ];
             
              
             
            
         } else {
              $answer = ['id'=>0,
                        'message'=>  'Ошибка ! '.  @$unisender_return['UnisenderAnswer']->error];
         }
         
         
         
         $message = 'UnisenderList.newListWithConfirmEmail = '.$batch_id.' - ' 
                 .'$list_title = '. $list_title
                 .'$agency_id = '.$agency_id
                 
                 .'$campaign_id = '.$campaign_id
                 .'$batch_id = '.$batch_id
                 .'$validate_status = '.$validate_status;
        CronLog::saveLog($message);
        
        
        return  $answer;
    }
    
    
    public static function getListName(){
        $ret = [];
        $model = static::find()->select('id, list_title')->orderBy(['id'=>SORT_DESC])->all();
        foreach ($model as $row)
            $ret[$row['id']]= $row['list_title'];
        
        return $ret;
    }
    
    
    
}
