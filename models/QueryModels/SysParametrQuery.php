<?php

namespace app\models\QueryModels;

/**
 * This is the ActiveQuery class for [[SysParametr]].
 *
 * @see SysParametr
 */
class SysParametrQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return SysParametr[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return SysParametr|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
    
    public function AvailableGroups(){
        
         $groups =   ['4','5'];
        
         return $this->andWhere(['in',  'group_id', $groups]);
    }
}