<?php

namespace app\models\QueryModels;

/**
 * This is the ActiveQuery class for [[AgencyCsvBatch]].
 *
 * @see AgencyCsvBatch
 */
class AgencyCsvBatchQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return AgencyCsvBatch[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return AgencyCsvBatch|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}