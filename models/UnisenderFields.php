<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "unisender_fields".
 *
 * @property integer $id
 * @property integer $agency_id
 * @property string $new_field_name
 * @property string $new_field_type
 * @property integer $new_field_id
 * @property string $dt_create
 * @property integer $uid_create
 *
 * @property Agency $agency
 * @property Users $uidCreate
 */
class UnisenderFields extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'unisender_fields';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['agency_id', 'new_field_id', 'uid_create'], 'integer'],
            [['dt_create'], 'safe'],
            [['new_field_name'], 'string', 'max' => 450],
            [['new_field_type'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agency_id' => 'Agency ID',
            'new_field_name' => 'Название дополнительного поля (поле подстановки)',
            'new_field_type' => 'New Field Type',
            'new_field_id' => 'New Field ID',
            'dt_create' => 'Dt Create',
            'uid_create' => 'Uid Create',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgency()
    {
        return $this->hasOne(Agency::className(), ['id' => 'agency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUidCreate()
    {
        return $this->hasOne(Users::className(), ['id' => 'uid_create']);
    }

    /**
     * @inheritdoc
     * @return \app\models\QueryModels\UnisenderFieldsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\QueryModels\UnisenderFieldsQuery(get_called_class());
    }
    
     public static function newField($new_field_name, $new_field_type='text', $agency_id = 1){
        $answer = [];
        $api_key = AgencySettings::getApiKey($agency_id);
        $unisender_return = Yii::$app->unisender->createField($new_field_name, $new_field_type, $api_key);
        if (@$unisender_return['UnisenderAnswer']->result->id){
              $list = new UnisenderFields();
        
             $new_field_id = @$unisender_return['UnisenderAnswer']->result->id;
             $list->agency_id = $agency_id;
             $list->new_field_name = $new_field_name;
             $list->new_field_type = $new_field_type;
             $list->new_field_id = $new_field_id;
             //$list->before_subscribe_url = $this->before_subscribe_url;
            // $list->after_subscribe_url = $this->after_subscribe_url;
             $list->uid_create = Yii::$app->user->id;
             $list->dt_create = date("Y-m-d H:i:s");
             $list->save();
             $answer = ['id'=>$list['id'],
                         'message'=> 'Операция выполнена успешно!'    ];
            
         } else {
              $answer = ['id'=>0,
                        'message'=>  'Ошибка ! '.  @$unisender_return['UnisenderAnswer']->error];
         }
        
        
        return  $answer;
    }
    
    public static function getList(){
        $ret = [];
        $model = static::find()->select('id, new_field_name')->all();
        foreach ($model as $row)
            $ret[$row['id']]= $row['new_field_name'];
        
        return $ret;
    }
    
    
}
