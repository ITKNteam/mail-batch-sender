<?php

namespace app\controllers;

use Yii;
use app\models\UnisenderList;
use app\models\UnisenderConfirmEmailTxt;
use app\models\AgencySettings;
use app\models\CompareDataEmail;
use app\models\UnisenderDeliveryStatus;
use app\models\searchModels\UnisenderDeliveryStatusSearch;
use yii\data\SqlDataProvider;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UnisenderController implements the CRUD actions for UnisenderList model.
 */
class UnisenderController extends Controller
{
    public $layout = 'sb-admin';
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all UnisenderList models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => UnisenderList::find()->with('agency')->orderBy(['id'=>SORT_DESC]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
    
    
    public function actionListStat($list_id, $agency_id, $request_stat=0){
        
        
        if ($request_stat){
            \app\models\UnisenderExportContacts::export($list_id, $agency_id);
             Yii::$app->session->setFlash('success', 'Задача выполнена.' );
    
        }
        
    
               
               $SQL = "SELECT   city,
                       
                    count(id) as c FROM westbtl.unisender_export_contacts
                    where list_id = $list_id
                    group by city, email_status, email_availability ";
               $db = Yii::$app->db;
              $count =  $db->createCommand($SQL)->queryAll();
              $count = count($count);

               
                $dataProvider = new SqlDataProvider([
                    'sql' => "SELECT   city,
                        email_status, email_availability,
                            case when email_status = 'new' then 'новый'
                                 when email_status = 'invited' then 'отправлено приглашение со ссылкой подтверждения подписки, ждём ответа, рассылка по такому адресу пока невозможна'
                                 when email_status = 'active' then 'активный адрес, возможна рассылка'
                                 when email_status = 'inactive' then 'адрес отключён через веб-интерфейс, никакие рассылки невозможны, но можно снова включить через веб-интерфейс'
                                 when email_status = 'unsubscribed' then 'адресат отписался от всех рассылок'
                                 when email_status = 'blocked' then 'адрес заблокирован администрацией нашего сервиса (например, по жалобе адресата), рассылка по нему невозможна. Разблокировка возможна только по просьбе самого адресата'
                                 when email_status = 'activation_requested' then 'запрошена активация адреса у администрации UniSender, рассылка пока невозможна'
                                 end as email_status_txt,
                         case
                           when email_availability  = 'available' then 'адрес доступен'
                         when 	email_availability  = 'unreachable' then 'адрес недоступен'
                         when 	email_availability  = 'temp_unreachable' then 'адрес временно недоступен'
                         when 	email_availability  = 'mailbox_full' then 'почтовый ящик переполнен'
                         when 	email_availability  = 'spam_rejected' then 'письмо сочтено спамом сервером получателя. Через несколько дней этот статус будет снят.'
                         when 	email_availability  = 'spam_folder' then 'письмо помещено в спам самим получателем.'
                                 end as email_availability_txt,
                    count(id) as c FROM westbtl.unisender_export_contacts
                    where list_id = :list_id
                    group by city, email_status, email_availability
                    order by city,  email_availability, c",
                    "params" => [':list_id' => $list_id],
                    'totalCount' => $count,

                    'pagination' => [
                        'pageSize' => 50,
                    ],
                ]);
               
                $TotalDataProvider = new SqlDataProvider([
                    'sql' => "SELECT   
                        email_status,
                        email_availability,
                            case when email_status = 'new' then 'новый'
                                 when email_status = 'invited' then 'отправлено приглашение со ссылкой подтверждения подписки, ждём ответа, рассылка по такому адресу пока невозможна'
                                 when email_status = 'active' then 'активный адрес, возможна рассылка'
                                 when email_status = 'inactive' then 'адрес отключён через веб-интерфейс, никакие рассылки невозможны, но можно снова включить через веб-интерфейс'
                                 when email_status = 'unsubscribed' then 'адресат отписался от всех рассылок'
                                 when email_status = 'blocked' then 'адрес заблокирован администрацией нашего сервиса (например, по жалобе адресата), рассылка по нему невозможна. Разблокировка возможна только по просьбе самого адресата'
                                 when email_status = 'activation_requested' then 'запрошена активация адреса у администрации UniSender, рассылка пока невозможна'
                                 end as email_status,
                         case
                         when email_availability  = 'available' then 'адрес доступен'
                         when 	email_availability  = 'unreachable' then 'адрес недоступен'
                         when 	email_availability  = 'temp_unreachable' then 'адрес временно недоступен'
                         when 	email_availability  = 'mailbox_full' then 'почтовый ящик переполнен'
                         when 	email_availability  = 'spam_rejected' then 'письмо сочтено спамом сервером получателя. Через несколько дней этот статус будет снят.'
                         when 	email_availability  = 'spam_folder' then 'письмо помещено в спам самим получателем.'
                                 end as email_availability,
                    count(id) as c FROM westbtl.unisender_export_contacts
                    where list_id = :list_id
                    group by  email_status, email_availability
                    order by  email_availability, c",
                    "params" => [':list_id' => $list_id],
                    'totalCount' => $count,

                    'pagination' => [
                        'pageSize' => 50,
                    ],
                ]);
            
        
         return $this->render('list-stat', [
            'dataProvider' => $dataProvider,
             'TotalDataProvider'=>$TotalDataProvider,
             'list_id'=>$list_id, 
             'agency_id'=>$agency_id,
        ]);
    }


    public function actionConfirmLetter()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => UnisenderConfirmEmailTxt::find(),
        ]);

        return $this->render('confirm-letter', [
            'dataProvider' => $dataProvider,
        ]);
    }
    
    
    public function actionCreateConfirmLetter()
    {
        
        $model = new UnisenderConfirmEmailTxt();

        
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
              
            Yii::$app->session->setFlash('success', 'Текст письма создан, и его можно прикреплять к спискам.');
             
            return $this->redirect(['confirm-letter']);
        } else {
            return $this->render('createConfirmLetter', [
                'model' => $model,
            ]);
        }
      }
      
      
      
    public function actionUpdateConfirmLetter($id)
    {
         
        $model =  UnisenderConfirmEmailTxt::findOne(['id'=>$id]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
              
            Yii::$app->session->setFlash('info', 'Текст письма обновлен, и его можно прикреплять к спискам.');
             
            return $this->redirect(['confirm-letter']);
        } else {
            return $this->render('createConfirmLetter', [
                'model' => $model,
            ]);
        }
      }
      
      
      //update-mail-confirm-tpl
      public function actionUpdateMailConfirmTpl($id)
    {
        
        $model = \app\models\UnisenderConfirmEmailTxt::findOne(['id'=>$id]);
        $model_new = new \app\models\UnisenderConfirmEmailTxt();
       
        
        
        $model_new->name = $model->name;
        $model_new->sender_name = $model->sender_name;
        $model_new->sender_email = $model->sender_email;
        $model_new->subject = $model->subject;
        $model_new->body = $model->body;
        //$model_new->dt_create = $model->dt_create;
        //$model_new->uid_create = $model->uid_create;
        $model_new->agency_id = $model->agency_id;
        $model_new->save();
        $new_id =   $model_new->id;
        return $this->redirect(['update-confirm-letter', 'id'=>$new_id]);
        
       
      }
    
    
    
    
    
    
    
    public function actionFields()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => \app\models\UnisenderFields::find()->with('agency'),
        ]);

        return $this->render('fields', [
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionMailTpl()
    {
        //mail-tpl
        $dataProvider = new ActiveDataProvider([
            'query' => \app\models\UnisenderMailTpl::find()->with('agency')->with('group')->with('campaign')->with('status'),
        ]);

        return $this->render('mail_tpl', [
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionMailTpl2()
    {
        //mail-tpl
        $dataProvider = new ActiveDataProvider([
            'query' => \app\models\UnisenderMailTpl::find()
                ->with('agency')
                ->with('group')
                ->with('campaign')
                ->with('campaign')
                ->with('unisenderMailTplFileds')
                ->with('status'),
        ]);

        return $this->render('mail_tpl_2', [
            'dataProvider' => $dataProvider,
        ]);
    }
    
     
     
      public function actionCreateMailTpl2()
    {
        
        $model = new \app\models\UnisenderMailTpl();
        $modelUniFileds = new \app\models\UnisenderMailTplFileds();  
        $modelCompare = new CompareDataEmail();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $modelCompare->load(Yii::$app->request->post());
            
            $modelCompare->agency_id = $model->agency_id;
            $modelCompare->mail_tpl_id = $model->id;
            $modelCompare->campaign_id = $model->campaign_id;
            $modelCompare->save();
            
            
            $modelUniFileds->load(Yii::$app->request->post());
            $modelUniFileds->u_mail_tpl_id = $model->id;
            $modelUniFileds->value = $modelCompare->value;
            $modelUniFileds->save();
              
            Yii::$app->session->setFlash('success', 'Текст письма создан.');
             
            return $this->redirect(['mail-tpl-2']);
        } else {
            return $this->render('createMailTpl_2', [
                'model' => $model,
                'modelCompare'=>$modelCompare, 
                'modelUniFileds'=>$modelUniFileds
            ]);
        }
      }
      
      
    public function actionUpdateMailTpl2($id)
    {
        
        $model = \app\models\UnisenderMailTpl::findOne(['id'=>$id]);
        $modelCompare = CompareDataEmail::findOne(['mail_tpl_id'=>$id]);
        $modelUniFileds =  \app\models\UnisenderMailTplFileds::find()->where(['u_mail_tpl_id'=>$id])->one();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
              
            $modelCompare->load(Yii::$app->request->post());
            $modelCompare->agency_id = $model->agency_id;
            $modelCompare->mail_tpl_id = $model->id;
            $modelCompare->campaign_id = $model->campaign_id;
            $modelCompare->save();
            
            $modelUniFileds->load(Yii::$app->request->post());
            $modelUniFileds->u_mail_tpl_id = $model->id;
            $modelUniFileds->value = $modelCompare->value;
            $modelUniFileds->save();
            
            Yii::$app->session->setFlash('success', 'Текст письма обновлен.');
             
            return $this->redirect(['mail-tpl-2']);
        } else {
            return $this->render('createMailTpl_2', [
                'model' => $model,
                'modelCompare'=>$modelCompare, 
                'modelUniFileds'=>$modelUniFileds
                
            ]);
        }
      }
    
    
     public function actionCreateMailTpl()
    {
        
        $model = new \app\models\UnisenderMailTpl();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
              
            Yii::$app->session->setFlash('success', 'Текст письма создан.');
             
            return $this->redirect(['mail-tpl']);
        } else {
            return $this->render('createMailTpl', [
                'model' => $model,
            ]);
        }
      }
      
      
     public function actionUpdateMailTpl($id)
    {
        
        $model = \app\models\UnisenderMailTpl::findOne(['id'=>$id]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
              
            Yii::$app->session->setFlash('success', 'Текст письма обновлен.');
             
            return $this->redirect(['mail-tpl']);
        } else {
            return $this->render('createMailTpl', [
                'model' => $model,
            ]);
        }
      }
    
      
           
    
    
    public function actionEmail()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => \app\models\UnisenderEmail::find()->with('agency')
                ->with('list')->orderBy(['id'=>SORT_DESC]),
        ]);

        return $this->render('email', [
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionCampaign()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => \app\models\UnisenderCampaign::find()->with('agency')->with('message')->orderBy(['id'=>SORT_DESC]),
        ]);

        return $this->render('campaign', [
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionCampaignReports()
    {
        
        
        
                          // -- count(id) FROM westbtl.crm_users
      $BATCH_SQL =  "SELECT activity_loc,
                    case 
                             when email = '' then 'empty'
                             when email != '' then 'have'
                             end  as email_t,

                   count(id) FROM westbtl.csv_batches
                  group by email_t, activity_loc
                  order by 1

";
        
        
       $db = Yii::$app->db;
              $b_count =  $db->createCommand($BATCH_SQL)->queryAll();
              $b_count = count($b_count);
//      
//      
      $BatchDataProvider = new SqlDataProvider([
                    'sql' => "SELECT activity_loc as city,
                                    case 
                                     when email = '' then 'Нет email'
                                    when email != '' then 'Есть email'
                                 end  as email_t,
                       count(id) FROM westbtl.csv_batches
                      group by email_t, activity_loc
                      order by 1

",
                   // "params" => [':loc_campaign_id' =>  $local_campaign_id],
                    'totalCount' => $b_count,

                    'pagination' => [
                        'pageSize' => $b_count,
                    ],
                ]);
      $TotalBatchDataProvider = new SqlDataProvider([
                    'sql' => "SELECT 
                                    case 
                                     when email = '' then 'нет email'
                                    when email != '' then 'есть email'
                                 end  as email_t,
                       count(id) FROM westbtl.csv_batches
                      group by email_t
                      order by 1

",
                   // "params" => [':loc_campaign_id' =>  $local_campaign_id],
                    'totalCount' => 5,

                    'pagination' => [
                        'pageSize' => 5,
                    ],
                ]);
      
      
               $SQL = "SELECT c.activity_loc as city,
                        count(c.id) as c FROM westbtl.csv_batches c
                        
                        group by c.activity_loc, c.unisender_send_result  ";
               $db = Yii::$app->db;
              $count =  $db->createCommand($SQL)->queryAll();
              $count = count($count);
              
              
   

               
                $dataProvider = new SqlDataProvider([
                    'sql' => "SELECT 
                            c.activity_loc AS city,
                            CASE
                                WHEN `c`.`unisender_send_result` IS NULL THEN 'не определенно'
                                WHEN `c`.`unisender_send_result` = 'not_sent' THEN 'Сообщение еще не было обработано. Также этот статус возвращается для отложенных для модерации сообщений.'
                                WHEN `c`.`unisender_send_result` = 'ok_sent' THEN 'Сообщение было отправлено, промежуточный статус до получения ответа о доставке/недоставке.'
                                WHEN `c`.`unisender_send_result` = 'ok_delivered' THEN 'Доставленно'
                                WHEN `c`.`unisender_send_result` = 'ok_read' THEN 'Прочитано'
                                WHEN `c`.`unisender_send_result` = 'ok_fbl' THEN 'Сообщение доставлено, но помещено в папку спам получателем. К сожалению, редкие почтовые службы сообщают такую информацию, поэтому таких статусов обычно немного.'
                                WHEN `c`.`unisender_send_result` = 'ok_link_visited' THEN 'Сообщение доставлено, прочитано и выполнен переход по одной из ссылок. Может измениться на ok_unsubscribed или ok_spam_folder.'
                                WHEN `c`.`unisender_send_result` = 'ok_unsubscribed' THEN 'Отписались'
                                WHEN `c`.`unisender_send_result` = 'err_user_unknown' THEN 'Адрес не существует'
                                WHEN `c`.`unisender_send_result` = 'err_user_inactive' THEN 'Адрес когда-то существовал, но сейчас отключен. Доставка не удалась. Статус окончательный.'
                                WHEN `c`.`unisender_send_result` = 'err_mailbox_full' THEN 'Почтовый ящик получателя переполнен. Статус окончательный.'
                                WHEN `c`.`unisender_send_result` = 'err_spam_rejected' THEN 'Письмо отклонено сервером как спам. Может измениться на err_spam_retry.'
                                WHEN `c`.`unisender_send_result` = 'err_spam_folder' THEN 'Письмо помещено в папку со спамом почтовой службой. Статус окончательный. К сожалению, редкие почтовые службы сообщают такую информацию, поэтому таких статусов обычно немного.'
                                WHEN `c`.`unisender_send_result` = 'err_delivery_failed' THEN 'Доставка не удалась по иным причинам'
                                WHEN `c`.`unisender_send_result` = 'err_will_retry' THEN 'Попытки продолжаются'
                                WHEN `c`.`unisender_send_result` = 'err_resend' THEN 'Фактически эквивалентен err_will_retry, с некоторыми несущественными внутренними особенностями.'
                                WHEN `c`.`unisender_send_result` = 'err_domain_inactive' THEN 'Домен не принимает почту или не существует. Статус окончательный.'
                                WHEN `c`.`unisender_send_result` = 'err_skip_letter' THEN 'Адресат не является активным - он отключён или заблокирован. Статус окончательный.'
                                WHEN `c`.`unisender_send_result` = 'err_spam_skipped' THEN 'Сообщение не отправлено, т.к. большая часть рассылки попала в cпам и остальные письма отправлять не имеет смысла. Может измениться на err_spam_retry.'
                                WHEN `c`.`unisender_send_result` = 'err_spam_retry' THEN 'письмо ранее не было отправлено из-за подозрения на спам, но после расследования выяснилось, что всё в порядке и его нужно переотправить. К сожалению, мы не сохраняем полный текст одиночных писем, поэтому не можем переотправить его самостоятельно, и можем лишь уведомить отправителя, что письмо можно переотправить. По предпринимаемым действиям статус похож на err_lost - но в данном случае причина его установки - действия наших сотрудников из antiabuse-команды, а не системная ошибка. Статус окончательный.'
                                WHEN `c`.`unisender_send_result` = 'err_unsubscribed' THEN 'отправка не выполнялась, т.к. адрес, по которому пытались отправить письмо, ранее отписался Выделяется по сравнению с err_skip_letter в отдельный случай, чтобы позволить пользователю API пометить этот адрес как отписавшийся и в своей базе данных и больше не отправлять на него. Статус окончательный.'
                                WHEN `c`.`unisender_send_result` = 'err_src_invalid' THEN 'неправильный адрес отправителя. Используется, если невалидность email-а отправителя обнаружилась не на стадии приёма задания и проверки параметров, а на более поздней стадии, когда осуществляется детальная проверка того, что нужно отправить. Статус окончательный.'
                                WHEN `c`.`unisender_send_result` = 'err_dest_invalid' THEN 'неправильный адрес получателя. Используется, если невалидность email-а получателя обнаружилась не на стадии приёма задания и проверки параметров, а на более поздней стадии, когда осуществляется подробная проверка того, что нужно отправить. Статус окончательный.'
                                WHEN `c`.`unisender_send_result` = 'err_not_allowed' THEN 'возможность отправки писем заблокирована системой из-за нехватки средств на счету или сотрудниками технической поддержки вручную. Статус окончательный.'
                                WHEN `c`.`unisender_send_result` = 'err_not_available' THEN 'адрес, по которому пытались отправить письмо, не является доступным (т.е. ранее отправки на него приводили к сообщениям а-ля адрес не существует или блокировка по спаму) Доступность адреса теоретически может быть восстановлена через несколько дней или недель, поэтому можно его не вычёркивать полностью из списка потенциальных адресатов. Статус окончательный.'
                                WHEN `c`.`unisender_send_result` = 'err_lost' THEN 'письмо было утеряно из-за сбоя на нашей стороне, и отправитель должен переотправить письмо самостоятельно, т.к. оригинал не сохранился. Статус окончательный.'
                                WHEN `c`.`unisender_send_result` = 'err_internal' THEN 'внутренний сбой. Необходима переотправка письма. Статус окончательный.'
                            END AS email_status,
                            c.unisender_send_result,
                            COUNT(c.row_id) AS c
                        FROM
                            (SELECT 
                                t.*, COUNT(t.row_id) AS c
                            FROM
                                crm_users t
                                where email != ''
                            GROUP BY email) c
                        GROUP BY c.activity_loc , unisender_send_result ",
                   // "params" => [':loc_campaign_id' =>  $local_campaign_id],
                    'totalCount' => $count,

                    'pagination' => [
                        'pageSize' => $count,
                    ],
                ]);
                
                $TotalDataProvider = new SqlDataProvider([
                    'sql' => "SELECT 
                            
                            CASE
                                WHEN `c`.`unisender_send_result` IS NULL THEN 'не определенно'
                                WHEN `c`.`unisender_send_result` = 'not_sent' THEN 'Сообщение еще не было обработано. Также этот статус возвращается для отложенных для модерации сообщений.'
                                WHEN `c`.`unisender_send_result` = 'ok_sent' THEN 'Сообщение было отправлено, промежуточный статус до получения ответа о доставке/недоставке.'
                                WHEN `c`.`unisender_send_result` = 'ok_delivered' THEN 'Доставленно'
                                WHEN `c`.`unisender_send_result` = 'ok_read' THEN 'Прочитано'
                                WHEN `c`.`unisender_send_result` = 'ok_fbl' THEN 'Сообщение доставлено, но помещено в папку спам получателем. К сожалению, редкие почтовые службы сообщают такую информацию, поэтому таких статусов обычно немного.'
                                WHEN `c`.`unisender_send_result` = 'ok_link_visited' THEN 'Сообщение доставлено, прочитано и выполнен переход по одной из ссылок. Может измениться на ok_unsubscribed или ok_spam_folder.'
                                WHEN `c`.`unisender_send_result` = 'ok_unsubscribed' THEN 'Отписались'
                                WHEN `c`.`unisender_send_result` = 'err_user_unknown' THEN 'Адрес не существует'
                                WHEN `c`.`unisender_send_result` = 'err_user_inactive' THEN 'Адрес когда-то существовал, но сейчас отключен. Доставка не удалась. Статус окончательный.'
                                WHEN `c`.`unisender_send_result` = 'err_mailbox_full' THEN 'Почтовый ящик получателя переполнен. Статус окончательный.'
                                WHEN `c`.`unisender_send_result` = 'err_spam_rejected' THEN 'Письмо отклонено сервером как спам. Может измениться на err_spam_retry.'
                                WHEN `c`.`unisender_send_result` = 'err_spam_folder' THEN 'Письмо помещено в папку со спамом почтовой службой. Статус окончательный. К сожалению, редкие почтовые службы сообщают такую информацию, поэтому таких статусов обычно немного.'
                                WHEN `c`.`unisender_send_result` = 'err_delivery_failed' THEN 'Доставка не удалась по иным причинам'
                                WHEN `c`.`unisender_send_result` = 'err_will_retry' THEN 'Попытки продолжаются'
                                WHEN `c`.`unisender_send_result` = 'err_resend' THEN 'Фактически эквивалентен err_will_retry, с некоторыми несущественными внутренними особенностями.'
                                WHEN `c`.`unisender_send_result` = 'err_domain_inactive' THEN 'Домен не принимает почту или не существует. Статус окончательный.'
                                WHEN `c`.`unisender_send_result` = 'err_skip_letter' THEN 'Адресат не является активным - он отключён или заблокирован. Статус окончательный.'
                                WHEN `c`.`unisender_send_result` = 'err_spam_skipped' THEN 'Сообщение не отправлено, т.к. большая часть рассылки попала в cпам и остальные письма отправлять не имеет смысла. Может измениться на err_spam_retry.'
                                WHEN `c`.`unisender_send_result` = 'err_spam_retry' THEN 'письмо ранее не было отправлено из-за подозрения на спам, но после расследования выяснилось, что всё в порядке и его нужно переотправить. К сожалению, мы не сохраняем полный текст одиночных писем, поэтому не можем переотправить его самостоятельно, и можем лишь уведомить отправителя, что письмо можно переотправить. По предпринимаемым действиям статус похож на err_lost - но в данном случае причина его установки - действия наших сотрудников из antiabuse-команды, а не системная ошибка. Статус окончательный.'
                                WHEN `c`.`unisender_send_result` = 'err_unsubscribed' THEN 'отправка не выполнялась, т.к. адрес, по которому пытались отправить письмо, ранее отписался Выделяется по сравнению с err_skip_letter в отдельный случай, чтобы позволить пользователю API пометить этот адрес как отписавшийся и в своей базе данных и больше не отправлять на него. Статус окончательный.'
                                WHEN `c`.`unisender_send_result` = 'err_src_invalid' THEN 'неправильный адрес отправителя. Используется, если невалидность email-а отправителя обнаружилась не на стадии приёма задания и проверки параметров, а на более поздней стадии, когда осуществляется детальная проверка того, что нужно отправить. Статус окончательный.'
                                WHEN `c`.`unisender_send_result` = 'err_dest_invalid' THEN 'неправильный адрес получателя. Используется, если невалидность email-а получателя обнаружилась не на стадии приёма задания и проверки параметров, а на более поздней стадии, когда осуществляется подробная проверка того, что нужно отправить. Статус окончательный.'
                                WHEN `c`.`unisender_send_result` = 'err_not_allowed' THEN 'возможность отправки писем заблокирована системой из-за нехватки средств на счету или сотрудниками технической поддержки вручную. Статус окончательный.'
                                WHEN `c`.`unisender_send_result` = 'err_not_available' THEN 'адрес, по которому пытались отправить письмо, не является доступным (т.е. ранее отправки на него приводили к сообщениям а-ля адрес не существует или блокировка по спаму) Доступность адреса теоретически может быть восстановлена через несколько дней или недель, поэтому можно его не вычёркивать полностью из списка потенциальных адресатов. Статус окончательный.'
                                WHEN `c`.`unisender_send_result` = 'err_lost' THEN 'письмо было утеряно из-за сбоя на нашей стороне, и отправитель должен переотправить письмо самостоятельно, т.к. оригинал не сохранился. Статус окончательный.'
                                WHEN `c`.`unisender_send_result` = 'err_internal' THEN 'внутренний сбой. Необходима переотправка письма. Статус окончательный.'
                            END AS email_status,
                            c.unisender_send_result,
                            COUNT(c.row_id) AS c
                        FROM
                            (SELECT 
                                t.*, COUNT(t.row_id) AS c
                            FROM
                                crm_users t
                                where email != ''
                            GROUP BY email) c
                        GROUP BY unisender_send_result
                        order by 3  ",
                   // "params" => [':loc_campaign_id' =>  $local_campaign_id],
                    'totalCount' => 30,

                    'pagination' => [
                        'pageSize' => 30,
                    ],
                ]);
                
                
                
                
            
            
            
            
            
            

            return $this->render('campaign-reports', [
                                    'TotalBatchDataProvider'=>$TotalBatchDataProvider,
                                    'BatchDataProvider'=>$BatchDataProvider,
                                    'dataProvider'=>$dataProvider,
                                    'TotalDataProvider'=>$TotalDataProvider
                                    ]);
        
        
       
    }
    
      public function actionCreateCampaign()
    {
        
        $model = new \app\models\UnisenderCampaign();

        
        
       // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        if (Yii::$app->request->post()) {
            $data= Yii::$app->request->post();
            
            //UnisenderList
           // print_r($data['UnisenderList']['list_title']);
            $model = new \app\models\UnisenderCampaign();
            $name =  $data['UnisenderCampaign']['name']; 
            $message_id =  $data['UnisenderCampaign']['message_id']; 
            
            
            $agency_id =  $data['UnisenderCampaign']['agency_id'];   
            $answ =  $model->newCampaign($name, $message_id,  $agency_id);
            Yii::$app->session->setFlash('success', $answ['message']);
             $id = $answ['id'];
            return $this->redirect(['campaign', 'id' => $id]);
        } else {
            return $this->render('createСampaign', [
                'model' => $model,
            ]);
        }
    }
    
    
    public function actionMailFields($mail_tpl_id)
    {
        //mail-tpl
        $MailTplmodel = \app\models\UnisenderMailTpl::find()->where(['id'=>$mail_tpl_id])->one();
        
        $MailTplProvider = new ActiveDataProvider([
            'query' => \app\models\UnisenderMailTpl::find()->where(['id'=>$mail_tpl_id]),
        ]);
        $dataProvider = new ActiveDataProvider([
            'query' => \app\models\UnisenderMailTplFileds::find()->where(['u_mail_tpl_id'=>$mail_tpl_id])->with('uMailTpl')->with('uField'),
        ]);

        return $this->render('mail_tpl_fields', [
            'MailTplmodel'=>$MailTplmodel,
            'MailTplProvider'=>$MailTplProvider,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    
    
     public function actionCreateEmail()
    {
        
        $model = new \app\models\UnisenderEmail();

        
        
       // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        if (Yii::$app->request->post()) {
            $data= Yii::$app->request->post();
            
            //UnisenderList
           // print_r($data['UnisenderList']['list_title']);
            $model = new \app\models\UnisenderEmail();
            $name =  $data['UnisenderEmail']['name']; 
            $list_id =  $data['UnisenderEmail']['list_id']; 
            $email_subject =  $data['UnisenderEmail']['subject']; 
            $body =  $data['UnisenderEmail']['body']; 
            $email_from_name =  $data['UnisenderEmail']['sender_name']; 
            $email_from_email =  $data['UnisenderEmail']['sender_email']; 
            
            $agency_id =  $data['UnisenderEmail']['agency_id'];   
            $answ =  $model->newEmail($name, $list_id, $email_subject, $body, $email_from_name, $email_from_email, $agency_id);
            Yii::$app->session->setFlash('success', $answ['message']);
             $id = $answ['id'];
            return $this->redirect(['email', 'id' => $id]);
        } else {
            return $this->render('createEmail', [
                'model' => $model,
            ]);
        }
    }
    
    public  function actionCampaignStat($campaign_id, $agency_id, $local_campaign_id, $report_type = 'stat', $request_stat=0, $message_id=0){
        
        
        if ($report_type === 'stat'){
            $api_key = AgencySettings::getApiKey($agency_id);
            $res = Yii::$app->unisender->getCampaignAggregateStats($campaign_id, $api_key);

             $CampaignStatus = Yii::$app->unisender->getCampaignStatus($campaign_id, $api_key);

             $res1 = UnisenderDeliveryStatus::saveStatus($campaign_id, $agency_id);


              $MaildeliverysearchModel = new UnisenderDeliveryStatusSearch();
            $MaildeliveryProvider = $MaildeliverysearchModel->search(Yii::$app->request->queryParams, $local_campaign_id);



            $CampaignStatusFormat = ['status'=> \app\models\UnisenderCampaign::CampaignStatusList(@$CampaignStatus['UnisenderAnswer']->result->status),
                                     'creation_time'=>date("d M Y h:i:s",  strtotime(@$CampaignStatus['UnisenderAnswer']->result->creation_time)),
                                     'start_time'=>date("d M Y h:i:s",  strtotime(@$CampaignStatus['UnisenderAnswer']->result->start_time))
                                    ];

            
            
            
            
            //    \app\models\CrmUsers::updateStatus($local_campaign_id);
            //\app\models\UnisenderExportContacts::export($list_id, $agency_id);
             Yii::$app->session->setFlash('success', 'Задача выполнена.' );
    
        
        
                
            $inf =   \app\models\UnisenderCampaign::getFullInfo($local_campaign_id);
        
                //$local_email_id = 10;
               
//               $SQL = "SELECT c.activity_loc as city,
//                        count(c.row_id) as c FROM crm_users c
//                        where email in (SELECT email FROM westbtl.unisender_delivery_status
//                                    where campaign_id = $local_campaign_id )
//                        group by c.activity_loc, c.unisender_send_result  ";
//               $db = Yii::$app->db;
//              $count =  $db->createCommand($SQL)->queryAll();
//              $count = count($count);
//              $count = 50;
              
   

        $dataProvider = new SqlDataProvider(['sql'=>'select now()']);
//                $dataProvider = new SqlDataProvider([
//                    'sql' => "SELECT 
//                            c.activity_loc AS city,
//                            CASE
//                                WHEN `c`.`unisender_send_result` IS NULL THEN 'не определенно'
//                                WHEN `c`.`unisender_send_result` = 'not_sent' THEN 'Сообщение еще не было обработано. Также этот статус возвращается для отложенных для модерации сообщений.'
//                                WHEN `c`.`unisender_send_result` = 'ok_sent' THEN 'Сообщение было отправлено, промежуточный статус до получения ответа о доставке/недоставке.'
//                                WHEN `c`.`unisender_send_result` = 'ok_delivered' THEN 'Доставленно'
//                                WHEN `c`.`unisender_send_result` = 'ok_read' THEN 'Прочитано'
//                                WHEN `c`.`unisender_send_result` = 'ok_fbl' THEN 'Сообщение доставлено, но помещено в папку спам получателем. К сожалению, редкие почтовые службы сообщают такую информацию, поэтому таких статусов обычно немного.'
//                                WHEN `c`.`unisender_send_result` = 'ok_link_visited' THEN 'Сообщение доставлено, прочитано и выполнен переход по одной из ссылок. Может измениться на ok_unsubscribed или ok_spam_folder.'
//                                WHEN `c`.`unisender_send_result` = 'ok_unsubscribed' THEN 'Отписались'
//                                WHEN `c`.`unisender_send_result` = 'err_user_unknown' THEN 'Адрес не существует'
//                                WHEN `c`.`unisender_send_result` = 'err_user_inactive' THEN 'Адрес когда-то существовал, но сейчас отключен. Доставка не удалась. Статус окончательный.'
//                                WHEN `c`.`unisender_send_result` = 'err_mailbox_full' THEN 'Почтовый ящик получателя переполнен. Статус окончательный.'
//                                WHEN `c`.`unisender_send_result` = 'err_spam_rejected' THEN 'Письмо отклонено сервером как спам. Может измениться на err_spam_retry.'
//                                WHEN `c`.`unisender_send_result` = 'err_spam_folder' THEN 'Письмо помещено в папку со спамом почтовой службой. Статус окончательный. К сожалению, редкие почтовые службы сообщают такую информацию, поэтому таких статусов обычно немного.'
//                                WHEN `c`.`unisender_send_result` = 'err_delivery_failed' THEN 'Доставка не удалась по иным причинам'
//                                WHEN `c`.`unisender_send_result` = 'err_will_retry' THEN 'Попытки продолжаются'
//                                WHEN `c`.`unisender_send_result` = 'err_resend' THEN 'Фактически эквивалентен err_will_retry, с некоторыми несущественными внутренними особенностями.'
//                                WHEN `c`.`unisender_send_result` = 'err_domain_inactive' THEN 'Домен не принимает почту или не существует. Статус окончательный.'
//                                WHEN `c`.`unisender_send_result` = 'err_skip_letter' THEN 'Адресат не является активным - он отключён или заблокирован. Статус окончательный.'
//                                WHEN `c`.`unisender_send_result` = 'err_spam_skipped' THEN 'Сообщение не отправлено, т.к. большая часть рассылки попала в cпам и остальные письма отправлять не имеет смысла. Может измениться на err_spam_retry.'
//                                WHEN `c`.`unisender_send_result` = 'err_spam_retry' THEN 'письмо ранее не было отправлено из-за подозрения на спам, но после расследования выяснилось, что всё в порядке и его нужно переотправить. К сожалению, мы не сохраняем полный текст одиночных писем, поэтому не можем переотправить его самостоятельно, и можем лишь уведомить отправителя, что письмо можно переотправить. По предпринимаемым действиям статус похож на err_lost - но в данном случае причина его установки - действия наших сотрудников из antiabuse-команды, а не системная ошибка. Статус окончательный.'
//                                WHEN `c`.`unisender_send_result` = 'err_unsubscribed' THEN 'отправка не выполнялась, т.к. адрес, по которому пытались отправить письмо, ранее отписался Выделяется по сравнению с err_skip_letter в отдельный случай, чтобы позволить пользователю API пометить этот адрес как отписавшийся и в своей базе данных и больше не отправлять на него. Статус окончательный.'
//                                WHEN `c`.`unisender_send_result` = 'err_src_invalid' THEN 'неправильный адрес отправителя. Используется, если невалидность email-а отправителя обнаружилась не на стадии приёма задания и проверки параметров, а на более поздней стадии, когда осуществляется детальная проверка того, что нужно отправить. Статус окончательный.'
//                                WHEN `c`.`unisender_send_result` = 'err_dest_invalid' THEN 'неправильный адрес получателя. Используется, если невалидность email-а получателя обнаружилась не на стадии приёма задания и проверки параметров, а на более поздней стадии, когда осуществляется подробная проверка того, что нужно отправить. Статус окончательный.'
//                                WHEN `c`.`unisender_send_result` = 'err_not_allowed' THEN 'возможность отправки писем заблокирована системой из-за нехватки средств на счету или сотрудниками технической поддержки вручную. Статус окончательный.'
//                                WHEN `c`.`unisender_send_result` = 'err_not_available' THEN 'адрес, по которому пытались отправить письмо, не является доступным (т.е. ранее отправки на него приводили к сообщениям а-ля адрес не существует или блокировка по спаму) Доступность адреса теоретически может быть восстановлена через несколько дней или недель, поэтому можно его не вычёркивать полностью из списка потенциальных адресатов. Статус окончательный.'
//                                WHEN `c`.`unisender_send_result` = 'err_lost' THEN 'письмо было утеряно из-за сбоя на нашей стороне, и отправитель должен переотправить письмо самостоятельно, т.к. оригинал не сохранился. Статус окончательный.'
//                                WHEN `c`.`unisender_send_result` = 'err_internal' THEN 'внутренний сбой. Необходима переотправка письма. Статус окончательный.'
//                            END AS email_status,
//                            c.unisender_send_result,
//                            COUNT(c.row_id) AS c
//                        FROM
//                            (SELECT 
//                                t.*, COUNT(t.row_id) AS c
//                            FROM
//                                crm_users t
//                            WHERE
//                                email IN (SELECT 
//                                        email
//                                    FROM
//                                        westbtl.unisender_delivery_status
//                                    WHERE
//                                        campaign_id = :loc_campaign_id)
//                            GROUP BY email) c
//                        GROUP BY c.activity_loc , unisender_send_result ",
//                    "params" => [':loc_campaign_id' =>  $local_campaign_id],
//                    'totalCount' => $count,
//
//                    'pagination' => [
//                        'pageSize' => $count,
//                    ],
//                ]);
                
                
//                $BATCH_SQL =  "SELECT
//            case 
//           when e.value = '' then 'есть email'
//           when e.value != '' then 'нет email'
//           end  as email_stat,
//           c.value as city,
//           count(c.id)
//
//           FROM westbtl.batch_data e,  westbtl.batch_data c
//          where e.agency_parametr_id in (44)
//          and e.batch_id = c.batch_id
//          and e.string_order = c.string_order
//          and c.batch_id in (".implode(',', $inf['campaign']['batches_id']).")
//          and c.agency_parametr_id in (52)
//          group by email_stat, city";
//        
//        
//       $db = Yii::$app->db;
//              $b_count =  $db->createCommand($BATCH_SQL)->queryAll();
//              $b_count = count($b_count);
//      
//      
//      $BatchDataProvider = new SqlDataProvider([
//                    'sql' => "SELECT
//            case 
//           when e.value = '' then 'empty'
//           when e.value != '' then 'have'
//           end  as email,
//           c.value as city,
//           count(c.id)
//
//           FROM westbtl.batch_data e,  westbtl.batch_data c
//          where e.agency_parametr_id in (44)
//          and e.batch_id = c.batch_id
//          and e.string_order = c.string_order
//          and c.agency_parametr_id in (52)
//          and c.batch_id in (:batch_ids)
//          group by email, city",
//                    "params" => [':batch_ids' =>  implode(',', $inf['campaign']['batches_id'])],
//                    'totalCount' => $b_count,
//
//                    'pagination' => [
//                        'pageSize' => $b_count,
//                    ],
//                ]);
//            
            
            
            
            
            

            return $this->render('campaign-stat', ['res'=>$res, 
                                    'CampaignStatus'=>$CampaignStatusFormat,
                                    'MaildeliverysearchModel'=>$MaildeliverysearchModel,    
                                    'MaildeliveryProvider'=>$MaildeliveryProvider,
                                    'dataProvider'=>$dataProvider,
                        //            'BatchDataProvider'=>$BatchDataProvider    
                    
                                    ]);
        
        } else {
            
            
            
        
             \app\models\CrmUsers::updateStatus($local_campaign_id);
            //\app\models\UnisenderExportContacts::export($list_id, $agency_id);
             Yii::$app->session->setFlash('success', 'Задача выполнена.' );
    
        
        
                
            $inf =   \app\models\UnisenderCampaign::getFullInfo($local_campaign_id);
        
                //$local_email_id = 10;
               
               $SQL = "SELECT c.activity_loc as city,
                        count(c.row_id) as c FROM crm_users c
                        where email in (SELECT email FROM westbtl.unisender_delivery_status
                                    where campaign_id = $local_campaign_id )
                        group by c.activity_loc, c.unisender_send_result  ";
               $db = Yii::$app->db;
              $count =  $db->createCommand($SQL)->queryAll();
              $count = count($count);
              //$count = 50;
              


               
                $dataProvider = new SqlDataProvider([
                    'sql' => "SELECT c.activity_loc as city,
                                case
                                when `c`.`unisender_send_result` is null then 'не определенно'
                                when `c`.`unisender_send_result` = 'not_sent' then 'Сообщение еще не было обработано. Также этот статус возвращается для отложенных для модерации сообщений.'
                                when `c`.`unisender_send_result` = 'ok_sent' then 'Сообщение было отправлено, промежуточный статус до получения ответа о доставке/недоставке.'
                                when `c`.`unisender_send_result` = 'ok_delivered' then 'Сообщение доставлено. Может измениться на ok_read, ok_link_visited, ok_unsubscribed или ok_spam_folder'
                                when `c`.`unisender_send_result` = 'ok_read' then 'Сообщение доставлено и зарегистрировано его прочтение. Может измениться на ok_link_visited, ok_unsubscribed или ok_spam_folder.'
                                when `c`.`unisender_send_result` = 'ok_fbl' then 'Сообщение доставлено, но помещено в папку спам получателем. К сожалению, редкие почтовые службы сообщают такую информацию, поэтому таких статусов обычно немного.'
                                when `c`.`unisender_send_result` = 'ok_link_visited' then 'Сообщение доставлено, прочитано и выполнен переход по одной из ссылок. Может измениться на ok_unsubscribed или ok_spam_folder.'
                                when `c`.`unisender_send_result` = 'ok_unsubscribed' then 'Сообщение доставлено и прочитано, но пользователь отписался по ссылке в письме. Статус окончательный.'
                                when `c`.`unisender_send_result` = 'err_user_unknown' then 'Адрес не существует, доставка не удалась. Статус окончательный.'
                                when `c`.`unisender_send_result` = 'err_user_inactive' then 'Адрес когда-то существовал, но сейчас отключен. Доставка не удалась. Статус окончательный.'
                                when `c`.`unisender_send_result` = 'err_mailbox_full' then 'Почтовый ящик получателя переполнен. Статус окончательный.'
                                when `c`.`unisender_send_result` = 'err_spam_rejected' then 'Письмо отклонено сервером как спам. Может измениться на err_spam_retry.'
                                when `c`.`unisender_send_result` = 'err_spam_folder' then 'Письмо помещено в папку со спамом почтовой службой. Статус окончательный. К сожалению, редкие почтовые службы сообщают такую информацию, поэтому таких статусов обычно немного.'
                                when `c`.`unisender_send_result` = 'err_delivery_failed' then 'Доставка не удалась по иным причинам. Статус окончательный.'
                                when `c`.`unisender_send_result` = 'err_will_retry' then 'Одна или несколько попыток доставки оказались неудачными, но попытки продолжаются. Статус неокончательный.'
                                when `c`.`unisender_send_result` = 'err_resend' then 'Фактически эквивалентен err_will_retry, с некоторыми несущественными внутренними особенностями.'
                                when `c`.`unisender_send_result` = 'err_domain_inactive' then 'Домен не принимает почту или не существует. Статус окончательный.'
                                when `c`.`unisender_send_result` = 'err_skip_letter' then 'Адресат не является активным - он отключён или заблокирован. Статус окончательный.'
                                when `c`.`unisender_send_result` = 'err_spam_skipped' then 'Сообщение не отправлено, т.к. большая часть рассылки попала в cпам и остальные письма отправлять не имеет смысла. Может измениться на err_spam_retry.'
                                when `c`.`unisender_send_result` = 'err_spam_retry' then 'письмо ранее не было отправлено из-за подозрения на спам, но после расследования выяснилось, что всё в порядке и его нужно переотправить. К сожалению, мы не сохраняем полный текст одиночных писем, поэтому не можем переотправить его самостоятельно, и можем лишь уведомить отправителя, что письмо можно переотправить. По предпринимаемым действиям статус похож на err_lost - но в данном случае причина его установки - действия наших сотрудников из antiabuse-команды, а не системная ошибка. Статус окончательный.'
                                when `c`.`unisender_send_result` = 'err_unsubscribed' then 'отправка не выполнялась, т.к. адрес, по которому пытались отправить письмо, ранее отписался Выделяется по сравнению с err_skip_letter в отдельный случай, чтобы позволить пользователю API пометить этот адрес как отписавшийся и в своей базе данных и больше не отправлять на него. Статус окончательный.'
                                when `c`.`unisender_send_result` = 'err_src_invalid' then 'неправильный адрес отправителя. Используется, если невалидность email-а отправителя обнаружилась не на стадии приёма задания и проверки параметров, а на более поздней стадии, когда осуществляется детальная проверка того, что нужно отправить. Статус окончательный.'
                                when `c`.`unisender_send_result` = 'err_dest_invalid' then 'неправильный адрес получателя. Используется, если невалидность email-а получателя обнаружилась не на стадии приёма задания и проверки параметров, а на более поздней стадии, когда осуществляется подробная проверка того, что нужно отправить. Статус окончательный.'
                                when `c`.`unisender_send_result` = 'err_not_allowed' then 'возможность отправки писем заблокирована системой из-за нехватки средств на счету или сотрудниками технической поддержки вручную. Статус окончательный.'
                                when `c`.`unisender_send_result` = 'err_not_available' then 'адрес, по которому пытались отправить письмо, не является доступным (т.е. ранее отправки на него приводили к сообщениям а-ля адрес не существует или блокировка по спаму) Доступность адреса теоретически может быть восстановлена через несколько дней или недель, поэтому можно его не вычёркивать полностью из списка потенциальных адресатов. Статус окончательный.'
                                when `c`.`unisender_send_result` = 'err_lost' then 'письмо было утеряно из-за сбоя на нашей стороне, и отправитель должен переотправить письмо самостоятельно, т.к. оригинал не сохранился. Статус окончательный.'
                                when `c`.`unisender_send_result` = 'err_internal' then 'внутренний сбой. Необходима переотправка письма. Статус окончательный.'
                                end as email_status,
                                 c.unisender_send_result,
                                count(c.row_id) as c 
                                FROM  crm_users c
                                where email in (SELECT email FROM westbtl.unisender_delivery_status
                                    where campaign_id = :loc_campaign_id )
                                group by c.activity_loc, c.unisender_send_result ORDER BY 1 desc, 3 ",
                    "params" => [':loc_campaign_id' =>  $local_campaign_id],
                    'totalCount' => $count,

                    'pagination' => [
                        'pageSize' => $count,
                    ],
                ]);
            
        
         return $this->render('campaign-report1', [
            'dataProvider' => $dataProvider,
             'campaign_id'=>$campaign_id, 
             'local_campaign_id'=>$local_campaign_id,
             'message_id'=>$message_id,
             'agency_id'=>$agency_id,
        ]);
            
          
            
            
        }
    
        
    }




    public function actionReplyEmail($id)
    {
        
        $model = \app\models\UnisenderEmail::findOne(['id'=>$id]);
//        $model = new \app\models\UnisenderEmail();
        $model->name = $model->name .' от '. date("d.m.Y");     
        
        
       // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        if (Yii::$app->request->post()) {
            $data= Yii::$app->request->post();
            
            //UnisenderList
           // print_r($data['UnisenderList']['list_title']);
            $model = new \app\models\UnisenderEmail();
            $name = $data['UnisenderEmail']['name']; 
            $list_id =  $data['UnisenderEmail']['list_id']; 
            $email_subject =  $data['UnisenderEmail']['subject']; 
            $body =  $data['UnisenderEmail']['body']; 
            $email_from_name =  $data['UnisenderEmail']['sender_name']; 
            $email_from_email =  $data['UnisenderEmail']['sender_email']; 
            
            $agency_id =  $data['UnisenderEmail']['agency_id'];   
            $answ =  $model->newEmail($name, $list_id, $email_subject, $body, $email_from_name, $email_from_email, $agency_id);
            Yii::$app->session->setFlash('success', $answ['message']);
             $id = $answ['id'];
            return $this->redirect(['email', 'id' => $id]);
        } else {
            return $this->render('replyEmail', [
                'model' => $model,
            ]);
        }
    }
    
    //create_tpl_field
    //create-mail-tpl-fileld?mail_tpl_id=1
    
    //create-mail-tpl-fileld
      public function actionCreateMailTplFileld($mail_tpl_id)
    {
        
        $model = new \app\models\UnisenderMailTplFileds();
        $model->u_mail_tpl_id = $mail_tpl_id;

         if ($model->load(Yii::$app->request->post())  && $model->validate()) {
             $model->dt_create =   date("Y-m-d H:i:s");
             $model->uid_create = Yii::$app->user->id;
                     
            $model->save(false);
            return $this->redirect(['mail-fields?mail_tpl_id='.$mail_tpl_id, ]);
        } else {
            return $this->render('create_tpl_field', [
                'model' => $model,
                'mail_tpl_id'=>$mail_tpl_id
            ]);
        }
        
       // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        if (Yii::$app->request->post()) {
            $data= Yii::$app->request->post();
            
            //UnisenderList
           // print_r($data['UnisenderList']['list_title']);
            $model = new \app\models\UnisenderFields();
            $new_field_name =  $data['UnisenderFields']['new_field_name']; 
            $new_field_type =  'text'; 
            $agency_id =  $data['UnisenderFields']['agency_id'];   
            $answ =  $model->newField($new_field_name, $new_field_type, $agency_id);
            Yii::$app->session->setFlash('success', $answ['message']);
             $id = $answ['id'];
            return $this->redirect(['view-field', 'id' => $id]);
        } else {
            return $this->render('createField', [
                'model' => $model,
            ]);
        }
    }
      public function actionCreateField()
    {
        
        $model = new \app\models\UnisenderFields();

        
        
       // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        if (Yii::$app->request->post()) {
            $data= Yii::$app->request->post();
            
            //UnisenderList
           // print_r($data['UnisenderList']['list_title']);
            $model = new \app\models\UnisenderFields();
            $new_field_name =  $data['UnisenderFields']['new_field_name']; 
            $new_field_type =  'text'; 
            $agency_id =  $data['UnisenderFields']['agency_id'];   
            $answ =  $model->newField($new_field_name, $new_field_type, $agency_id);
            Yii::$app->session->setFlash('success', $answ['message']);
             $id = $answ['id'];
            return $this->redirect(['create-field']);
        } else {
            return $this->render('createField', [
                'model' => $model,
            ]);
        }
    }
    
     public function actionViewField($id)
    {  
         $model = \app\models\UnisenderFields::findOne($id);
         
        return $this->render('viewFiled', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single UnisenderList model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new UnisenderList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        
        $model = new UnisenderList();

        
        
       // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        if (Yii::$app->request->post()) {
            $data= Yii::$app->request->post();
               $cityes =  $data['UnisenderList']['cityes']; 
            
            //UnisenderList
           // print_r($data['UnisenderList']['list_title']);
             $model = new UnisenderList();
             $list_title =  $data['UnisenderList']['list_title']; 
             $agency_id =  $data['UnisenderList']['agency_id'];   
             $confirm_email  =  $data['UnisenderList']['confirm_email'];   
             $campaign_id  =  $data['UnisenderList']['campaign_id'];   
             $answ =  $model->newList($list_title, $agency_id, $confirm_email, $campaign_id);
             $id = $answ['id'];
             
             if ($id){
                if ($id && $cityes){

                   foreach ($cityes as $val) {

                                  $modelsListAvailableCityes = new \app\models\UnisenderListAvailableCityes();
                                  $modelsListAvailableCityes->unisender_list_id = $id;
                                  $modelsListAvailableCityes->city = $val;
                                  $modelsListAvailableCityes->save(false);

                              }
                }           
               Yii::$app->session->setFlash('success', $answ['message']);
             
              return $this->redirect(['view', 'id' => $id]);
             } else {
                 
                Yii::$app->session->setFlash('warning', $answ['message']);
                return $this->redirect(['unisender/create', 'id' => $id]);
                 
             }
              
            
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    
    

    /**
     * Updates an existing UnisenderList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $bokingMebers  = \app\models\UnisenderListAvailableCityes::find()->where(['unisender_list_id'=>$id])->select('city')->asArray()->all();
        $model->cityes = array_values( \yii\helpers\ArrayHelper ::getColumn($bokingMebers, 'city'));

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \app\models\UnisenderListAvailableCityes::deleteAll(['unisender_list_id' => $id]);
             foreach ($model->cityes as $val) {

                               $modelsListAvailableCityes = new \app\models\UnisenderListAvailableCityes();
                               $modelsListAvailableCityes->unisender_list_id = $id;
                               $modelsListAvailableCityes->city = $val;
                               $modelsListAvailableCityes->save(false);

                           }
            
            
            return $this->redirect(['view',  'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing UnisenderList model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UnisenderList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UnisenderList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UnisenderList::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
