<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "compare_data_email".
 *
 * @property integer $id
 * @property integer $agency_id
 * @property integer $param_id
 * @property string $value
 * @property integer $mail_tpl_id
 * @property string $dt_create
 * @property integer $uid_create
 *
 * @property Agency $agency
 * @property AgencySettings $param
 * @property Users $uidCreate
 * @property UnisenderMailTpl $mailTpl
 */
class CompareDataEmail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'compare_data_email';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['agency_id', 'param_id', 'mail_tpl_id', 'uid_create','campaign_id'], 'integer'],
            [['dt_create'], 'safe'],
            [['param_id','value'],'required'],
            [['value'], 'string', 'max' => 450]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agency_id' => 'Агентсво',
            'param_id' => 'Поле в CSV файле',
            'value' => 'значение для сравнения',
            'mail_tpl_id' => 'Группа шаблона, которая будет использована для данного значения',
            'dt_create' => 'Dt Create',
            'uid_create' => 'Uid Create',
            'campaign_id'=>' Промо кампания'
        ];
    }

    public static function getData($value, $param_id, $agency_id, $campaign_id ){
      
        $d = static::find()->where(['agency_id'=>$agency_id,
                                    'param_id'=>$param_id,    
                                    'value'=>$value,
                                    'campaign_id'=>$campaign_id
                                    ])->with('mailTpl')->one();
        
        
        if(!$d){
            
          return ['personal_message'=>'',
                 'britain_percent'=>   ''
                    ];
            
        }
         
        $new_field_name = 'britain_percent';
        $brit = UnisenderMailTplFileds::find()->select('value')
                               ->where(['u_mail_tpl_id'=>$d->mail_tpl_id])
                               ->andWhere(['f.agency_id'=>$agency_id])
                               ->andWhere(['f.new_field_name'=>$new_field_name])
                            ->leftJoin('unisender_fields f', 'u_field_id = f.id')
                            ->one();
        
         return ['personal_message'=>$d->mailTpl['mail_body'],
                 'britain_percent'=>   $brit['value']
                    ];
                
        
    }
    public static function getData2($value, $param_id, $agency_id, $campaign_id ){
      
        $d = static::find()->where(['agency_id'=>$agency_id,
                                    'param_id'=>$param_id,    
                                    'value'=>$value,
                                    'campaign_id'=>$campaign_id
                                    ])->with('mailTpl')->one();
        
        
        if(!$d){
            
          return ['personal_message'=>'',
                 'britain_percent'=>   ''
                    ];
            
        }
         
        $new_field_name = 'britain_percent';
        $brit = UnisenderMailTplFileds::find()->select('value')
                               ->where(['u_mail_tpl_id'=>$d->mail_tpl_id])
                               ->andWhere(['f.agency_id'=>$agency_id])
                               ->andWhere(['f.new_field_name'=>$new_field_name])
                            ->leftJoin('unisender_fields f', 'u_field_id = f.id')
                            ->one();
        
         return ['personal_message'=>$d->mailTpl['mail_body'],
                 'britain_percent'=>   $brit['value']
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
    public function getParam()
    {
        return $this->hasOne(AgencySettings::className(), ['id' => 'param_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUidCreate()
    {
        return $this->hasOne(Users::className(), ['id' => 'uid_create']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMailTpl()
    {
        return $this->hasOne(UnisenderMailTpl::className(), ['id' => 'mail_tpl_id']);
    }
    public function getUnisenderMailTplFileds()
    {
        return $this->hasOne(UnisenderMailTplFileds::className(), ['u_mail_tpl_id' => 'mail_tpl_id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\QueryModels\CompareDataEmailQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\QueryModels\CompareDataEmailQuery(get_called_class());
    }
}
