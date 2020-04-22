<?php

namespace app\controllers;


use Yii;
use yii\filters\AccessControl;

use app\models\User;
use app\models\Tasks;

use app\models\CsvBatches;
use app\models\QueryModels\CsvBatchesQuery;
use app\models\searchModels\CsvBatchesSearch;
use app\models\searchModels\AdmUsersSearch;
use app\models\searchModels\CrmUsersSearch;
use app\models\CrmUsers;
use app\models\BatchData;
use app\models\CronTask;
use app\models\AgencySettings;
use app\models\CompareDataEmail;
use app\models\UnisenderList;

use app\models\AgencyCsvBatch;


use yii\helpers\ArrayHelper;

use yii\web\UploadedFile;

use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Response;
use yii\widgets\ActiveForm;



use Facebook\Facebook;
use Facebook\Helpers\FacebookRedirectLoginHelper;
use Facebook\Exceptions\FacebookSDKException;

use yii\data\SqlDataProvider;
use yii\helpers\Html;



class UsersController extends Controller
{
//    public function behaviors()
//    {
//        return [
//            'access' => [
//                'class' => AccessControl::className(),
//                'only' => ['logout','lk','list','send-to-crm','delete-file','update'],
//                'rules' => [
//                    [
//                        'actions' => ['logout','lk','list','send-to-crm','delete-file','update'],
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
//                ],
//            ],
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'logout' => ['post'],
//                ],
//            ],
//        ];
//    }

   
 
    public $layout = 'sb-admin';
    
    
    
    public function actionList()
    {
        $searchModel = new AdmUsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionRegistration()
    { 
       
       
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect(['lk']);
        }        
        $model = new User();
            
        if($model->load(Yii::$app->request->post())   && $model->signup()){
            return $this->goBack();
        
                
        } 
         return $this->render('_frmUser', [
            'model' => $model,
        ]);
        
        
    }
    
    public function actionPasswordRecovery(){
        
        $model = new User();
          
            if($model->load(Yii::$app->request->post()) ){
                if ($model->PasswordRecovery()){
                   Yii::$app->session->setFlash('recovery_message','На ваш email выслано письмо!' );
                } else {
                    Yii::$app->session->setFlash('recovery_message_bad','Неправильный email !' );
                }
                return $this->redirect(['users/password-recovery']); 
                
          
            }
            
           return $this->render('_frmEmail', [
                'model' => $model,
            ]);
        
    }




    public function actionEmailActivation($token='')
    {
         Yii::$app->view->params['boneClass'] =  'bone template';
         if(RegistrationToken::setActivation($token) ){
             \app\models\UsersLoginLog::setUserLogin();
                 return $this->redirect(['users/lk']);  
         } else {
             return $this->redirect(['site/error']);  
         }     
           
    }
    
    
   
    
    
    public function actionLk()
    {   
        $CsvSearchModel =  new CsvBatchesSearch();
        $CsvDataProvider = $CsvSearchModel->search(Yii::$app->request->queryParams);
        $csv_upload = new \app\models\CsvUpload();
       
        $user = User::getUserInfo();
        
         if (Yii::$app->request->isPost && $_FILES['CsvUpload']['tmp_name']['file']) {
            $csv_upload->load(Yii::$app->request->post()); 
            if(is_uploaded_file($_FILES['CsvUpload']['tmp_name']['file'])){
                    $csv_upload->file = UploadedFile::getInstance($csv_upload,'file');
                    $csv_upload->file = $csv_upload::saveFile($csv_upload, 'file');
                  }

            }
        
        
            return $this->render('lk', [
            'user'=>$user,  
               'CsvSearchModel'=>$CsvSearchModel,
               'CsvDataProvider'=>$CsvDataProvider,
                'csv_upload'=>$csv_upload
               
                
            ]);
        
        
      
        
       
       
    }
    
    
    public function actionCsvLoad($batch_id=0){
        
        
        //$batch_id =0;
        $group_id =3;
        $count = 0;
            
        $CsvProvider = new ArrayDataProvider([]);
        $columns = [];
        $model = new \app\models\AceptBatch();
         $csv_upload = new \app\models\CsvUpload();
       
        
        
         if (Yii::$app->request->isPost && $_FILES['CsvUpload']['tmp_name']['file']) {
            $csv_upload->load(Yii::$app->request->post()); 
            $data = Yii::$app->request->post();
            $agency_id = $data['CsvUpload']['agency_id']; 
            $dilimer = $data['CsvUpload']['delimer']; 
            if(is_uploaded_file($_FILES['CsvUpload']['tmp_name']['file'])){
                    $csv_upload->file = UploadedFile::getInstance($csv_upload,'file');
                   // $csv_upload->file = $csv_upload::saveArrFile($csv_upload, 'file');
                    $batch_id = $csv_upload::saveArrFile($csv_upload, 'file', $group_id, $agency_id, $dilimer);
                    
                    
                    $this->redirect(['/users/csv-load','batch_id'=>$batch_id ]);
                  }
                  
                 // $batch_id  = AgencyCsvBatch::find(['file_name'=>$csv_upload->file])->select('id')->one();

           
           }
                    $pagination = Yii::$app->request->get();
                    
                    
//                    $page = @$pagination['page'];  
//                     if(!$page)
//                         $page =1;
//
//                     $per_page = @$pagination['per-page'];
//                     $batch_id  = @$pagination['batch_id'];
//                     if (!$per_page)
//                         $per_page = 10;
//
//                    $limit = 16*$per_page;
//                    $offset = $limit*$page;
                    
                    
                    
         $per_page = @$pagination['per-page'];
         if (!$per_page)
             $per_page = 10;
        
        
        
        $limit = 16*$per_page;
        
        
         $page = @$pagination['page'];  
         if(!$page){
             $page =1;
             $offset = 0;
         }  else {  
           $offset = $limit*$page;
           
         }  
        $offset = 0;    
         
                    
                     if(!$batch_id==0){
                        $agency_batch = AgencyCsvBatch::find()->where(['id'=>$batch_id])->one();

                       $count =  $agency_batch->string_count;
                     }

                    $batch = BatchData::find()->select('s.name as param_name, acb.id as sys_name, agency_parametr_id, string_order, batch_data.value')
                                        ->where(['batch_id'=>$batch_id])
                                        ->leftJoin('agency_settings ase', 'agency_parametr_id = ase.id')
                                        ->leftJoin('sys_parametr s', 'ase.sys_parametr_id = s.id')
                                        ->leftJoin('agency_csv_batch acb', 'batch_id = acb.id')
                                        ->limit($limit)
                                        ->offset($offset) 
                                        ->all();

                     $data =   ArrayHelper::map($batch,    'param_name', 'value',  'string_order');



                         if(@$data[1])
                           $columns =  array_keys(@$data[1]);




                       $CsvProvider = new ArrayDataProvider([
                          // 'allModels' => $data,
                           'models' => $data,
                           'totalCount'=>$count,

                              'pagination' => [
                                  'totalCount'=>$count,
                                  'pageSize' => 10,
                              ],
                          ]);
         
           
             $model->batch_id = $batch_id;
        
            return $this->render('csvLoad', [
                'batch_id'=>$batch_id,  
                'CsvProvider'=>$CsvProvider,
                'columns'=>$columns,
               'csv_upload'=>$csv_upload,
               'model'=>$model
                
               
                
            ]);
    }
    
    
    public function actionCsvLoadHuge(){
        
                
        $searchModel = new CsvBatchesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $batch_id = $searchModel->batch_id;
         $csv_upload = new \app\models\CsvUpload();
       
        
        
         if (Yii::$app->request->isPost && $_FILES['CsvUpload']['tmp_name']['file']) {
            $csv_upload->load(Yii::$app->request->post()); 
            $data = Yii::$app->request->post();
            $agency_id = $data['CsvUpload']['agency_id']; 
            $dilimer = $data['CsvUpload']['delimer']; 
            $campaign_id = $data['CsvUpload']['campaign_id']; 
            if(is_uploaded_file($_FILES['CsvUpload']['tmp_name']['file'])){
                    $csv_upload->file = UploadedFile::getInstance($csv_upload,'file');
                    //$csv_upload->file = $csv_upload::saveHugeFile($csv_upload, 'file', $agency_id, $dilimer);
                     $batch_id = $csv_upload::saveHugeFile($csv_upload, 'file', $agency_id, $dilimer, $campaign_id);
                   // $batch_id = $csv_upload::saveArrFile($csv_upload, 'file', $group_id, $agency_id, $dilimer);
                    
                    
                    $this->redirect(['/users/csv-load-huge','CsvBatchesSearch[batch_id]'=>$batch_id ]);
                  }
                 
           }
                   
        
            return $this->render('csvLoadHuge', [
               'model' => $searchModel,
               'searchModel' => $searchModel,
               'dataProvider' => $dataProvider,
               'csv_upload'=>$csv_upload,
                'batch_id'=>$batch_id
               
                
               
                
            ]);
    }
    
    
    
    
    public function actionCsvLoadHuge2($batch_id=0){
        
                
        //$searchModel = new CrmUsersSearch();
        $searchModel = new CrmUsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        
         $csv_upload = new \app\models\CsvUpload();
       
        
        
         if (Yii::$app->request->isPost && $_FILES['CsvUpload']['tmp_name']['file']) {
            $csv_upload->load(Yii::$app->request->post()); 
            $data = Yii::$app->request->post();
            $agency_id = $data['CsvUpload']['agency_id']; 
            $dilimer = $data['CsvUpload']['delimer']; 
            $campaign_id = $data['CsvUpload']['campaign_id']; 
            if(is_uploaded_file($_FILES['CsvUpload']['tmp_name']['file'])){
                    $csv_upload->file = UploadedFile::getInstance($csv_upload,'file');
                    //$csv_upload->file = $csv_upload::saveHugeFile($csv_upload, 'file', $agency_id, $dilimer);
                     $batch_id = $csv_upload::saveHugeFile($csv_upload, 'file', $agency_id, $dilimer, $campaign_id);
                   // $batch_id = $csv_upload::saveArrFile($csv_upload, 'file', $group_id, $agency_id, $dilimer);
                    
                    
                    $this->redirect(['/users/csv-load-huge','batch_id'=>$batch_id ]);
                  }
                 
           }
                   
        
            return $this->render('csvLoadHuge', [
               'searchModel' => $searchModel,
               'dataProvider' => $dataProvider,
               'csv_upload'=>$csv_upload,
               
                
               
                
            ]);
    }
    
    
  
    


    
    public function actionCrmUsers(){
        $this->layout = 'simple-load-layout';
                
        //$searchModel = new CrmUsersSearch();
        $searchModel = new CrmUsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        
        
                   
        
            return $this->render('crmUsers', [
               'searchModel' => $searchModel,
               'dataProvider' => $dataProvider,
               
                
            ]);
    }
    
    
    
    
     public function actionAceptBatch2(){
       
       $limit = 99; 
         
        
       $data = Yii::$app->request->post();
       $list_id = $data['CsvBatchesSearch']['list_id'];
       $batch_id = $data['CsvBatchesSearch']['batch_id']; 
       $agency_id = $data['CsvBatchesSearch']['agency_id']; 
       
       AgencyCsvBatch::updateAll(['last_list_id'=>$list_id], "id = $batch_id");
       
       //\app\models\CronTask::addTask('SendContacts', $batch_id, $limit, $list_id);
       \app\models\CronTask::addTask('SubscribeContacts2', $batch_id, $limit, $list_id);
      // \app\models\CronTask::addTask('insertUsers', $batch_id, $limit, $list_id);
       
      //  $res  = \app\models\AceptBatch::sendContacts($list_id, $batch_id);
      
     
     Yii::$app->session->setFlash('success', 'Задача поставлена в очередь, на загрузку контактов.' );
    
    return $this->redirect(['users/check-batch-status-2', 'batch_id'=>$batch_id, 'list_id'=>$list_id ]);
       
   //  $res  = \app\models\AceptBatch::sendContacts($list_id, $batch_id);
   //  Yii::$app->session->setFlash('success', $res['message'] );
   // return $this->redirect(['users/csv-load']);  
        
        
    }
    
