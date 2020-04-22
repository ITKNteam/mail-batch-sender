<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tmp_160".
 *
 * @property integer $id
 * @property string $f_name
 * @property string $l_name
 * @property string $email
 * @property string $status
 * @property string $detail
 */
class Tmp160 extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tmp_160';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['f_name', 'l_name', 'email', 'detail'], 'string', 'max' => 450],
            [['status'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'f_name' => 'F Name',
            'l_name' => 'L Name',
            'email' => 'Email',
            'status' => 'Status',
            'detail' => 'Detail',
        ];
    }
}
