<?php

namespace app\models\QueryModels;

/**
 * This is the ActiveQuery class for [[\app\models\UnisenderListAvailableCityes]].
 *
 * @see \app\models\UnisenderListAvailableCityes
 */
class UnisenderListAvailableCityesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \app\models\UnisenderListAvailableCityes[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\UnisenderListAvailableCityes|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}