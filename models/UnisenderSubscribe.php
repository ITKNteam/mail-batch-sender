<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "unisender_subscribe".
 *
 * @property integer $id
 * @property integer $agency_id
 * @property integer $batch_id
 * @property integer $list_id
 * @property string $email
 * @property string $person_id
 * @property string $message
 * @property string $code
 * @property string $dt_create
 *
 * @property Agency $agency
 * @property AgencyCsvBatch $batch
 * @property UnisenderList $list
 */
class UnisenderSubscribe extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'unisender_subscribe';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['agency_id', 'batch_id', 'list_id'], 'integer'],
            [['message'], 'string'],
            [['dt_create'], 'safe'],
            [['email'], 'string', 'max' => 250],
            [['person_id', 'code'], 'string', 'max' => 45]
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
            'list_id' => 'List ID',
            'email' => 'Email',
            'person_id' => 'Person ID',
            'message' => 'Message',
            'code' => 'Code',
            'dt_create' => 'Dt Create',
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
        return $this->hasOne(AgencyCsvBatch::className(), ['id' => 'batch_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getList()
    {
        return $this->hasOne(UnisenderList::className(), ['id' => 'list_id']);
    }
}
