<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "agency_settings".
 *
 * @property integer $id
 * @property integer $agency_id
 * @property integer $sys_parametr_id
 * @property string $value
 *
 * @property Agency $agency
 * @property SysParametr $sysParametr
 */
class AgencySettings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'agency_settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['agency_id', 'sys_parametr_id', 'row_order'], 'integer'],
            [['value'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agency_id' => 'Agency ID',
            'sys_parametr_id' => 'Sys Parametr ID',
            'value' => 'Value',
            'row_order' => 'Порядок',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgency()
    {
        return $this->hasOne(Agency::className(), ['id' => 'agency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSysParametr()
    {
        return $this->hasOne(SysParametr::className(), ['id' => 'sys_parametr_id']);
        
    }

    /**
     * @inheritdoc
     * @return AgencySettingsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\QueryModels\AgencySettingsQuery(get_called_class());
    }
    
    public static function getValue($id){
        $model = static::find()->where(['id'=>$id])->one();
        return $model['value'];
    }
    
    public static function getSysName($id){
        $model = static::find()->where(['id'=>$id])->with('sysParametr')->one();
        return $model->sysParametr['name'];
    }
    
    
     public static function getParamName($agency_id=1, $group_id = 3){
        $ret = [];
        $RowsTemplate = (new \yii\db\Query())
            ->select(['agency_settings.id', 'sys_parametr.name'])
            ->from('agency_settings')
            ->join('LEFT JOIN', 'sys_parametr', 'sys_parametr.id = agency_settings.sys_parametr_id')
            ->where(['sys_parametr.group_id' => $group_id])
            ->orderBy('row_order')      
            ->all();
        
//        $m = static::find()->where(['agency_id'=>$agency_id])
//                ->with('sysParametr')->all();
        foreach ($RowsTemplate as $row)
            $ret[$row['id']]= $row['name'];
        
        return $ret;
    
    
    }
    
    public static function getAgencyParamValue($agency_id, $sys_name ){
     $RowsTemplate = (new \yii\db\Query())
            ->select(['agency_settings.id','agency_settings.value', 'sys_parametr.name'])
            ->from('agency_settings')
            ->join('LEFT JOIN', 'sys_parametr', 'sys_parametr.id = agency_settings.sys_parametr_id')
            ->where(['sys_parametr.sys_name' => $sys_name])
            ->andWhere(['agency_settings.agency_id' => $agency_id])
            ->orderBy('row_order')      
            ->one(); 
     return $RowsTemplate['value'];
    }
    
    
    public static function getApiKey($agency_id){
        
     $sys_name='agency_unisender_api';
     $api_key = AgencySettings::getAgencyParamValue($agency_id, $sys_name);
     
     return $api_key;
    }
    
    public static function getAgencyParamKey($agency_id, $sys_name ){
     $RowsTemplate = (new \yii\db\Query())
            ->select(['agency_settings.id','agency_settings.value', 'sys_parametr.name'])
            ->from('agency_settings')
            ->join('LEFT JOIN', 'sys_parametr', 'sys_parametr.id = agency_settings.sys_parametr_id')
            ->where(['sys_parametr.sys_name' => $sys_name])
            
            ->andWhere(['agency_settings.agency_id' => $agency_id])
            ->orderBy('row_order')      
            ->one(); 
     return $RowsTemplate['id'];
    }
    
    
    
            
    
}
