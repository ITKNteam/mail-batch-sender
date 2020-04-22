<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sys_parametr_group".
 *
 * @property integer $id
 * @property string $name
 *
 * @property AgencyCsvBatch[] $agencyCsvBatches
 * @property SysParametr[] $sysParametrs
 */
class SysParametrGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_parametr_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgencyCsvBatches()
    {
        return $this->hasMany(AgencyCsvBatch::className(), ['sys_group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSysParametrs()
    {
        return $this->hasMany(SysParametr::className(), ['group_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return SysParametrGroupQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\QueryModels\SysParametrGroupQuery(get_called_class());
    }
}