    public function actionDeleteBatch($id){
        
        
        
        $batch = AgencyCsvBatch::find()->where(['id'=>$id])->one();
        $batch->delete();
        return $this->redirect(['users/simple-batches']);  
        
        
    }
    
    public function actionBatches($agency_id=1){
        
         $dataProvider = new ActiveDataProvider([
          //  'query' => AgencyCsvBatch::find()->where(['agency_id'=>$agency_id])->with('agency')->with('createU'),
            'query' => AgencyCsvBatch::find()->with('agency')
                 ->with('createU')
                 ->andWhere('id >= 54')->orderBy(['id'=>SORT_DESC]),
        ]);

        return $this->render('batches', [
            'dataProvider' => $dataProvider,
        ]);
        
        
        
    }
    
    
    public function actionAceptBatch(){
       
       $limit = 100; 
         
        
       $data = Yii::$app->request->post();
       $list_id = $data['AceptBatch']['list_id'];
       $batch_id = $data['AceptBatch']['batch_id']; 
       $agency_id = $data['AceptBatch']['agency_id']; 
       
       AgencyCsvBatch::updateAll(['last_list_id'=>$list_id], "id = $batch_id");
       
       //\app\models\CronTask::addTask('SendContacts', $batch_id, $limit, $list_id);
       \app\models\CronTask::addTask('SubscribeContacts', $batch_id, $limit, $list_id);
       \app\models\CronTask::addTask('insertUsers', $batch_id, $limit, $list_id);
       
      //  $res  = \app\models\AceptBatch::sendContacts($list_id, $batch_id);
      
     
     Yii::$app->session->setFlash('success', 'Задача поставлена в очередь.' );
    
    return $this->redirect(['users/check-batch-status', 'batch_id'=>$batch_id, 'list_id'=>$list_id ]);
       
   //  $res  = \app\models\AceptBatch::sendContacts($list_id, $batch_id);
   //  Yii::$app->session->setFlash('success', $res['message'] );
   // return $this->redirect(['users/csv-load']);  
        
        
    }
    
    
    public function actionCrmUsersList($batch_id){
       
        
        $status =  \app\models\CrmUsers::updateStatus($batch_id);
        
        
        if ($status){
                    Yii::$app->session->setFlash('success',  'Операция выполнена' );
        } else {
            Yii::$app->session->setFlash('danger',  'По данному файлу небыло рассылок. Просмотр результата невозможен!' );
        }
        
        $CrmUsersSearchModel =  new CrmUsersSearch();
        $CrmUsersDataProvider = $CrmUsersSearchModel->search(Yii::$app->request->queryParams, $batch_id);
          
        
        
        
        
         $columns = [
            'email',
            'phone',
            'f_name',
            'l_name',
            'p_name',
            'age',
            'gender',
            'priority_mark1',
            'priority_mark2',
            'hostess_id',
            'activity_dt',
            'activity_loc',
            'test_res',
            'test_id',
            'activity_type',
            'activity_id',
            'advanced_data',
            'user_key',
            'rec_status',
            'last_dt',
            'unisender_send_result',
                 ['label'=>' Расшифровка',
                    'format' => 'raw',
                    'value'=> function ($data){ return \app\models\UnisenderDeliveryStatus::DileveryStatusList($data->unisender_send_result);  }   

                   ],
             ];
             
           
        
            return $this->render('crmUsersGrid', [
                'batch_id'=>$batch_id,  
                'CsvProvider'=>$CrmUsersDataProvider,
                'columns'=>$columns,
                'model'=>$CrmUsersSearchModel
              
               
                
            ]);
        
    }
    
    


