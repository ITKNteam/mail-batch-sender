<?php

namespace app\models\QueryModels;
use yii\helpers\ArrayHelper;

/**
 * This is the ActiveQuery class for [[AgencySettings]].
 *
 * @see AgencySettings
 */
class AgencySettingsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return AgencySettings[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return AgencySettings|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
    
     public function AvailableGroups(){
        
         $groups =   ['4','5'];
        
         return $this->andWhere(['in',  'group_id', $groups]);
    }
    
    public function AvailableParams(){
        
         $sys_parametrs = \app\models\SysParametr::find()->select('id, name')->AvailableGroups()->all();
        
         return $this->andWhere(['in',  'sys_parametr_id', ArrayHelper::getColumn($sys_parametrs, 'id')]);
    }
}