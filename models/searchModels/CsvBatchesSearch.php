<?php
namespace app\models\searchModels;


use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CsvBatches;
use app\models\User;


/**
 * usersSearch represents the model behind the search form about `app\models\User`.
 */
class CsvBatchesSearch extends CsvBatches
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
            [['batch_id', 'user_id', 'status_id', 'test_id', 'campaign_id'], 'integer'],
            [['dt_load', 'age', 'activity_dt','activity_dt_st','activity_dt_fn', 
                'dt_load_st','dt_load_fn','activity_loc', 'activity_loc_ar',
                'hostess_name_ar','validate_status_ar'], 'safe'],
            [['test_res', 'advanced_data'], 'string'],
            [['f_name', 'l_name', 'p_name', 'email'], 'string', 'max' => 450],
            [['phone', 'gender', 'hostess_id', 'activity_loc', 'activity_type', 'activity_id','validate_status'], 'string', 'max' => 45],
            [['priority_mark1', 'priority_mark2'], 'string', 'max' => 90],
            [['hostess_name'], 'string', 'max' => 100]
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
        
//     $query = User::find()->HavePicture()->HavePoints()->IsWoman()->IsActive()->orderBy(['likes_count' => SORT_DESC, 'id' => SORT_ASC]);

        if (User::getRoleName()  == 'Administrator'){
           $query = CsvBatches::find()
                   ->with(['batch']);     
         }
         if (User::getRoleName()  == 'Manager'){
           $query = CsvBatches::find()->UserAvailableCityes()->with(['batch']);     
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
//            'f_name' => $this->f_name,
//            'l_name' => $this->l_name,
//            'activity_loc' => $this->activity_loc,
//          
//        ]);
//        
//        $winers_id  = \app\models\UsersCert::find()->select('user_id')
//                                 ->asArray()->all();

        
         if ($this->campaign_id){
            $campaign_id = $this->campaign_id;
            $batchs_id = \app\models\AgencyCsvBatch::find()
                    ->where(['campaign_id'=> $campaign_id])
                    ->select(['id'])
                    ->asArray()
                    ->all();
            
            
               $query->andFilterWhere(['like', 'f_name', $this->f_name])
            // ->andFilterWhere(['like', 'l_name', $this->l_name])       
  //           ->andFilterWhere(['like', 'batch_id', $this->batch_id])
             ->andFilterWhere(['in', 'batch_id', \yii\helpers\ArrayHelper::getColumn($batchs_id, 'id')])
                  ->andFilterWhere(['like', 'email', $this->email])       
             
              ->andFilterWhere(['between', 'dt_load', $this->dt_load_st, $this->dt_load_fn])
              ->andFilterWhere(['between', 'activity_dt', $this->activity_dt_st, $this->activity_dt_fn])
              
                      ->andFilterWhere(['in', 'activity_loc', $this->activity_loc_ar])     
                  //   ->andFilterWhere(['in', 'hostess_name', $this->hostess_name_ar])     
                      ->andFilterWhere(['in', 'gender', $this->gender_ar])
                      ->andFilterWhere(['in', 'validate_status', $this->validate_status_ar]);
                        
            
            
         } else {
                $query->andFilterWhere(['like', 'f_name', $this->f_name])
             ->andFilterWhere(['like', 'l_name', $this->l_name])       
             ->andFilterWhere(['like', 'batch_id', $this->batch_id])
             
             ->andFilterWhere(['like', 'email', $this->email])       
              ->andFilterWhere(['between', 'dt_load', $this->dt_load_st, $this->dt_load_fn])
              ->andFilterWhere(['between', 'activity_dt', $this->activity_dt_st, $this->activity_dt_fn])
                        ->andFilterWhere(['in', 'validate_status', $this->validate_status_ar])     
                        ->andFilterWhere(['in', 'activity_loc', $this->activity_loc_ar])     
                       // ->andFilterWhere(['in', 'hostess_name', $this->hostess_name_ar])     
                       ->andFilterWhere(['in', 'gender', $this->gender_ar])     
             
              ->andFilterWhere(['like', 'hostess_name', $this->hostess_name]);
             
         }  
        
        
     
//               ->andFilterWhere(['not in', 'user_id', ArrayHelper::getColumn($winers_id, 'user_id')]);
//              ->andFilterWhere(['like', 'statusId', '1'])
//              ->andFilterWhere(['like', 'is_finalist', '1']);
           //  ->andFilterWhere(['EXISTS', 'user_pic'] );
          //  ->filterWhere(['user_pic'=>!null] );

        return $dataProvider;
    }
    
    
    
    
    public static function getBatchCityList($batch_id){
        
         
        $list = [];
        $handbook = CsvBatches::find()->select('activity_loc')
                ->distinct('activity_loc')
                ->where(['batch_id'=>$batch_id])
                ->orderBy(['id' => SORT_ASC])
                ->all();
        
       
            foreach ($handbook as $row){
                $list[$row->activity_loc]= $row->activity_loc;
            
        }
        
        return $list;
        
        
    }
    public static function getBatchHostesList($batch_id=0){
        
         
        $list = [];
        
        if ($batch_id){
            $handbook = CsvBatches::find()->select('hostess_name')
                    ->distinct('hostess_name')
                    ->where(['batch_id'=>$batch_id])
                    ->orderBy(['id' => SORT_ASC])
                    ->all();
        } else {
            $handbook = CsvBatches::find()->select('hostess_name')
                    ->distinct('hostess_name')
                    ->orderBy(['id' => SORT_ASC])
                    ->all();
        }
       
            foreach ($handbook as $row){
                $list[$row->hostess_name]= $row->hostess_name;
            
        }
        
        return $list;
        
        
    }
    
    
    
    public static function getValidateStatusName($validate_status){
//            when validate_status is null then 'нет email'
//            when validate_status =null then 'нет email'
//            when validate_status ='passed' then 'Валидные email'
//             when validate_status ='failed' then 'Не валидные email'
//             when validate_status ='unprocessed' then 'Не валидные email'
//             when validate_status ='error' then 'Не валидные email'
//             else 'нет email'
        
        
        $validate_status_name = 'Нет email';
                
        switch ($validate_status) {
            case null:
                $validate_status_name = 'нет email';            
                 break;
            case 'failed':
            case 'unprocessed':
                $validate_status_name = 'Не валидные email';
                break;
            case 'error':
            case 'unknown':  
                $validate_status_name = 'Валидируется';
                break;
            case 'passed':
                $validate_status_name = 'Валидные email';
                break;
            case 'no_email':
                $validate_status_name = 'Нет email';
                break;
            
          
            }
                    
           return $validate_status_name;    
        
    }
    
    
    
    public static function getValidatStatusNameArr() {
          return ['passed'=> 'Валидные',
                  'failed'=> 'Невалидные',
                  'unknown'=>'Валидируется',
                  'no_email'=> 'Нет email',
                  ];
    }
}
