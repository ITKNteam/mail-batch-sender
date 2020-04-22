<?php

namespace app\models\searchModels;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UnisenderDeliveryStatus;

/**
 * usersSearch represents the model behind the search form about `app\models\User`.
 */
class UnisenderDeliveryStatusSearch extends UnisenderDeliveryStatus
{
    /**
     * @inheritdoc
     */
   public function rules()
    {
        return [
            [['campaign_id', 'letter_id'], 'integer'],
            [['last_update', 'send_result'], 'safe'],
            [['email'], 'string', 'max' => 300],
            [['send_result'], 'string', 'max' => 100],
            
        ];
    }


    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $campaign_id)
    {
        $query = UnisenderDeliveryStatus::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

//        $query->andFilterWhere([
 //           'first_name' => $this->first_name,
//            'last_name' => $this->last_name,
////            'statusId' => $this->statusId,
////            'reg_date' => $this->reg_date,
////            'vk_id' => $this->vk_id,
////            'fb_id' => $this->fb_id,
////            'ok_id' => $this->ok_id,
////            'last_login' => $this->last_login,
////            'points_balance' => $this->points_balance,
////            'sex' => $this->sex,
////            'offerta_accept' => $this->offerta_accept,
////            'is_active' => $this->is_active,
////            'likes_count' => $this->likes_count,
////            'points_coefficent' => $this->points_coefficent,
////            'wrong_atempt' => $this->wrong_atempt,
////            'locked_by_time' => $this->locked_by_time,
 //       ]);

        $query->andFilterWhere(['like', 'email', $this->email])
              ->andFilterWhere(['like', 'campaign_id', $campaign_id])
              ->andFilterWhere(['like', 'send_result', $this->send_result] );
          //  ->filterWhere(['user_pic'=>!null] );

        return $dataProvider;
    }
}
