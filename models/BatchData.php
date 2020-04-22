<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "batch_data".
 *
 * @property integer $id
 * @property integer $batch_id
 * @property integer $sys_parametr_id
 * @property integer $string_order
 * @property string $value
 *
 * @property AgencyCsvBatch $batch
 * @property SysParametr $sysParametr
 */
class BatchData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    
    public $param_name = '';
    public $sys_name = '';
    public $batch_date = '';
    public static function tableName()
    {
        return 'batch_data';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['batch_id', 'agency_parametr_id', 'string_order'], 'integer'],
            [['value','param_name','sys_name'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'batch_id' => 'Batch ID',
            'agency_parametr_id' => 'Agency Parametr ID',
            'string_order' => 'String Order',
            'value' => 'Value',
        ];
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
    public function getAgencyParametr()
    {
        return $this->hasOne(AgencySettings::className(), ['id' => 'agency_parametr_id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\QueryModels\BatchDataQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\QueryModels\BatchDataQuery(get_called_class());
    }
}
