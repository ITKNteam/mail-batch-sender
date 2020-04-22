<?php

namespace app\models\QueryModels;

/**
 * This is the ActiveQuery class for [[\app\models\UserAvailableCityes]].
 *
 * @see \app\models\UserAvailableCityes
 */
class UserAvailableCityesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \app\models\UserAvailableCityes[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\UserAvailableCityes|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}