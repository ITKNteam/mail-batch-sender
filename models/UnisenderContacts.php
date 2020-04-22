<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "unisender_contacts".
 *
 * @property integer $id
 * @property integer $agency_id
 * @property integer $batch_id
 * @property integer $total
 * @property integer $inserted
 * @property integer $updated
 * @property integer $deleted
 * @property integer $new_emails
 * @property integer $invalid
 * @property integer $uid_create
 * @property string $dt_create
 *
 * @property Agency $agency
 * @property BatchData $batch
 * @property Users $uidCreate
 */
class UnisenderContacts extends \yii\db\ActiveRecord
{
    
    public $new_contacts=0;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'unisender_contacts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['agency_id', 'batch_id', 'total', 'inserted', 'updated', 'deleted', 'new_emails', 'invalid', 'uid_create', 'list_id'], 'integer'],
            [['dt_create'], 'safe']
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
            'batch_id' => 'Batch ID',
            'total' => 'Всего',
            'inserted' => 'Вставлено',
            'updated' => 'Обновленно',
            'deleted' => 'Удалено',
            'new_emails' => 'Новые email',
            'invalid' => 'Некоректные',
            'uid_create' => 'Uid Create',
            'dt_create' => 'Дата ',
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
    public function getBatch()
    {
        return $this->hasOne(BatchData::className(), ['id' => 'batch_id']);
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
     * @return \app\models\QueryModels\UnisenderContactsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\QueryModels\UnisenderContactsQuery(get_called_class());
    }
    
    
    
    public static function getLastBatchInf(){
        
        $batch = static::find()->select('max(batch_id) as batch_id, max(list_id) as list_id')->one();
        
        $inf = static::find()->select('max(batch_id) as batch_id, max(list_id) as list_id, sum(inserted) as new_contacts')
                        ->where(['batch_id'=>$batch['batch_id'],
                                 'list_id'=>$batch['list_id']
                                ]
                                    )->one();
        
        return $inf;
    }
}
