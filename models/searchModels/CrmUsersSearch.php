<?php

namespace app\models\searchModels;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CrmUsers;
use app\models\User;

/**
 * usersSearch represents the model behind the search form about `app\models\User`.
 */
class CrmUsersSearch extends CrmUsers
{
   
    
     public $activity_dt_st = null;
    public $activity_dt_fn = null;
   
    public $dt_load_st = null;
    public $dt_load_fn = null;
    
    public $agency_id = null;
    public $list_id = null;
    
    public $campaign_id = null;
    public $activity_loc_ar = null; 
    public $hostess_name_ar = [];
    public $validate_status_ar= null;
    public $gender_ar = null;

    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['batch_id', 'row_id', 'test_id', 'activity_type', 'activity_id', 'rec_status', 'unisender_letter_id'], 'integer'],
            [['last_dt', 'unisender_last_update','campaign_id'], 'safe'],
            [['email', 'advanced_data', 'user_key'], 'string', 'max' => 450],
            [['last_externalId',], 'string', 'max' => 245],
            [['phone', 'f_name', 'l_name', 'p_name', 'age', 'gender', 'priority_mark1', 'priority_mark2', 'hostess_id', 'activity_dt', 'activity_loc', 'test_res', 'unisender_send_result'], 'string', 'max' => 45]
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
    public function search($params)
    {
        //$query = CrmUsers::find()->where(['batch_id'=>$batch_id]);
        
        if (User::getRoleName()  == 'Administrator'){
            
            $query = CrmUsers::find()->with(['batch']);  
            
            
         }
         if (User::getRoleName()  == 'Manager'){
           $query = CrmUsers::find()->UserAvailableCityes()->with('batch');     
         }
        
            
        
        
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


        
//        $campaign_id = $this->campaign_id;
//            $batchs_id = \app\models\AgencyCsvBatch::find()
//                    ->where(['campaign_id'=> $campaign_id])
//                    ->select(['id'])
//                    ->asArray()
//                    ->all();
            
         //   $batchs_id = ['161','160'];
        
          $query->andFilterWhere(['like', 'f_name', $this->f_name])
             ->andFilterWhere(['like', 'l_name', $this->l_name])       
             ->andFilterWhere(['like', 'batch_id', $this->batch_id])       
             ->andFilterWhere(['like', 'email', $this->email])       
             // ->andFilterWhere(['not like', 'is_have_pic', '0']);
             // ->andFilterWhere(['like', 'activity_dt', $this->activity_dt])
              ->andFilterWhere(['between', 'activity_dt', $this->activity_dt_st, $this->activity_dt_fn])
              ->andFilterWhere(['like', 'gender', $this->gender])
              ->andFilterWhere(['like', 'activity_loc', $this->activity_loc])
             // ->andWhere(['in', 'batch_id', \yii\helpers\ArrayHelper::getColumn($batchs_id, 'id')])
                  ->andFilterWhere(['like', 'rec_status', $this->rec_status])
              //->andFilterWhere(['like', 'batch.campaign_id', $this->campaign_id])
              ->andFilterWhere(['like', 'hostess_name', $this->hostess_name]);
//               ->andFilterWhere(['not in', 'user_id', ArrayHelper::getColumn($winers_id, 'user_id')]);
//              ->andFilterWhere(['like', 'statusId', '1'])
//              ->andFilterWhere(['like', 'is_finalist', '1']);
           //  ->andFilterWhere(['EXISTS', 'user_pic'] );
          //  ->filterWhere(['user_pic'=>!null] );
        
        
        return $dataProvider;
    }
}
