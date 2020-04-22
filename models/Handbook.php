<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "handbook".
 *
 * @property integer $id
 * @property integer $hand_name_id
 * @property string $value
 * @property string $trl_value
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 *
 * @property HandbookName $handName
 */
class Handbook extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'handbook';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hand_name_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['value', 'trl_value'], 'string', 'max' => 120]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hand_name_id' => 'Hand Name ID',
            'value' => 'Value',
            'trl_value' => 'Trl Value',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHandName()
    {
        return $this->hasOne(HandbookName::className(), ['id' => 'hand_name_id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\QueryModels\HandbookQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\QueryModels\HandbookQuery(get_called_class());
    }
    
     public static function getValueList($hndbook_id, $is_trl = 0){
        
        $list = [];
        $handbook = static::find()->where(['hand_name_id'=>$hndbook_id])
                ->orderBy(['id' => SORT_ASC])
                ->all();
        
        if ($is_trl){
            foreach ($handbook as $row){
                $list[$row->id]= $row->trl_value;
            }
        } else {
            foreach ($handbook as $row){
                $list[$row->id]= $row->value;
            }
        }
        
        return $list;
        
    }
     public static function getValueListKey($hndbook_id, $is_trl = 0){
        
        $list = [];
        $handbook = static::find()->where(['hand_name_id'=>$hndbook_id])
                ->orderBy(['id' => SORT_ASC])
                ->all();
        
        if ($is_trl){
            foreach ($handbook as $row){
                $list[$row->trl_value]= $row->trl_value;
            }
        } else {
            foreach ($handbook as $row){
                $list[$row->value]= $row->value;
            }
        }
        
        return $list;
        
    }
    
    public static function getValue($id, $is_trl = 0){
        
        $hndb_val = static::find()->where(['id'=>$id])->one();
        
        if ($is_trl){
            return @$hndb_val->trl_value;
        }
        return @$hndb_val->value;
        
    }
    
}
