<?php

namespace app\models\QueryModels;

/**
 * This is the ActiveQuery class for [[\app\models\CronTask]].
 *
 * @see \app\models\CronTask
 */
class CronTaskQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \app\models\CronTask[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\CronTask|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}