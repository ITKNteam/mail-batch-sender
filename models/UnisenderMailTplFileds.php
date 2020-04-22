<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "unisender_mail_tpl_fileds".
 *
 * @property integer $id
 * @property integer $u_mail_tpl_id
 * @property integer $u_field_id
 * @property string $value
 * @property string $dt_create
 * @property integer $uid_create
 *
 * @property UnisenderMailTpl $uMailTpl
 * @property UnisenderFields $uField
 * @property Users $uidCreate
 */
class UnisenderMailTplFileds extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'unisender_mail_tpl_fileds';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['u_mail_tpl_id', 'u_field_id', 'uid_create'], 'integer'],
            [['u_field_id','value'],'required'],
            [['value'], 'string'],
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
            'u_mail_tpl_id' => 'U Mail Tpl ID',
            'u_field_id' => 'Название дополнительного поля (поле подстановки)',
            'value' => 'Value',
            
            'dt_create' => 'Dt Create',
            'uid_create' => 'Uid Create',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUMailTpl()
    {
        return $this->hasOne(UnisenderMailTpl::className(), ['id' => 'u_mail_tpl_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUField()
    {
        return $this->hasOne(UnisenderFields::className(), ['id' => 'u_field_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUidCreate()
    {
        return $this->hasOne(User::className(), ['id' => 'uid_create']);
    }

    /**
     * @inheritdoc
     * @return \app\models\QueryModels\UnisenderMailTplFiledsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\QueryModels\UnisenderMailTplFiledsQuery(get_called_class());
    }
}
