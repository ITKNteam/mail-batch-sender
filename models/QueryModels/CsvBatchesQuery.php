<?php

namespace app\models\QueryModels;
use Yii;
use app\models\UserAvailableCityes;
use yii\helpers\ArrayHelper;
/**
 * This is the ActiveQuery class for [[\app\models\CsvBatches]].
 *
 * @see \app\models\CsvBatches
 */
class CsvBatchesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \app\models\CsvBatches[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\CsvBatches|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
    
    public function UserAvailableCityes(){
        
         $cityes =   UserAvailableCityes::find()->where(['user_id'=>Yii::$app->user->id])->select(['city'])->asArray()->all();
        
         return $this->andWhere(['in',  'activity_loc', ArrayHelper::getColumn($cityes, 'city')]);
    }
        
    
    
    public function ListAvailableCityes($list_id){
        
         $cityes =   UnisenderListAvailableCityes::find()->where(['list_id'=>$list_id])->select(['city'])->asArray()->all();
        
         return $this->andWhere(['in',  'activity_loc', ArrayHelper::getColumn($cityes, 'city')]);
    }
}