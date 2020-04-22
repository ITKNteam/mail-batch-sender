<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "email_template".
 *
 * @property integer $id
 * @property string $name
 * @property string $email_from_name
 * @property string $email_from_email
 * @property string $email_subject
 * @property string $email_text
 * @property string $preview_pic
 * @property integer $status_id
 *
 * @property AgencyCsvBatch[] $agencyCsvBatches
 */
class EmailTemplate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'email_template';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email_text'], 'string'],
            [['status_id'], 'integer'],
            [['name'], 'string', 'max' => 45],
            [['email_from_name', 'email_from_email', 'email_subject', 'preview_pic'], 'string', 'max' => 450]
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
            'email_from_name' => 'Email From Name',
            'email_from_email' => 'Email From Email',
            'email_subject' => 'Email Subject',
            'email_text' => 'Email Text',
            'preview_pic' => 'Preview Pic',
            'status_id' => 'Status ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgencyCsvBatches()
    {
        return $this->hasMany(AgencyCsvBatch::className(), ['email_tpl_id' => 'id']);
    }
}
