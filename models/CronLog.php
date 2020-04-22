<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cron_log".
 *
 * @property integer $id
 * @property string $dt_operation
 * @property string $res
 */
class CronLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cron_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dt_operation'], 'safe'],
            [['res'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dt_operation' => 'Dt Operation',
            'res' => 'Res',
        ];
    }
    
    
    public static function saveLog($message=''){
        $model = new CronLog();
        
        $model->res = $message;
        $model->dt_operation = date('Y-m-d H:i:s');
        $model->save();
        return 1;
        
    }
}
