<?php

namespace app\models\searchModels;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AgencySettings;

/**
 * AgencySettingsSearch represents the model behind the search form about `app\models\AgencySettings`.
 */
class AgencySettingsSearch extends AgencySettings
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'agency_id', 'sys_parametr_id', 'row_order'], 'integer'],
            [['value'], 'safe'],
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
        $query = AgencySettings::find()->AvailableParams();

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
            'agency_id' => $this->agency_id,
            'sys_parametr_id' => $this->sys_parametr_id,
            'row_order' => $this->row_order,
        ]);

        $query->andFilterWhere(['like', 'value', $this->value]);

        return $dataProvider;
    }
}
