<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "unisender_delivery_status".
 *
 * @property integer $id
 * @property integer $campaign_id
 * @property integer $letter_id
 * @property string $email
 * @property string $send_result
 * @property string $last_update
 *
 * @property UnisenderCampaign $campaign
 */
class UnisenderDeliveryStatus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'unisender_delivery_status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['campaign_id', 'letter_id'], 'integer'],
            [['last_update'], 'safe'],
            [['email'], 'string', 'max' => 300],
            [['send_result'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'campaign_id' => 'Campaign ID',
            'letter_id' => 'Letter ID',
            'email' => 'Email',
            'send_result' => 'Результат сообщения',
            'last_update' => 'Last Update',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCampaign()
    {
        return $this->hasOne(UnisenderCampaign::className(), ['id' => 'campaign_id']);
    }
    
    
    
    public static function saveStatus($campaign_id,$agency_id){
        $c_id = UnisenderCampaign::find()->where("campaign_id = $campaign_id")->one();
        $local_campaign_id = $c_id['id'];
        
        $del = UnisenderDeliveryStatus::deleteAll("campaign_id = $local_campaign_id");
        
        $api_key = AgencySettings::getApiKey($agency_id);
        $res = Yii::$app->unisender->getCampaignDeliveryStats($campaign_id, $api_key);
        
 //       $c_id = UnisenderCampaign::findOne("campaign_id = $campaign_id");
        
        //$local_campaign_id = 1;
        
        $letter_id = $res['UnisenderAnswer']->result->letter_id;
//        [0] => email
//        [1] => send_result
//        [2] => last_update
        //[letter_id] => 37868870
        $dd = $res['UnisenderAnswer']->result->data;
        if($dd){
         foreach ($dd as $row){
           $data[]=[
                $local_campaign_id,
               $letter_id,
               $row['0'],
               $row['1'],
               $row['2'],
               
           ];  
             
         }
         
         
          $db = Yii::$app->db;
        $sql = $db->queryBuilder->batchInsert(UnisenderDeliveryStatus::tableName(), [
                    'campaign_id',
                    'letter_id',
                    'email',
                    'send_result',
                    'last_update',
                    ], $data);
        $res =  $db->createCommand($sql  )->execute();
        }
        return $res;
        
    }
    
    
    

    
    
      public static function DileveryStatusList($status){
        $status_list = [null=>'не определенно',
                        'not_sent'=>'Сообщение еще не было обработано. Также этот статус возвращается для отложенных для модерации сообщений.',
                        'ok_sent'=>'Сообщение было отправлено, промежуточный статус до получения ответа о доставке/недоставке.',
                        'ok_delivered'=>"Сообщение доставлено. Может измениться на 'ok_read', 'ok_link_visited', 'ok_unsubscribed' или 'ok_spam_folder'." ,
                        'ok_read'=>"Сообщение доставлено и зарегистрировано его прочтение. Может измениться на 'ok_link_visited', 'ok_unsubscribed' или 'ok_spam_folder'.",
                        'ok_fbl'=>'Сообщение доставлено, но помещено в папку "спам" получателем. К сожалению, редкие почтовые службы сообщают такую информацию, поэтому таких статусов обычно немного.',
                        'ok_link_visited'=>"Сообщение доставлено, прочитано и выполнен переход по одной из ссылок. Может измениться на 'ok_unsubscribed' или 'ok_spam_folder'.",
                        'ok_unsubscribed'=>'Сообщение доставлено и прочитано, но пользователь отписался по ссылке в письме. Статус окончательный.',
                        'err_user_unknown'=>'Адрес не существует, доставка не удалась. Статус окончательный.',
                        'err_user_inactive'=>'Адрес когда-то существовал, но сейчас отключен. Доставка не удалась. Статус окончательный.',
                        'err_mailbox_full'=>'Почтовый ящик получателя переполнен. Статус окончательный.',
                        'err_spam_rejected'=>'Письмо отклонено сервером как спам. Может измениться на err_spam_retry.',
                        'err_spam_folder'=>'Письмо помещено в папку со спамом почтовой службой. Статус окончательный. К сожалению, редкие почтовые службы сообщают такую информацию, поэтому таких статусов обычно немного.',
                        'err_delivery_failed'=>'Доставка не удалась по иным причинам. Статус окончательный.',
                        'err_will_retry'=>'Одна или несколько попыток доставки оказались неудачными, но попытки продолжаются. Статус неокончательный.',
                        'err_resend'=>'Фактически эквивалентен err_will_retry, с некоторыми несущественными внутренними особенностями.',
                        'err_domain_inactive'=>'Домен не принимает почту или не существует. Статус окончательный.',
                        'err_skip_letter'=>'Адресат не является активным - он отключён или заблокирован. Статус окончательный.',
                        'err_spam_skipped'=>'Сообщение не отправлено, т.к. большая часть рассылки попала в cпам и остальные письма отправлять не имеет смысла. Может измениться на err_spam_retry.',
                        'err_spam_retry'=>'письмо ранее не было отправлено из-за подозрения на спам, но после расследования выяснилось, что всё в порядке и его нужно переотправить. К сожалению, мы не сохраняем полный текст одиночных писем, поэтому не можем переотправить его самостоятельно, и можем лишь уведомить отправителя, что письмо можно переотправить. По предпринимаемым действиям статус похож на err_lost - но в данном случае причина его установки - действия наших сотрудников из antiabuse-команды, а не системная ошибка. Статус окончательный.',
                        'err_unsubscribed'=>'отправка не выполнялась, т.к. адрес, по которому пытались отправить письмо, ранее отписался Выделяется по сравнению с err_skip_letter в отдельный случай, чтобы позволить пользователю API пометить этот адрес как отписавшийся и в своей базе данных и больше не отправлять на него. Статус окончательный.',
                        'err_src_invalid'=>'неправильный адрес отправителя. Используется, если "невалидность email-а отправителя" обнаружилась не на стадии приёма задания и проверки параметров, а на более поздней стадии, когда осуществляется детальная проверка того, что нужно отправить. Статус окончательный.',
                        'err_dest_invalid'=>'неправильный адрес получателя. Используется, если "невалидность email-а получателя" обнаружилась не на стадии приёма задания и проверки параметров, а на более поздней стадии, когда осуществляется подробная проверка того, что нужно отправить. Статус окончательный.',
                        'err_not_allowed'=>'возможность отправки писем заблокирована системой из-за нехватки средств на счету или сотрудниками технической поддержки вручную. Статус окончательный.',
                        'err_not_available'=>'адрес, по которому пытались отправить письмо, не является доступным (т.е. ранее отправки на него приводили к сообщениям а-ля "адрес не существует" или "блокировка по спаму") Доступность адреса теоретически может быть восстановлена через несколько дней или недель, поэтому можно его не вычёркивать полностью из списка потенциальных адресатов. Статус окончательный.',
                        'err_lost'=>'письмо было утеряно из-за сбоя на нашей стороне, и отправитель должен переотправить письмо самостоятельно, т.к. оригинал не сохранился. Статус окончательный.',
                        'err_internal'=>'внутренний сбой. Необходима переотправка письма. Статус окончательный.',
        ];
        
        
        return $status_list[$status];
        
        
    }
    
    
}
