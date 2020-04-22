<?php

namespace app\models\QueryModels;

/**
 * This is the ActiveQuery class for [[\app\models\CrmUsers]].
 *
 * @see \app\models\CrmUsers
 */
class CrmUsersQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \app\models\CrmUsers[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\CrmUsers|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}