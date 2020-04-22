<?php

namespace app\models\QueryModels;

/**
 * This is the ActiveQuery class for [[\app\models\UnisenderContacts]].
 *
 * @see \app\models\UnisenderContacts
 */
class UnisenderContactsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \app\models\UnisenderContacts[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\UnisenderContacts|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}