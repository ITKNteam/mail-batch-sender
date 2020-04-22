<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_available_cityes".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $city
 *
 * @property Users $user
 */
class UserAvailableCityes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_available_cityes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['city'], 'string', 'max' => 90]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'city' => 'City',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\QueryModels\UserAvailableCityesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\QueryModels\UserAvailableCityesQuery(get_called_class());
    }
}
