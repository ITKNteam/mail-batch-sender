<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "handbook_name".
 *
 * @property integer $id
 * @property string $name
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 *
 * @property Handbook[] $handbooks
 */
class HandbookName extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'handbook_name';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
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
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHandbooks()
    {
        return $this->hasMany(Handbook::className(), ['hand_name_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\QueryModels\HandbookNameQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\QueryModels\HandbookNameQuery(get_called_class());
    }
}
