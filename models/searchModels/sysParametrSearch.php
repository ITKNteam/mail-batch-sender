<?php

namespace app\models\searchModels;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SysParametr;

/**
 * sysParametrSearch represents the model behind the search form about `app\models\SysParametr`.
 */
class sysParametrSearch extends SysParametr
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'group_id'], 'integer'],
            [['name', 'sys_name', 'data_type'], 'safe'],
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
        $query = SysParametr::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'group_id' => $this->group_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'sys_name', $this->sys_name])
            ->andFilterWhere(['like', 'data_type', $this->data_type]);

        return $dataProvider;
    }
}
