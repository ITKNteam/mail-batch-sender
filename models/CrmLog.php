<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "crm_log".
 *
 * @property integer $id
 * @property integer $crm_user_id
 * @property string $method_name
 * @property string $failedCauseOf
 * @property integer $isSuccessfully
 * @property string $details
 * @property string $entity
 * @property string $action_dt
 *
 * @property CrmUsers $crmUser
 */
class CrmLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'crm_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['crm_user_id', 'isSuccessfully'], 'integer'],
            [['details', 'entity'], 'string'],
            [['action_dt'], 'safe'],
            [['method_name'], 'string', 'max' => 450],
            [['externalId'], 'string', 'max' => 245],
            [['failedCauseOf'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'crm_user_id' => 'Crm User ID',
            'method_name' => 'Method Name',
            'failedCauseOf' => 'Failed Cause Of',
            'isSuccessfully' => 'Is Successfully',
            'details' => 'Details',
            'entity' => 'Entity',
            'action_dt' => 'Action Dt',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCrmUser()
    {
        return $this->hasOne(CrmUsers::className(), ['id' => 'crm_user_id']);
    }
}