    public function actionPurgeBatch(){
        
       $limit = 100; 
        
       $data = Yii::$app->request->post();
       $list_id = $data['AceptBatch']['list_id'];
       $batch_id = $data['AceptBatch']['batch_id']; 
       $agency_id = $data['AceptBatch']['agency_id']; 
       
       AgencyCsvBatch::updateAll(['last_list_id'=>$list_id], "id = $batch_id");
       \app\models\CronTask::addTask('SendContacts', $batch_id, $limit, $list_id);
       
      //  $res  = \app\models\AceptBatch::sendContacts($list_id, $batch_id);
      
     
     Yii::$app->session->setFlash('success', 'Задача поставлена в очередь.' );
    
    return $this->redirect(['users/check-batch-status', 'batch_id'=>$batch_id, 'list_id'=>$list_id ]);  
        
        
    }


    public function actionCheckBatchStatus($batch_id=0, $list_id=0){
        
        $contacts = \app\models\UnisenderContacts::find()->select('
            sum(total) as total, 
            sum(inserted) as inserted, 
            sum(updated) as updated, 
            sum(deleted) as deleted, 
            sum(new_emails) as new_emails, 
            sum(invalid) as invalid
            
            ')->where(['batch_id'=>$batch_id, 'list_id'=>$list_id]);
        $provider = new ActiveDataProvider([
                'query' => $contacts,
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]);
        
        $provider = new ActiveDataProvider([
                'query' => \app\models\UnisenderSubscribe::find()->where(['batch_id'=>$batch_id, 'list_id'=>$list_id]),
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]);
        
        $percent = CronTask::getPercentContacts($batch_id, $list_id, 'SubscribeContacts');
        
        return $this->render('checkBatchStatus',[
                                'batch_id'=>$batch_id, 
                                'list_id'=>$list_id,
                                'provider'=>$provider,
                                'percent'=>$percent
                        ]);
    }
    
    
    

    public function actionPurgeBatch2(){
        
       $limit = 100; 
        
       $data = Yii::$app->request->post();
       $list_id = $data['AceptBatch']['list_id'];
       $batch_id = $data['AceptBatch']['batch_id']; 
       $agency_id = $data['AceptBatch']['agency_id']; 
       
       AgencyCsvBatch::updateAll(['last_list_id'=>$list_id], "id = $batch_id");
       \app\models\CronTask::addTask('SendContacts2', $batch_id, $limit, $list_id);
       
      //  $res  = \app\models\AceptBatch::sendContacts($list_id, $batch_id);
      
     
     Yii::$app->session->setFlash('success', 'Задача поставлена в очередь.' );
    
    return $this->redirect(['users/check-batch-status-2', 'batch_id'=>$batch_id, 'list_id'=>$list_id ]);  
        
        
    }


    public function actionCheckBatchStatus2($batch_id=0, $list_id=0){
        
        $contacts = \app\models\UnisenderContacts::find()->select('
            sum(total) as total, 
            sum(inserted) as inserted, 
            sum(updated) as updated, 
            sum(deleted) as deleted, 
            sum(new_emails) as new_emails, 
            sum(invalid) as invalid
            
            ')->where(['batch_id'=>$batch_id, 'list_id'=>$list_id]);
        $provider = new ActiveDataProvider([
                'query' => $contacts,
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]);
        
        $provider = new ActiveDataProvider([
                'query' => \app\models\UnisenderSubscribe::find()->where(['batch_id'=>$batch_id, 'list_id'=>$list_id]),
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]);
        
        $percent = CronTask::getPercentContacts($batch_id, $list_id, 'SubscribeContacts2');
        
        return $this->render('checkBatchStatus_2',[
                                'batch_id'=>$batch_id, 
                                'list_id'=>$list_id,
                                'provider'=>$provider,
                                'percent'=>$percent
                        ]);
    }
    
    





    public function actionUpdate($id){
        
        $model = User::findOne(['id'=>$id]);
        $userCityes  = \app\models\UserAvailableCityes::find()->where(['user_id'=>$id])->select('city')->asArray()->all();
        $model->cityes = array_values( \yii\helpers\ArrayHelper::getColumn($userCityes, 'city'));

        
         if ($model->load(Yii::$app->request->post()) ) {
             
           
                     //findOne(['user_id'=>$id]);
             
               

                 
                     $model->save(false);
                           
                            \app\models\AuthAssignment::updateAll(['item_name' => $model->role], 'user_id = '.$id);

                            \app\models\UserAvailableCityes::deleteAll(['user_id' => $id]);
                            foreach ($model->cityes as $val) {

                                              $modeluserAvailableCityes = new \app\models\UserAvailableCityes();
                                              $modeluserAvailableCityes->user_id = $id;
                                              $modeluserAvailableCityes->city = $val;
                                              $modeluserAvailableCityes->save(false);

                                          }
                         
                 

            
             
             Yii::$app->session->setFlash('success', 'Пользователь обновлен.');
            
            
            //return $this->redirect(['list']);
            return $this->redirect(['update',   'id' => $model->id, 'cityes'=>  implode(',', $model->cityes)  ]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
        
        
    }
    
    
    public function actionUpdateProfile($uid)
    {
        
         if ($a='lock'){
             User::changeStatus($uid, $status_id);
         }
         if ($a='role'){
             User::changeRole($uid, $set_role);
         }
         return $this->redirect(['users/list']);  
       
    }
    
    
    
    public function actionSimpleLoad($batch_id=161){
        
           $title = '';
           
           $batch_model = AgencyCsvBatch::find()->where(['id'=>$batch_id])->one();
         //  $batch_steps = \app\models\BatchSteps::find()->where(['batch_id'=>$batch_id])->all();
           
           $query =  \app\models\BatchSteps::find();
           $query->andFilterWhere(['batch_id'=>$batch_id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
           
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            
              AgencyCsvBatch::setCampaignInfo($batch_id, $data['AgencyCsvBatch']['campaign_name'], $data['AgencyCsvBatch']['email_tpl_id']);
              \app\models\BatchSteps::runEmailValidation($batch_id);
            $this->redirect(['/users/simple-load','batch_id'=>$batch_id ]);
          
            //print_r($data);
        }
        
           
           $this->layout = 'simple-load-layout';
           return $this->render('simple-load/card', 
                   [
                        'batch_model' => $batch_model,
                        'StepsDataProvider' => $dataProvider,
                       
                    ]
                   );
        
    }

    
    public function actionSimpleBatches($agency_id=1, $campaign=35){
        $this->layout = 'simple-load-layout';
         $dataProvider = new ActiveDataProvider([
          //  'query' => AgencyCsvBatch::find()->where(['agency_id'=>$agency_id])->with('agency')->with('createU'),
            'query' => AgencyCsvBatch::find()->with('agency')
                 ->with('createU')
                 ->where(['campaign_id' => $campaign])
                 ->andWhere('id >= 54')
                 
                 ->andWhere(['not in', 'id', ['162','163','164','165','166','167','168','169','162','170','171','172','173','174','175','176','177','178','179','180','181']])
                 ->orderBy(['id'=>SORT_DESC]),
        ]);

         $csv_upload = new \app\models\CsvUpload();
       
        
        
         if (Yii::$app->request->isPost && $_FILES['CsvUpload']['tmp_name']['file']) {
            $csv_upload->load(Yii::$app->request->post()); 
            $data = Yii::$app->request->post();
            $agency_id = 2; 
            $dilimer = $data['CsvUpload']['delimer']; 
            $campaign_id = $data['CsvUpload']['campaign_id']; 
            $campaign_name = $data['CsvUpload']['campaign_name']; 
            if(is_uploaded_file($_FILES['CsvUpload']['tmp_name']['file'])){
                    $csv_upload->file = UploadedFile::getInstance($csv_upload,'file');
                    //$csv_upload->file = $csv_upload::saveHugeFile($csv_upload, 'file', $agency_id, $dilimer);
                     $batch_id = $csv_upload::saveSimpleHugeFile($csv_upload, 'file', $agency_id, $dilimer, $campaign_id, $campaign_name);
                   // $batch_id = $csv_upload::saveArrFile($csv_upload, 'file', $group_id, $agency_id, $dilimer);
                    
                    
                    $this->redirect(['/users/simple-load','batch_id'=>$batch_id ]);
                  }
                 
           }
                   
         
        return $this->render('simple-load/batch_list', [
            'csv_upload'=>$csv_upload,
            'dataProvider' => $dataProvider,
            'campaign'=>$campaign
        ]);
        
        
        
    }
    
    
    
    public function actionSimpleCommentBatch(){
        if (Yii::$app->request->isPost ) {
            
            $data =   Yii::$app->request->post();
            $batch_id = $data['AgencyCsvBatch']['id']; 
            $batch_comment = $data['AgencyCsvBatch']['batch_comment']; 
            $model = AgencyCsvBatch::find()->where(['id'=>$batch_id])->one();
            $model->batch_comment = $batch_comment;
            $model->update(false);
            
        $this->redirect(['/users/simple-load','batch_id'=>$batch_id ]);    
        }
        
        
    }






    public function actionSimpleCsvHuge(){
        
        $this->layout = 'simple-load-layout';
        $searchModel = new CsvBatchesSearch();
        
        $cityes_array = [];
        $hostess_array = [];
        $validate_status_array = [];
        $gender_array = [];
       
//            
        $data= Yii::$app->request->queryParams;
        
        if (isset($data['CsvBatchesSearch']['activity_loc_ar'])){
          $cityes =  $data['CsvBatchesSearch']['activity_loc_ar']; 
            if ($cityes){
             foreach ($cityes as $val) {

                                     $cityes_array[] = $val;


                                  }
                    }
        }
        if (isset($data['CsvBatchesSearch']['hostess_name_ar'])){
          $hostess =  $data['CsvBatchesSearch']['hostess_name_ar']; 
            if ($hostess){
             foreach ($hostess as $val) {

                                     $hostess_array[] = $val;


                                  }
                    }
        }
        if (isset($data['CsvBatchesSearch']['validate_status_ar'])){
          $statuses =  $data['CsvBatchesSearch']['validate_status_ar']; 
            if ($statuses){
             foreach ($statuses as $val) {

                                     $validate_status_array[] = $val;


                                  }
                    }
        }
        if (isset($data['CsvBatchesSearch']['gender_ar'])){
          $genders =  $data['CsvBatchesSearch']['gender_ar']; 
            if ($genders){
             foreach ($genders as $val) {

                                     $gender_array[] = $val;


                                  }
                    }
        }
        
        if (isset($data['CsvBatchesSearch']['batch_id'])){
            $batch_id = $data['CsvBatchesSearch']['batch_id'];
        }
             
        
         $searchModel->activity_loc_ar = $cityes_array;
         $searchModel->gender_ar = $gender_array;
         $searchModel->hostess_name_ar = $hostess_array;
         $searchModel->validate_status_ar = $validate_status_array;
         
         
         $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
         
         $csv_upload = new \app\models\CsvUpload();
       
        
        
                   
        
            return $this->render('simple-load/csvHuge', [
               'model' => $searchModel,
               'searchModel' => $searchModel,
               'dataProvider' => $dataProvider,
               'csv_upload'=>$csv_upload,
                'batch_id'=>$batch_id,
                'cityes_array'=>$cityes_array   
               
                
               
                
            ]);
    }
    
    
     public function actionSimpleAllBatches($campaign=35){
        $this->layout = 'simple-load-layout';
                
        //$searchModel = new CrmUsersSearch();
        $searchModel = new CsvBatchesSearch();
       // $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

       // $campaign = $searchModel->campaign_id;
        
        
        
        $cityes_array = [];
        $hostess_array = [];
        $validate_status_array = [];
        $gender_array = [];
//        
       
//      
        
        $data= Yii::$app->request->queryParams;
        
       
        
         if (isset($data['CsvBatchesSearch']['validate_status_ar'])
                 && $data['CsvBatchesSearch']['validate_status_ar']!=''){
          $statuses =  $data['CsvBatchesSearch']['validate_status_ar']; 
            if ($statuses){
             foreach ($statuses as $val) {
                                     $validate_status_array[] = $val;

                                  }
                    }
                    $searchModel->validate_status_ar = $validate_status_array;
        }
        
        if (isset($data['CsvBatchesSearch']['activity_loc_ar']) && $data['CsvBatchesSearch']['activity_loc_ar']!=''){
          $cityes =  $data['CsvBatchesSearch']['activity_loc_ar']; 
            if ($cityes){
             foreach ($cityes as $val) {

                                     $cityes_array[] = $val;


                                  }
                    }
                    $searchModel->activity_loc_ar = $cityes_array;
         
        }
        
        
        if (isset($data['CsvBatchesSearch']['hostess_name_ar']) && $data['CsvBatchesSearch']['hostess_name_ar']!=''){
          $hostess =  $data['CsvBatchesSearch']['hostess_name_ar']; 
            if ($hostess){
             foreach ($hostess as $val) {

                                     $hostess_array[] = $val;


                                  }
                    }
                    
                    $searchModel->hostess_name_ar = $hostess_array;
         
        } 
       
        if (isset($data['CsvBatchesSearch']['gender_ar']) && $data['CsvBatchesSearch']['gender_ar']!=''){
          $genders =  $data['CsvBatchesSearch']['gender_ar']; 
            if ($genders){
             foreach ($genders as $val) {

                                     $gender_array[] = $val;


                                  }
                    }
                    
         
         $searchModel->gender_ar = $gender_array;
                    
         
        }
        
        if (isset($data['CsvBatchesSearch']['batch_id']) && $data['CsvBatchesSearch']['batch_id']!=''){
            $batch_id = $data['CsvBatchesSearch']['batch_id'];
        }
        if (isset($data['CsvBatchesSearch']['campaign_id']) && $data['CsvBatchesSearch']['campaign_id']!=''){
            $campaign = $data['CsvBatchesSearch']['campaign_id'];
        }
             
        
        // $searchModel->activity_loc_ar = $cityes_array;
         
         
         $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
         
        
            return $this->render('simple-load/all-batches', [
               'searchModel' => $searchModel,
               'dataProvider' => $dataProvider,
                'campaign'=>$campaign
               
                
            ]);
    }
    
   
    public function actionSimpleCsvReport($batch_id=0, $activity_loc = '' ){
        
        
        
        $this->layout = 'simple-load-layout';
        $batch = AgencyCsvBatch::findOne(['id'=>$batch_id]);
        
        
        if ($activity_loc==''){
            $SQL = "SELECT count(*) as c FROM (SELECT  activity_loc,   validate_status, count(*) as c FROM westbtl.csv_batches 
                        where batch_id = $batch_id
                        group by validate_status, activity_loc
                       ) as z ";
                               $db = Yii::$app->db;
                              $count =  $db->createCommand($SQL)->queryAll();
                              $count = count($count);



                              
//                 " SELECT  z.activity_loc,    
//                                        z.validate_status, count(z.id) as c,
//                                     z.batch_id FROM 
//                                     ( select id,  
//                                        case 
//                                     when validate_status is null then 'нет email'
//                                     when validate_status =null then 'нет email'
//                                     when validate_status ='passed' then 'Валидные email'
//                                      when validate_status ='failed' then 'Не валидные email'
//                                      when validate_status ='unprocessed' then 'Не валидные email'
//                                      when validate_status ='error' then 'Не валидные email'
//                                      else 'нет email'
//                                     end as
//                                    validate_status, 
//                                    activity_loc, 
//                                     batch_id  from 
//
//                                     westbtl.csv_batches 
//                                       where batch_id = :batch_id 
//                                            
//                                            ) z
//
//
//                                       group by  z.activity_loc,  z.validate_status,  z.batch_id
//                                  order by  z.activity_loc,  c", 
                             
                              
                              
                $dataProvider = new SqlDataProvider([
                    'sql' =>
                                     " SELECT  z.activity_loc,    
                                        z.validate_status, count(z.id) as c,
                                     z.batch_id FROM 
                                     ( select id,  
                                        case 
                                     
                                        -- when email_availability is null and validate_status is null then 'Нет email'
                                        when validate_status ='canceled' then  'Отменен по просьбе BTL'
                                        -- when validate_status = 'failed' then  'email_not_valid'
                                        when validate_status ='passed' then  'Валидные email'
                                        when validate_status ='unknown' then  'Валидируется'
                                        when validate_status ='error' then  'Валидируется'
                                        when validate_status ='unprocessed' then  'Не валидные email'
                                        when validate_status ='failed' then  'Не валидные email'
                                       
                                     end as validate_status,
                                    activity_loc, 
                                     batch_id  from 

                                     westbtl.csv_batches 
                                       where batch_id = :batch_id 
                                            
                                            ) z


                                       group by  z.activity_loc,  z.validate_status,  z.batch_id
                                  order by  z.activity_loc,  c",
                      "params" => [':batch_id' => $batch_id],
                                    'totalCount' => $count,
                                    'pagination' => [
                                        'pageSize' => 200,
                                    ],
                ]);

            
            
            
             $gridColumns = [
                          //  ['class' => 'kartik\grid\SerialColumn'],
                               [
                                'attribute'=>'activity_loc', 
                                'width'=>'250px',
                                 'label'=>'Город',
                                 'value'=>function ($model) {return Html::a($model['activity_loc'], '/users/simple-csv-report?batch_id='.$model['batch_id'].'&activity_loc='.$model['activity_loc'],
                                                ['title' => Yii::t('yii', 'Отчет'), 'data-pjax' => '0']);
                                 },
                                         'format' => 'raw',
                                  'group'=>true,  // enable grouping
                            ],

                            [
                                'attribute'=>'validate_status', 
                                'width'=>'200px',
                                'label'=>'Статус e-mail адреса',

                            ],

                            [
                                'attribute'=>'c', 
                                'width'=>'150px',
                                'label'=>'Кол-во',

                            ],



                        ];
        } else {
          
                              
                  
        
        
//            $SQL = "SELECT count(*) as c FROM (SELECT  activity_loc,   validate_status, count(*) as c FROM westbtl.csv_batches 
//                        where batch_id = $batch_id
//                        group by validate_status, activity_loc
//                       ) as z ";
//                               $db = Yii::$app->db;
//                              $count =  $db->createCommand($SQL)->queryAll();
//                              $count = count($count);
//
//
//                $dataProvider = new SqlDataProvider([
//                    'sql' =>
//                    'SELECT  activity_loc,   validate_status , count(*) as c, batch_id FROM westbtl.csv_batches 
//                        where batch_id = :batch_id
//                        group by validate_status, activity_loc, batch_id
//                       order by activity_loc, hostess_name, c',
//                      "params" => [':batch_id' => $batch_id],
//                                    'totalCount' => $count,
//                                    'pagination' => [
//                                        'pageSize' => 200,
//                                    ],
//                ]);      
     
                        
                
                  
             $SQL = "SELECT count(*) as c FROM (SELECT  hostess_name,   validate_status, count(*) as c FROM westbtl.csv_batches 
                        where batch_id = $batch_id
                            and activity_loc = '$activity_loc'
                        group by validate_status, hostess_name
                       ) as z ";
                               $db = Yii::$app->db;
                              $count =  $db->createCommand($SQL)->queryAll();
                              $count = count($count);

                              
//                                " SELECT  z. hostess_name,    
//                                        z.validate_status, count(z.id) as c,
//                                     z.batch_id FROM 
//                                     ( select id,  
//                                        case 
//                                     when validate_status is null then 'нет email'
//                                     when validate_status =null then 'нет email'
//                                     when validate_status ='passed' then 'Валидные email'
//                                      when validate_status ='failed' then 'Не валидные email'
//                                      when validate_status ='unprocessed' then 'Не валидные email'
//                                      when validate_status ='error' then 'Не валидные email'
//                                      else 'нет email'
//                                     end as
//                                    validate_status, 
//                                                 hostess_name, 
//                                     batch_id  from 
//
//                                     westbtl.csv_batches 
//                                       where batch_id = :batch_id 
//                                       and activity_loc = :activity_loc
//                                            
//                                            ) z
//
//
//                                       group by z. hostess_name, z.validate_status,  z.batch_id
//                                  order by  z. hostess_name,  c",

                              
                   

                $dataProvider = new SqlDataProvider([
                    'sql' =>
                     " SELECT  z. hostess_name,    
                                        z.validate_status, count(z.id) as c,
                                     z.batch_id FROM 
                                     ( select id,  
                                        case 
                                      
                                       
                                        when validate_status ='canceled' then  'Отменен по просьбе BTL'
                                        -- when validate_status = 'failed' then  'email_not_valid'
                                                when email_availability is null and validate_status ='passed'  then  'Валидные email'
                                        when validate_status ='passed' then  'Валидные email'
                                        when validate_status ='unknown' then  'Валидируется'
                                        when validate_status ='error' then  'Валидируется'
                                        when validate_status ='unprocessed' then  'Не валидные email'
                                        when validate_status ='failed' then  'Не валидные email'
                                        
                                     end as validate_status,
                                                 hostess_name, 
                                     batch_id  from 

                                     westbtl.csv_batches 
                                       where batch_id = :batch_id 
                                       and activity_loc = :activity_loc
                                            
                                            ) z


                                       group by z. hostess_name, z.validate_status,  z.batch_id
                                  order by  z. hostess_name,  c",
                      "params" => [':batch_id' => $batch_id, ':activity_loc'=>$activity_loc ],
                                    'totalCount' => $count,
                                    'pagination' => [
                                        'pageSize' => 400,
                                    ],
                ]);
            
            
            
             $gridColumns = [
                                        ['class' => 'kartik\grid\SerialColumn'],

                                        [
                                            'attribute'=>'hostess_name', 
                                            'width'=>'200px',
                                            'label'=>'hostess name',
                                            'group'=>true,
                                            
                                        ],
                                        [
                                            'attribute'=>'validate_status', 
                                            'width'=>'200px',
                                            'label'=>'Статус e-mail адреса',
                                            
                                        ],
                                        
                                        [
                                            'attribute'=>'c', 
                                            'width'=>'150px',
                                            'label'=>'Кол-во',
                                            
                                        ],
                                        
                                    
                                 
                                    ];
        }
        
        
        return $this->render('simple-load/simple_csv_report', [
            'gridColumns'=>$gridColumns,
            'dataProvider' => $dataProvider,
            'activity_loc'=>$activity_loc,
            'batch'=>$batch
        ]);
        
        
    }
    
   public function actionRerunValidation($batch_id, $validate_statuses = 'error')
    {
      
          
             $count = \app\models\BatchSteps::reRunValidation($batch_id, $validate_statuses);
             Yii::$app->session->setFlash('warning', "Обновленно $count записей" );
      
        
         $this->redirect(['/users/simple-batch-tasks','batch_id'=>$batch_id ]);
        // $this->redirect(['/users/simple-load','batch_id'=>$batch_id ]);
      
    }
    
    
    
    /**
     * mode = 0 - отобразить текущее состояние
     * mode = 1 - запустить создание списков для даанной пачки
     * mode = 2 - запустить подписку
     * mode = 3 - экспорт контактов
     * mode = 4 - запуск рассылки
     */ 
    public function actionSimpleUnisenderValidation($batch_id, $mode=0){
        
        $this->layout = 'simple-load-layout';
        if ($mode==1){
            
             
            $a = \app\models\Agency::setListsForBatch($batch_id);
            $h = count($a);
            $ret =  \app\models\BatchSteps::saveStep($batch_id, 'Созданы списки для UNKNOWN', "Создано  $h списков ", 7, 1);
            Yii::$app->session->setFlash('warning', "Обновленно записей" );
            $this->redirect(['/users/simple-unisender-validation','batch_id'=>$batch_id, 'CsvBatchesSearch[batch_id]'=>$batch_id ]);
        }
        if ($mode==2){
            
             $db = Yii::$app->db;
             $SQL = "select distinct list_id as list_id from westbtl.csv_batches
                        where batch_id = $batch_id and validate_status = 'unknown'";

             $db = Yii::$app->db;
             $steps =  $db->createCommand($SQL)->queryAll();

            foreach ($steps as $step) {
                  if ($step['list_id']){
                    $limit =49;
                     $res =  CronTask::addTask('subscribeContactsUnknown', $batch_id, $limit, $step['list_id']);
                  }  
            }                       
            
            $ret =  \app\models\BatchSteps::saveStep($batch_id, 'Запуск подписки контактов UNKNOWN', "Задачи по подписке, поставлена в очередь. Данные обновляются каждые 2 минуты.", 8, 1);
            
             Yii::$app->session->setFlash('warning', "Задачи по подписке, поставлена в очередь. Данные обновляются каждые 2 минуты." );
             $this->redirect(['/users/simple-unisender-validation','batch_id'=>$batch_id, 'CsvBatchesSearch[batch_id]'=>$batch_id ]);
        }
        if ($mode==3){
            
            
             $db = Yii::$app->db;
//            
//             $SQL = "select distinct list_id as list_id from westbtl.csv_batches
//                        where batch_id = $batch_id and validate_status = 'unknown'";
//             
             $SQL = "select distinct list_id as list_id from westbtl.csv_batches
                        where batch_id = $batch_id ";

             $db = Yii::$app->db;
             $steps =  $db->createCommand($SQL)->queryAll();

            foreach ($steps as $step) {
                //$task_name, $batch_id,  $step_limit, $list_id=0, $validate_statuses=''
                if ($step['list_id']){
                    $limit =49;
                    $res =  CronTask::addTask('UnisenderExportContacts', $batch_id, $limit, $step['list_id']);
                }
            }                       
            
            $ret =  \app\models\BatchSteps::saveStep($batch_id, 'Запуск экспорта  UNKNOWN', "Задачи по выгрузке контактов, поставлена в очередь. Данные обновляются каждые 2 минуты.", 9, 1);
             Yii::$app->session->setFlash('warning', "Задачи по выгрузке контактов, поставлена в очередь. Данные обновляются каждые 2 минуты." );
             $this->redirect(['/users/simple-unisender-validation','batch_id'=>$batch_id, 'CsvBatchesSearch[batch_id]'=>$batch_id ]);
        }
        
        if ($mode==4){
             Yii::$app->session->setFlash('warning', " Рассылка запущена." );
             
             $ret =  \app\models\BatchSteps::runListCreation($batch_id, 1);
             
             $ret =  \app\models\BatchSteps::saveStep($batch_id, 'Запуск рассылки UNKNOWN', "Запущена новая рассылка по проверенным контактам", 10, 1);
             
            $this->redirect(['/users/simple-load','batch_id'=>$batch_id ]);
        }
        
        $batch = AgencyCsvBatch::find()->where(['id'=>$batch_id])->one();
        
        
        
          $SQL = "SELECT   
                    count(id) as c FROM westbtl.csv_batches
                    where batch_id = $batch_id
                    group by  email_status, email_availability ";
               $db = Yii::$app->db;
              $count =  $db->createCommand($SQL)->queryAll();
              $count = count($count);

               
                $dataProvider = new SqlDataProvider([
                    'sql' => "SELECT   
                                email_status,
                                email_availability,
                                        case    when email_status is null then 'адрес не доступен'
                                                when email_status =     'new' then 'новый'
                                                 when email_status = 'invited' then 'отправлено приглашение со ссылкой подтверждения подписки, ждём ответа, рассылка по такому адресу пока невозможна'
                                                 when email_status = 'active' then 'активный адрес, возможна рассылка'
                                                 when email_status = 'inactive' then 'адрес отключён через веб-интерфейс, никакие рассылки невозможны, но можно снова включить через веб-интерфейс'
                                                 when email_status = 'unsubscribed' then 'адресат отписался от всех рассылок'
                                                 when email_status = 'blocked' then 'адрес заблокирован администрацией нашего сервиса (например, по жалобе адресата), рассылка по нему невозможна. Разблокировка возможна только по просьбе самого адресата'
                                                 when email_status = 'activation_requested' then 'запрошена активация адреса у администрации UniSender, рассылка пока невозможна'
                                                 end as email_status,
                                 case
                                 when email_availability is null then 'данные не поступали'
                                 when email_availability  = 'available' then 'адрес доступен'
                                 when 	email_availability  = 'unreachable' then 'адрес недоступен'
                                 when 	email_availability  = 'temp_unreachable' then 'адрес временно недоступен'
                                 when 	email_availability  = 'mailbox_full' then 'почтовый ящик переполнен'
                                 when 	email_availability  = 'spam_rejected' then 'письмо сочтено спамом сервером получателя. Через несколько дней этот статус будет снят.'
                                 when 	email_availability  = 'spam_folder' then 'письмо помещено в спам самим получателем.'
                                                 end as email_availability,
                                    count(id) as c FROM csv_batches
                                    where batch_id = :batch_id
                                      and validate_status = 'unknown'
                                    group by  email_status, email_availability
                                    order by  email_availability, c",
                    "params" => [':batch_id' => $batch_id],
                    'totalCount' => $count,

                    'pagination' => [
                        'pageSize' => 50,
                    ],
                ]);
        
        $searchModel = new CsvBatchesSearch();
        $dataProviderCsv = $searchModel->search(Yii::$app->request->queryParams);

                
                
        return $this->render('simple-load/unisender-validator', [
             'dataProvider' => $dataProvider,
            'dataProviderCsv'=>$dataProviderCsv,
            'searchModel'=>$searchModel,
            'batch'=>$batch,
            
        ]); 
        
    }

    
    
    
    ///инфо по всем файлам
    
    public function actionSimpleUnisenderValidationAll(){
        
        
        
        //////
        
        $tasks = CronTask::find()->where(['status_id'=>1,'task_name'=>'UnisenderExportContacts'])->count();
        
        if ($tasks==0){
    //    if (1==1){
              $b = [
                    1=>160,
                    2=>161,    
                    3=>194,    
                    4=>196,    
                    5=>197,    
                    6=>200,
                    7=>201,
                    8=>204,
                    9=>205,
                    10=>210,
                    11=>211,
                    12=>214,
                    13=>215,
                    14=>218,
                    15=>219,
                    16=>224,
                    17=>230,
                    18=>231,
                    19=>234,
                    20=>235,
                    21=>228,
                    22=>221,
                    
                  ];
        
                foreach ($b as $batch_id){

                 $limit = 1000;


                    $db = Yii::$app->db;
                    $SQL = "select distinct list_id as list_id from westbtl.csv_batches
                                where batch_id = $batch_id";

                    $db = Yii::$app->db;
                    $steps =  $db->createCommand($SQL)->queryAll();

                 foreach ($steps as $step) {                       
                     if ($step['list_id']){
                  //       $list_id = 506;
                        $res =  CronTask::addTask('UnisenderExportContacts', $batch_id, $limit, $list_id);
                     }
                  // $a  =  AceptBatch::subscribeContactsUnknown($batch_id, $step['list_id'], $limit);
                  // export2($list_id,  $agency_id, $slice_count)
                  // $a =    \app\models\UnisenderExportContacts::export2($step['list_id'], $limit);


        //          echo $step['list_id'];

                 }

                }
        }
        
        ////// конец
        
        
        
        $this->layout = 'simple-load-layout';
       
        
        
          $SQL = "SELECT   
                    count(id) as c FROM westbtl.csv_batches
                    where list_id  is not null
                    group by  email_status, email_availability ";
               $db = Yii::$app->db;
              $count =  $db->createCommand($SQL)->queryAll();
              $count = count($count);

            $sql = " SELECT   
                               email_status,
                               email_availability,
                                       case    when email_status is null then 'данные не поступали'
                                               when email_status =     'new' then 'новый'
                                                when email_status = 'invited' then 'отправлено приглашение со ссылкой подтверждения подписки, ждём ответа, рассылка по такому адресу пока невозможна'
                                                when email_status = 'active' then 'активный адрес, возможна рассылка'
                                                when email_status = 'inactive' then 'адрес отключён через веб-интерфейс, никакие рассылки невозможны, но можно снова включить через веб-интерфейс'
                                                when email_status = 'unsubscribed' then 'адресат отписался от всех рассылок'
                                                when email_status = 'blocked' then 'адрес заблокирован администрацией нашего сервиса (например, по жалобе адресата), рассылка по нему невозможна. Разблокировка возможна только по просьбе самого адресата'
                                                when email_status = 'activation_requested' then 'запрошена активация адреса у администрации UniSender, рассылка пока невозможна'
                                                end as email_status,
                                case
                                when email_availability is null then 'данные не поступали'
                                when email_availability  = 'available' then 'адрес доступен'
                                when 	email_availability  = 'unreachable' then 'адрес недоступен'
                                when 	email_availability  = 'temp_unreachable' then 'адрес временно недоступен'
                                when 	email_availability  = 'mailbox_full' then 'почтовый ящик переполнен'
                                when 	email_availability  = 'spam_rejected' then 'письмо сочтено спамом сервером получателя. Через несколько дней этот статус будет снят.'
                                when 	email_availability  = 'spam_folder' then 'письмо помещено в спам самим получателем.'
                                                end as email_availability,
                                   count(id) as c FROM csv_batches
                                   where list_id  is not null
                                   and validate_status = 'unknown'
                                   group by  email_status, email_availability
                                   order by  email_availability, c";  
              
              
              
             $sql_new =  "select * from (
                        SELECT   
                                email_status,
                                                case    when email_status is null then 'Данные не поступали'
                                                                when email_status =     'new' then 'Новый'
                                                                when email_status = 'invited' then 'В ожидании ответа'
                                                                when email_status = 'active' then 'Доступно для рассылки'
                                                                when email_status = 'inactive' then 'Отписавшиеся пользователи'
                                                                when email_status = 'unsubscribed' then 'Отписавшиеся пользователи'
                                                                when email_status = 'blocked' then 'Адрес заблокирован администрацией UniSender'
                                                                when email_status = 'activation_requested' then 'Запрошена активация адреса у администрации UniSender, рассылка пока невозможна'
                                                        end as email_status1,
                                 
                                        count(id) as c FROM csv_batches
                                        where list_id  is not null
                                        and validate_status = 'unknown'
                                        group by  email_status
                                -- , email_availability
                                         order by  c) z

                        union 

                        select 'total', 'Всего разослано  писем' as email_status1,
                              count(id) as c1 FROM csv_batches 
                                        where list_id  is not null";
             $sql_new_av =  "select * from (
                        SELECT   
                                email_availability,
                                               
 case
                                when email_availability is null then 'данные не поступали'
                                when email_availability  = 'available' then 'адрес доступен'
                                when 	email_availability  = 'unreachable' then 'адрес недоступен'
                                when 	email_availability  = 'temp_unreachable' then 'адрес временно недоступен'
                                when 	email_availability  = 'mailbox_full' then 'почтовый ящик переполнен'
                                when 	email_availability  = 'spam_rejected' then 'Попали в спам.'
                                when 	email_availability  = 'spam_folder' then 'письмо помещено в спам самим получателем.'
                                                end as email_status1,


                                 
                                        count(id) as c FROM csv_batches
                                        where 
                                        -- list_id  is not null and
                                         validate_status = 'unknown'
                                        group by  email_availability
                                -- , email_availability
                                         order by  c) z

                        union 

                        select 'total', 'Всего разослано  писем' as email_status1,
                              count(id) as c1 FROM csv_batches 
                                        where 
                                       --  list_id  is not null and 
                                        validate_status = 'unknown'";
              
              
             
              
                $dataProvider = new SqlDataProvider([
                    'sql' => $sql_new_av,
                    
                 
              //      "params" => [':batch_id' => $batch_id],
                    'totalCount' => $count,

                    'pagination' => [
                        'pageSize' => 200,
                    ],
                ]);
                
                
                 $gridColumns = [
                       ['class' => 'kartik\grid\SerialColumn'],
//                          [
//                           'attribute'=>'email_availability', 
//                           'width'=>'250px',
//                            'label'=>'Доступность e-mail адреса',
////                                            'value'=>function ($model, $key, $index, $widget) { 
////                                                return $model->supplier->company_name;
////                                            },
////                                            'filterType'=>GridView::FILTER_SELECT2,
////                                            'filter'=>ArrayHelper::map(Suppliers::find()->orderBy('company_name')->asArray()->all(), 'id', 'company_name'), 
////                                            'filterWidgetOptions'=>[
////                                                'pluginOptions'=>['allowClear'=>true],
////                                            ],
////                                            'filterInputOptions'=>['placeholder'=>'Any supplier'],
//                           'group'=>true,  // enable grouping
//                       ],
                       [
                           'attribute'=>'email_status1', 
                           'width'=>'400px',
                           'label'=>'Статус e-mail адреса',

                       ],

                       [
                           'attribute'=>'c', 
                           'width'=>'150px',
                           'label'=>'Кол-во',

                       ],



                   ];
//                 $gridColumns = [
//                       ['class' => 'kartik\grid\SerialColumn'],
//                         
//                       [
//                           'attribute'=>'email_status1', 
//                           'width'=>'400px',
//                           'label'=>'Статус e-mail адреса',
//
//                       ],
//
//                       [
//                           'attribute'=>'c', 
//                           'width'=>'150px',
//                           'label'=>'Кол-во',
//
//                       ],
//
//
//
//                   ];
        
        
                
                
        return $this->render('simple-load/unisender-validator-all', [
             
            'dataProvider'=>$dataProvider,
            'gridColumns'=>$gridColumns
            
            
        ]); 
        
    }
    
    
    public function actionSimpleBatchTasks($batch_id=0){
      
        $this->layout = 'simple-load-layout';
        
         $CronModelActive = \app\models\CronTask::find()
                 ->where(['status_id'=>[1, -3, -4], 'batch_id'=>$batch_id])
                 ->orderBy(['batch_id'=>SORT_DESC, 'id'=>SORT_DESC ])->all();
                
       
          $SubscribeSpeedProvider = new SqlDataProvider([
                'sql' =>
                "SELECT id,
                        DATE_FORMAT(t.dt_create, '%d %M - %H:%i') as minutes,
                        count(t.id) quant
                              FROM westbtl.unisender_subscribe t
                        group by DATE_FORMAT(t.dt_create, '%d %M - %H:%i')
                    order by id desc",

                    'totalCount' => 15,
                    'pagination' => [
                        'pageSize' => 15,
                    ],
            ]);
         
          
          
            $BatchTaskStatusProvider = new SqlDataProvider([
                'sql' =>
                " SELECT 
                case   when status_id = 3 then 'Готов к подписке'
                           when status_id = 7 then 'Отправлен в Unisender'
                       when status_id = 8 then 'Получен из Unisender'
                       else 'остальные'
                end as status_name,
                    status_id, count(*) as quant FROM westbtl.csv_batches
                where batch_id = $batch_id
                group by status_id",

                    'totalCount' => 5,
                    'pagination' => [
                        'pageSize' => 5,
                    ],
            ]);
            
             
            $ValidateStatusProvider = new SqlDataProvider([
                'sql' =>
                " SELECT 
                        validate_status, 
                        email_availability,
                        case 
                         when email_availability is null and validate_status is null then 'Нет email'
                         when validate_status ='canceled' then  'отменен по просьюе БТЛ'
                         when validate_status = 'failed'  then  'не валидный email'

                         when   email_availability is null and validate_status ='passed'  then  'Письма отправлены'
                         when validate_status ='passed' then  'Валидный email'
                         when validate_status ='unknown' then  'На повторной валидации'
                         when validate_status ='error' then  'error'
                         when validate_status ='unprocessed' then  'unprocessed'

                        end as total_rus,

                               case 
                         when email_availability is null and validate_status is null then 'email_not_set'
                         when validate_status ='canceled' then  'btl_request_canceled'
                         when validate_status = 'failed' then  'email_not_valid'

                         when   email_availability is null and validate_status ='passed'  then  'all_sent'
                         when validate_status ='passed' then  'valid_email'
                         when validate_status ='unknown' then  'second_validation'
                         when validate_status ='error' then  'error'
                         when validate_status ='unprocessed' then  'unprocessed'

                        end as total,

                        count(*) as quant
                        FROM westbtl.csv_batches,  agency_csv_batch a
                  where 
                  
                       a.id = batch_id
                   and batch_id = $batch_id

                   group by validate_status, email_availability
                  order by 1, 2 desc




",

                    'totalCount' => 45,
                    'pagination' => [
                        'pageSize' => 45,
                    ],
            ]);
          
         
        
         return $this->render('simple-load/simple-batch-tasks', [
             
            
            'batch_id'=>$batch_id,
             'CronModelActive'=>$CronModelActive,
             'SubscribeSpeedProvider'=>$SubscribeSpeedProvider,
             'BatchTaskStatusProvider'=>$BatchTaskStatusProvider,
             'ValidateStatusProvider'=>$ValidateStatusProvider
            
            
        ]); 
        
        
    }
    
    
    public function actionSimpleStat($campaign_id=35){
        
        $this->layout = 'simple-load-layout';
        
        $SQL = "SELECT  validate_status, 
                    email_availability,
                    case 
                     when email_availability is null and validate_status is null then 'Нет email'
                     when validate_status ='canceled' then  'отменен по просьюе БТЛ'
                     when validate_status = 'failed'  then  'не валидный email'

                     when   email_availability is null and validate_status ='passed'  then  'Письма отправлены'
                     when validate_status ='passed' then  'Валидный email'
                     when validate_status ='unknown' then  'На повторной валидации'
                     when validate_status ='error' then  'error'
                     when validate_status ='unprocessed' then  'unprocessed'

                    end as total_rus,

                           case 
                     when email_availability is null and validate_status is null then 'email_not_set'
                     when validate_status ='canceled' then  'btl_request_canceled'
                     when validate_status = 'failed' then  'email_not_valid'

                     when   email_availability is null and validate_status ='passed'  then  'all_sent'
                     when validate_status ='passed' then  'valid_emil'
                     when validate_status ='unknown' then  'second_validation'
                     when validate_status ='error' then  'error'
                     when validate_status ='unprocessed' then  'unprocessed'

                    end as total,

                    count(*) as q
                    FROM westbtl.csv_batches,  agency_csv_batch a
              where 
              
              
                a.campaign_id = $campaign_id
              and a.id = batch_id
               and batch_id >= 159

               group by validate_status, email_availability
              order by 1, 2 desc";
        
        
          
                    $db = Yii::$app->db;
                    


                    $stat =  $db->createCommand($SQL)->queryAll();
          
          
       return $this->render('simple-load/total_stat', [
           'stat'=>$stat,
           'campaign_id'=>$campaign_id    
            
        ]); 
          
    }
    public function actionSimpleStat2($campaign_id){
        
        $this->layout = 'simple-load-layout';
        
        $SQL = "select z.activity_loc, sum(z.passed) as 'passed',  sum(z.failed) as 'failed',
sum(z.no_email) as 'no_email'
	
 from
                (select  u.activity_loc, 
                IF(u.validate_status = 'passed',1,0)	as 'passed', 
                IF(u.validate_status = 'failed',1,0)	as 'failed',
                IF(u.validate_status = 'no_email',1,0)	as 'no_email'

                 from csv_batches u, agency_csv_batch a 
                where 
                 u.batch_id = a.id and
                 
                 a.campaign_id = $campaign_id and 
                 u.activity_loc != 'activity_loc'
                 ) z
                 group by  z.activity_loc
                 order by 1

                ";
        
        
          
                    $db = Yii::$app->db;
                    


                    $stat =  $db->createCommand($SQL)->queryAll();
          
                    
             $StatProvider = new SqlDataProvider([
                'sql' =>
                " select z.activity_loc, sum(z.passed) as 'passed',  sum(z.failed) as 'failed',
sum(z.no_email) as 'no_email'
	
 from
                (select  u.activity_loc, 
                IF(u.validate_status = 'passed',1,0)	as 'passed', 
                IF(u.validate_status = 'failed',1,0)	as 'failed',
                IF(u.validate_status = 'no_email',1,0)	as 'no_email'

                 from csv_batches u, agency_csv_batch a 
                where 
                 u.batch_id = a.id and
                 a.campaign_id = $campaign_id and 
                 u.activity_loc != 'activity_loc'
                 ) z
                 group by  z.activity_loc
                 order by 1




",

                    'totalCount' => 100,
                    'pagination' => [
                        'pageSize' => 100,
                    ],
            ]);
                    
          
       return $this->render('simple-load/total_stat_2', [
           'StatProvider'=>$StatProvider,
           'campaign_id'=>$campaign_id    
            
        ]); 
          
    }
    
    
    
    public function actionSimpleCrmTasks($batch_id = 0, $mode = ''){
      
        $this->layout = 'simple-load-layout';
        
         $searchModel = new CrmUsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        
         $csv_upload = new \app\models\CsvUpload();
       
        
        
        
        
        if ($mode=='start'){
                
           $res =  \app\models\CrmUsers::addCrmTasksChain($batch_id);
            
//            $count =  \app\models\CrmUsers::find()->where(['batch_id'=>$batch_id])->count();
//            if ($count ==0){
//                $data_insert = \app\models\CrmUsers::insertUsers2($batch_id);
//            }
//            
//             //$res =  CronTask::addTask('UnisenderExportContacts', $batch_id, $limit, $list_id);
//
//             //CronTask::addTask($task_name, $batch_id, $step_limit, $list_id, $validate_statuses, $list_count, $status_id, $row_count)
//           $res =  CronTask::addTask('checkEmailAvability', 
//                              $batch_id,
//                                10, 
//                                0, 
//                                0, 
//                                0, 
//                                1, $count);
//           $res =  CronTask::addTask('RegisterBasic', 
//                              $batch_id, 
//                                10, 
//                                0, 
//                                0, 
//                                0, 
//                                -4, $count);
//           $res =  CronTask::addTask('UpdateProfile', 
//                              $batch_id, 
//                                10, 
//                                0, 
//                                0, 
//                                0, 
//                                -4, $count);
//           $res =  CronTask::addTask('FillSmokingHabbits', 
//                              $batch_id, 
//                                10, 
//                                0, 
//                                0, 
//                                0, 
//                                -4, $count);
              $this->redirect(['simple-crm-tasks','CrmUsersSearch[batch_id]'=>$batch_id ]);
            
        }
        
        if ($mode == 'massive'){
            
             $count =  CrmUsers::find()->where(['batch_id'=>$batch_id])->count();
            
            
            $del = CrmUsers::deleteAll(['batch_id'=>$batch_id]);
            
            $data_insert = CrmUsers::insertUsers2($batch_id);
             
             
             
             $res =  CronTask::addTask('RegisterBasicMassive', 
                                      $batch_id, 
                                        1000, 
                                        0, 
                                        0, 
                                        0, 
                                        1, $count);
              Yii::$app->session->setFlash('warning', " Массовая загрузка запущена" );
               $this->redirect(['simple-crm-tasks','CrmUsersSearch[batch_id]'=>$batch_id ]);
        }
        
        
          $batch_id = $searchModel->batch_id;
                   
        
            return $this->render('simple-load/simple-crm-tasks', [
               'searchModel' => $searchModel,
               'dataProvider' => $dataProvider,
               'csv_upload'=>$csv_upload,
                'batch_id'=>$batch_id
               
                
               
                
            ]);
        
    }
    
    
    
    
    
    
    
    
}
