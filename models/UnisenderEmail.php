<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "unisender_email".
 *
 * @property integer $id
 * @property integer $agency_id
 * @property string $name
 * @property string $sender_name
 * @property string $sender_email
 * @property string $subject
 * @property integer $list_id
 * @property string $body
 * @property integer $message_id
 * @property string $dt_create
 * @property integer $uid_create
 *
 * @property Agency $agency
 * @property UnisenderList $list
 * @property Users $uidCreate
 */
class UnisenderEmail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'unisender_email';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['agency_id', 'list_id', 'message_id', 'uid_create'], 'integer'],
            [['body'], 'string'],
            [['dt_create'], 'safe'],
            [['name', 'sender_name', 'sender_email', 'subject'], 'string', 'max' => 450]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agency_id' => 'Agency ID',
            'name' => 'Название письма',
            'sender_name' => 'Имя отправителя. Произвольная строка, не совпадающая с e-mail адресом',
            'sender_email' => 'E-mail адрес отправителя. Этот e-mail должен быть проверен.',
            'subject' => 'Строка с темой письма. Может включать поля подстановки.',
            'list_id' => 'Список рассылки',
            'body' => 'Текст письма в формате HTML с возможностью добавлять поля подстановки.',
            'message_id' => 'Message ID',
            
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
    public function getList()
    {
        return $this->hasOne(UnisenderList::className(), ['id' => 'list_id']);
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
     * @return \app\models\QueryModels\UnisenderEmailQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\QueryModels\UnisenderEmailQuery(get_called_class());
    }
    
    
    
     public static function newEmail($name, $list_id, $subject, $body, $sender_name, $sender_email, $agency_id = 1){
        $answer = [];
        
        $unisender_list_id = UnisenderList::find()->where(['id'=>$list_id])->select('list_id')->one();  
         $api_key = AgencySettings::getApiKey($agency_id);
        
        $unisender_return = Yii::$app->unisender->createEmailMessage($unisender_list_id['list_id'], $subject, $body, $sender_name, $sender_email, $api_key);
        if (@$unisender_return['UnisenderAnswer']->result->message_id){
              $list = new UnisenderEmail();
        
             $message_id = @$unisender_return['UnisenderAnswer']->result->message_id;
             $list->name = $name;
             $list->sender_name = $sender_name;
             $list->sender_email = $sender_email;
             $list->subject = $subject;
             $list->list_id = $list_id;
             $list->body = $body;
             
             $list->message_id = $message_id;
             $list->agency_id = $agency_id;
             //$list->before_subscribe_url = $this->before_subscribe_url;
            // $list->after_subscribe_url = $this->after_subscribe_url;
             //$list->uid_create = @Yii::$app->user->id;
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
    
    public static function getEmailsList(){
        $ret = [];
        $model = static::find()->select('id, name')->orderBy(['id'=>SORT_DESC])->all();
        foreach ($model as $row)
            $ret[$row['id']]= $row['name'];
        
        return $ret;
    }
}
