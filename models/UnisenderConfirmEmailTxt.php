<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "unisender_confirm_email_txt".
 *
 * @property integer $id
 * @property string $name
 * @property string $body
 * @property string $dt_create
 * @property integer $uid_create
 */
class UnisenderConfirmEmailTxt extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'unisender_confirm_email_txt';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['body'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by', 'agency_id'], 'integer'],
            [['name'], 'string', 'max' => 45],
            [['sender_name', 'sender_email', 'subject'], 'string', 'max' => 450]
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
     * @return \yii\db\ActiveQuery
     */
    public function getAgency()
    {
        return $this->hasOne(Agency::className(), ['id' => 'agency_id']);
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
             'name' => 'Название письма',
            'sender_name' => 'Имя отправителя. Произвольная строка, не совпадающая с e-mail адресом',
            'sender_email' => 'E-mail адрес отправителя. Этот e-mail должен быть проверен.',
            'subject' => 'Строка с темой письма. Может включать поля подстановки.',
            'body' => 'Текст письма в формате HTML с возможностью добавлять поля подстановки.',
            'dt_create' => 'Dt Create',
            'uid_create' => 'Uid Create',
            'agency_id'=>'Unisender Account'
        ];
    }
    
    
    
    public static function getList(){
        $ret = [];
        $a = static::find()->select('id, name')->all();
        foreach ($a as $row)
            $ret[$row['id']]= $row['name'];
        
        return $ret;
        
    }
}
