<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "unisender_list_available_cityes".
 *
 * @property integer $id
 * @property integer $unisender_list_id
 * @property string $city
 *
 * @property UnisenderList $unisenderList
 */
class UnisenderListAvailableCityes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'unisender_list_available_cityes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'unisender_list_id'], 'integer'],
            [['city'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'unisender_list_id' => 'Unisender List ID',
            'city' => 'City',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnisenderList()
    {
        return $this->hasOne(UnisenderList::className(), ['id' => 'unisender_list_id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\QueryModels\UnisenderListAvailableCityesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\QueryModels\UnisenderListAvailableCityesQuery(get_called_class());
    }
}
