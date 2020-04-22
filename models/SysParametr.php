<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sys_parametr".
 *
 * @property integer $id
 * @property string $name
 * @property string $sys_name
 * @property string $data_type
 * @property integer $group_id
 *
 * @property AgencySettings[] $agencySettings
 * @property BatchData[] $batchDatas
 * @property SysParametrGroup $group
 */
class SysParametr extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sys_parametr';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_id'], 'integer'],
            [['name', 'sys_name', 'data_type'], 'string', 'max' => 45]
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
            'sys_name' => 'Sys Name',
            'data_type' => 'Data Type',
            'group_id' => 'Group ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgencySettings()
    {
        return $this->hasMany(AgencySettings::className(), ['sys_parametr_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBatchDatas()
    {
        return $this->hasMany(BatchData::className(), ['sys_parametr_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(SysParametrGroup::className(), ['id' => 'group_id']);
    }

    /**
     * @inheritdoc
     * @return SysParametrQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\QueryModels\SysParametrQuery(get_called_class());
    }
    
    
    public static function getList(){
        $ret = [];
        $a = static::find()->select('id, name')->AvailableGroups()->all();
        foreach ($a as $row)
            $ret[$row['id']]= $row['name'];
        
        return $ret;
        
    }
     public static function getName($id){
            
        $model = static::find()->where(['id'=>$id])->one();
        return $model['name'];
    }
}
