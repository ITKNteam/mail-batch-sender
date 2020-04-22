<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "unisender_campaign".
 *
 * @property integer $id
 * @property integer $agency_id
 * @property integer $message_id
 * @property integer $campaign_id
 * @property string $status
 * @property integer $count
 * @property string $pay_sum
 * @property string $currency
 * @property string $dt_create
 * @property integer $uid_create
 * @property string $name
 *
 * @property Agency $agency
 * @property UnisenderEmail $message
 * @property Users $uidCreate
 */
class UnisenderCampaign extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'unisender_campaign';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['agency_id', 'message_id', 'campaign_id', 'count', 'uid_create'], 'integer'],
            [['dt_create'], 'safe'],
            [['status', 'pay_sum', 'currency', 'name'], 'string', 'max' => 45],
            [[ 'name'], 'string', 'max' => 900]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agency_id' => 'Агентство',
            'message_id' => 'Email для массовой рассылки',
            'campaign_id' => 'Campaign ID',
            'status' => 'Status',
            'count' => 'Count',
            'pay_sum' => 'Pay Sum',
            'currency' => 'Currency',
            'dt_create' => 'Dt Create',
            'uid_create' => 'Uid Create',
            'name' => 'Название рассылки',
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
    public function getMessage()
    {
        return $this->hasOne(UnisenderEmail::className(), ['id' => 'message_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUidCreate()
    {
        return $this->hasOne(Users::className(), ['id' => 'uid_create']);
    }

    /**
     * @inheritdoc
     * @return \app\models\QueryModels\UnisenderCampaignQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\QueryModels\UnisenderCampaignQuery(get_called_class());
    }
    
    public static function newCampaign($name, $message_id,  $agency_id = 1){
       $answer = [];
       $status = null;
       $currency= null;
       $count= null;
       
        $api_key = AgencySettings::getApiKey($agency_id);
       
       
        $unisender_message_id = UnisenderEmail::find()->where(['id'=>$message_id])->select('message_id')->one();  
        
        $unisender_return = Yii::$app->unisender->createCampaign($unisender_message_id['message_id'], $api_key);
        if (@$unisender_return['UnisenderAnswer']->result->campaign_id){
              $list = new UnisenderCampaign();
        
             $campaign_id = @$unisender_return['UnisenderAnswer']->result->campaign_id;
             $status = @$unisender_return['UnisenderAnswer']->result->status;
             $count = @$unisender_return['UnisenderAnswer']->result->count;
             $currency = @$unisender_return['UnisenderAnswer']->result->currency;
             $list->name = $name;
             $list->message_id = $message_id;
           
             $list->campaign_id = $campaign_id;
             $list->status = $status;
             $list->count = $count;
             $list->currency = $currency;
             $list->agency_id = $agency_id;
             //$list->before_subscribe_url = $this->before_subscribe_url;
            // $list->after_subscribe_url = $this->after_subscribe_url;
            // $list->uid_create = @Yii::$app->user->id;
             $list->dt_create = date("Y-m-d H:i:s");
             $list->save();
             $answer = ['id'=>$list['id'],
                         'message'=> 'Операция выполнена успешно!'    ];
            
         } else {
              $answer = ['id'=>0,
                        'message'=>  'Ошибка ! '.  @$unisender_return['UnisenderAnswer']->error];
         }
        
        
        return  $answer;
    }
    
    
    
    public static function widgetLastCampaignStat(){
        
        
        
        $cmp = static::find()->orderBy('id desc')->one();
        
        $agency_id  = $cmp['agency_id'];
        $campaign_id = $cmp['campaign_id'];
        $name = $cmp['name'];
        
        $api_key = AgencySettings::getApiKey($agency_id);
        $res = Yii::$app->unisender->getCampaignAggregateStats($campaign_id, $api_key);
        
        
        
        $CampaignStatus = Yii::$app->unisender->getCampaignStatus($campaign_id, $api_key);
        
        $CampaignStatusFormat = ['status'=> static::CampaignStatusList(@$CampaignStatus['UnisenderAnswer']->result->status),
                                 'creation_time'=>date("d M Y h:i:s",  strtotime(@$CampaignStatus['UnisenderAnswer']->result->creation_time)),
                                 'start_time'=>date("d M Y h:i:s",  strtotime(@$CampaignStatus['UnisenderAnswer']->result->start_time))
                                ];
        
        return ['res'=>$res, 'name'=>$name, 'CampaignStatus'=>$CampaignStatusFormat];
        
        
    }
    
    
   
    public static function CampaignStatusList($status){
        $status_list = ['waits_censor'=>'рассылка ожидает проверки.',
                        'censor_hold'=>'фактически эквивалентна "рассылка ожидает проверки": рассмотрена администратором, но отложена для дальнейшей проверки.', 
                        'declined'=>'рассылка отклонена администратором.',  
                        'waits_schedule'=>'задание на постановку рассылки в очередь получено и рассылка ждёт постановки в очередь. Обычно рассылка в этом состоянии находится одну-две минуты перед тем, как перейти в состояние scheduled.',  
                        'scheduled'=>'рассылка запланирована к запуску. Как только настанет время отправки, она будет запущена.',  
                        'in_progress'=>'рассылка выполняется.',  
                        'analysed'=>'все сообщения отправлены, идёт анализ результатов.',  
                        'completed'=>'все сообщения отправлены и анализ результатов закончен.',
                        'stopped'=>'рассылка поставлена "на паузу".',
                        'canceled'=>'рассылка отменена (обычно из-за нехватки денег или по желанию пользователя).',
        ];
        
        
        return @$status_list[$status];
        
        
    }
    
    
    
    public static function getEmailsList(){
        $ret = [];
        $model = static::find()->select('id, name')->orderBy(['id'=>SORT_DESC])->all();
        foreach ($model as $row)
            $ret[$row['id']]= $row['name'];
        
        return $ret;
    }
    
    /****
     * Возвращает все id объетов, связанных с этой рассылкой
     */
    
    /// 'id' => 'ID',
//            'agency_id' => 'Агентство',
//            'message_id' => 'Email для массовой рассылки',
//            'campaign_id' => 'Campaign ID',
//            'status' => 'Status',
//            'count' => 'Count',
//            'pay_sum' => 'Pay Sum',
//            'currency' => 'Currency',
//            'dt_create' => 'Dt Create',
//            'uid_create' => 'Uid Create',
//            'name' => 'Название рассылки',
    
    public static function getFullInfo($local_id){
        
        $camp = static::find()->where(['id'=>$local_id])->one();
        
        $batches = AgencyCsvBatch::find()->where(['last_list_id'=>$camp->message->list_id])->all();
        
        $batch_inf = [];
        $batch_ids = [];
        $batches_id = 161;
        
//        foreach ($batches as $batch){
//            $batch_inf[] = ['id'=>$batch->id,
//                            'row_count'=>$batch->string_count,
//                            'dt_create'=>$batch->batch_date,
//                            'file_name'=>$batch->file_name,
//                            'last_list_id'=>$batch->last_list_id,
//                            ];
//            //$batches_id[] = $batch->id;
//            $batches_id[] = 161;
//            }
        
        $ret = ['campaign'=>
                    [
                      'local_id'=>$camp->id,
                      'unisender_id'=>$camp->campaign_id,  
                      'name'=>$camp->name,  
                      'dt_create'=>$camp->dt_create, 
                      'batches_id'=>$batches_id,   
                      'agency_id'=>$camp->agency_id,  
                
                    ],
               'email'=>[
                     'local_id'=>$camp->message_id,
                      'unisender_id'=>$camp->message->message_id,  
                      'name'=>$camp->message->name,  
                      'dt_create'=>$camp->message->dt_create,  
                 ],
               'list'=> [
                     'local_id'=>$camp->message->list_id,
                      'unisender_id'=>$camp->message->list->list_id,  
                      'name'=>$camp->message->list->list_title 
                 ], 
                'batches'=>$batch_inf
                
        ];
        
        return $ret;
        
    }
    
    
}
