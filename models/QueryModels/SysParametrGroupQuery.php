<?php

namespace app\models\QueryModels;

/**
 * This is the ActiveQuery class for [[SysParametrGroup]].
 *
 * @see SysParametrGroup
 */
class SysParametrGroupQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return SysParametrGroup[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return SysParametrGroup|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}