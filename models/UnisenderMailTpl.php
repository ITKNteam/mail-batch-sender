<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "unisender_mail_tpl".
 *
 * @property integer $id
 * @property integer $agency_id
 * @property string $mail_subject
 * @property string $mail_body
 * @property integer $group_id
 * @property integer $campaign_id
 * @property integer $status_id
 * @property string $dt_create
 * @property integer $uid_create
 *
 * @property Agency $agency
 * @property AgencySettings $group
 * @property AgencySettings $campaign
 * @property AgencySettings $status
 * @property UnisenderMailTplFileds[] $unisenderMailTplFileds
 */
class UnisenderMailTpl extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'unisender_mail_tpl';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['agency_id', 'group_id', 'campaign_id', 'status_id', 'uid_create'], 'integer'],
            [['mail_body'], 'string'],
            [['dt_create'], 'safe'],
            [['mail_body'],'required'],
            [['mail_subject'], 'string', 'max' => 250]
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
            'mail_subject' => 'Заголовок письма',
            'mail_body' => 'Тело письма (данный текст будет использован в поле подстановки)',
            'group_id' => 'Группа шаблона',
            'campaign_id' => 'Промо кампания',
            'status_id' => 'Status ID',
            'dt_create' => 'Dt Create',
            'uid_create' => 'Uid Create',
        ];
    }
    
//    public static function getMailGroupNames(){
//        $ret = [];
//        $m = static::find()->with('group')->all();
//        foreach ($m as $row)
//            $ret[$row['id']]= $row->group['value'];
//        
//        return $ret;
//    
//    
//    }
    public static function getMailGroupNames(){
        $ret = [];
        //$m = static::find()->with('campaign')->select('campaign_id')->all();
        $m = AgencySettings::find()->where(['sys_parametr_id'=>27])->all();
        foreach ($m as $row)
            $ret[$row['id']]= $row['value'];
        
        return $ret;
    
    
    }
    
    public static function getCampaignNames(){
        $ret = [];
        //$m = static::find()->with('campaign')->select('campaign_id')->all();
        $m = AgencySettings::find()->where(['sys_parametr_id'=>28])->all();
        foreach ($m as $row)
            $ret[$row['id']]= $row['value'];
        
        return $ret;
    
    
    }
    
    
    
    public static function getMailGroupName($id){
            
        $model = static::find()->where(['id'=>$id])->with('group')->one();
        return $model->group['value'];
    }
    
    
    public static function getCampaignName($id){
        if ($id){ 
            $m = AgencySettings::find()->where(['id'=>$id])->one();
            //$model = static::find()->where(['id'=>$id])->with('campaign')->one();
            return @$m['value'];
        }
        return 'не определено';
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
    public function getGroup()
    {
        return $this->hasOne(AgencySettings::className(), ['id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCampaign()
    {
        return $this->hasOne(AgencySettings::className(), ['id' => 'campaign_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(AgencySettings::className(), ['id' => 'status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnisenderMailTplFileds()
    {
        return $this->hasMany(UnisenderMailTplFileds::className(), ['u_mail_tpl_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return \app\models\QueryModels\UnisenderMailTplQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\QueryModels\UnisenderMailTplQuery(get_called_class());
    }
}
