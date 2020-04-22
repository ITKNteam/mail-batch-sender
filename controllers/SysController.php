<?php

namespace app\controllers;

use Yii;

use app\models\Handbook;
use app\models\HandbookName;



use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class SysController extends \yii\web\Controller
{
    
    public $layout = 'sb-admin';
    public function actionIndex()
    {
        return $this->render('index');
    }
    
     public function actionHandbooks($id=0)
    {
        
       
        

        $HndbNameProvider = new ActiveDataProvider([
            'query' => HandbookName::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        
        $HndbProvider = new ActiveDataProvider([
            'query' => Handbook::find()->where(['hand_name_id'=>$id]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        $handbook_name_id = $id;
        
        
        return $this->render('handbooks/index',
        ['HndbNameProvider'=>$HndbNameProvider,
         'HndbProvider'=>$HndbProvider ,
            'handbook_name_id'=>$handbook_name_id
            
        ]);
    }
    
     public function actionCreate($m)
    {
        $model = null;
        $view = '';
        
        
       
        

        if ($model->load(Yii::$app->request->post())  && $model->validate()) {
            $model->save();
            return $this->redirect(['sys/'.$view, 'id' => $model->id]);
        } else {
            return $this->render($view.'/create', [
                'model' => $model,
            ]);
        }
    }
    public function actionCreateHandbook($handbook_name_id)
    {
       
        $view = 'handbooks';
        
        
       
           
           $model = new \app\models\Handbook();
           
           
       
       
        

        if ($model->load(Yii::$app->request->post())  && $model->validate()) {
            $model->hand_name_id = $handbook_name_id;
            
            $model->save();
            return $this->redirect(['sys/'.$view, 'id' => $model->id]);
        } else {
            return $this->render($view.'/create', [
                'model' => $model,
            ]);
        }
    }
    
    

    /**
     * Updates an existing Post model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $m)
    {     
         $model = null;
        $view = '';
      
        if ($m == 'conf'){
           $view = 'conf'; 
           $modelName = new \app\models\SysConfig();
           
        }
        if ($m == 'handbook'){
           $view = 'handbooks'; 
           $modelName = new Handbook();
           
        }
        $model = $this->findModel($modelName,$id);
        
        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
            return $this->redirect(['sys/'.$view]);
        } else {
            return $this->render($view.'/update', [
                'model' => $model,
            ]);
        }
    }
    
    
    
    
     public function actionDelete($id, $m)
    {
        $model = null;
        $view = '';
        
        if ($m == 'conf'){
           $view = 'conf'; 
           $modelName = new \app\models\SysConfig();
           
        }
        $this->findModel($modelName,$id)->delete();

        return $this->redirect(['sys/'.$view]);
    }
    
    
   

    /**
     * Finds the Post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($modelName, $id)
    {
        if (($model = $modelName::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    
    
    public function actionAllCronTasks(){
        
        $this->layout = 'simple-load-layout';
        
        $CronModelActive = \app\models\CronTask::find()->where(['status_id'=>[1, -3, -4]])
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
        
            return $this->render('all-tasks', [
                'SubscribeSpeedProvider' => $SubscribeSpeedProvider,
                'CronModelActive'=>$CronModelActive
            ]);
            
    }
    
    public function actionChangeTaskStatus($task_id, $status_id, $redirect_url = '/sys/all-cron-tasks', $batch_id=0){
      
        
        $task = \app\models\CronTask::findOne(['id'=>$task_id]);
        
         if($status_id == 1 && in_array($task['task_name'], [ 'SubscribeContacts',
                                                            'SubscribeContacts2',
                                                             'SubscribeByStatus',
                                                             'SendContactsByStatus',
                                                             'SendContactsRepeat',
                                                             'subscribeContactsUnknown',
                                                              'UnisenderExportContacts'  ]) )
                {
             
                   
              if ( \app\models\CronTask::checkActiveTask()){   
                    $status_id = -4;
               } 
            } 
         if($status_id == 1 && in_array($task['task_name'], [ 'ValidateEmail']) )
                {
             
                   
              if ( \app\models\CronTask::checkActiveTask('BulkValidator')){   
                    $status_id = -4;
               } 
            } 
             
             
        
        $task->status_id = $status_id;
        $task->update(FALSE);
        
         Yii::$app->session->setFlash('warning', " Статус успешно изменен!" );
        
        return $this->redirect([$redirect_url, 'batch_id'=>$batch_id ]);
        
    }
    
    
    public function actionStartTask($batch_id =  187, $mode=0, $redirect_url = '/sys/all-cron-tasks'){
        
        
        
        $this->layout = 'simple-load-layout';
        if ($mode==1){
            
            $agency_id = 2; 
            $list_title = $batch_id.' - '.' ('.crypt($batch_id.'rasmuslerdorf'.rand(0, 9999). date('s')).')';
           // $batch_id =  187;
            $validate_status = 'all';
            $ret =   \app\models\UnisenderList::newListWithConfirmEmail($list_title, $agency_id, $batch_id, $validate_status);
    
            
            
             $db = Yii::$app->db;
             $SQL = "SELECT * FROM westbtl.unisender_list
                        where batch_id = $batch_id
                        order by 1 desc
                        limit 1";

             $db = Yii::$app->db;
             $step =  $db->createCommand($SQL)->queryOne();
             
             $list_id = $step['id'];
            
            $db = Yii::$app->db;
                        

            $SQL = "update westbtl.csv_batches
                       set list_id = $list_id
                           where batch_id = $batch_id";

               $db = Yii::$app->db;
               $steps =  $db->createCommand($SQL)->execute();
            
            Yii::$app->session->setFlash('warning', "Список для подписки создал. <b>$steps</b> записей гтовы к загрузке в Unisender " );
            $this->redirect([$redirect_url,'batch_id'=>$batch_id, 'CsvBatchesSearch[batch_id]'=>$batch_id ]);
        }
        if ($mode==2){
            
             $db = Yii::$app->db;
             $SQL = "SELECT * FROM westbtl.unisender_list
                        where batch_id = $batch_id
                        order by 1 desc
                        limit 1";

             $db = Yii::$app->db;
             $step =  $db->createCommand($SQL)->queryOne();
                    $limit =60;
                     $res =  \app\models\CronTask::addTask('subscribeContactsUnknown', $batch_id, $limit, $step['id']);
            
              $ret =  \app\models\BatchSteps::saveStep($batch_id, 'Запуск подписки контактов ', "Задачи по подписке, поставлена в очередь. Данные обновляются каждые 2 минуты.", 8, 1);
            
             Yii::$app->session->setFlash('warning', "Задачи по подписке, поставлена в очередь. Данные обновляются каждую минуту." );
             $this->redirect([$redirect_url,'batch_id'=>$batch_id, 'CsvBatchesSearch[batch_id]'=>$batch_id ]);
        }
        if ($mode==3){
            $db = Yii::$app->db;
             $SQL = "SELECT * FROM westbtl.unisender_list
                        where batch_id = $batch_id
                        order by 1 desc
                        limit 1";

             $db = Yii::$app->db;
             $step =  $db->createCommand($SQL)->queryOne();
             
             $limit = 1000;
            
             $res =  \app\models\CronTask::addTask('UnisenderExportContacts', $batch_id, $limit, $step['id']);
                                    
            
            $ret =  \app\models\BatchSteps::saveStep($batch_id, 'Запуск экспорта  ', "Задачи по выгрузке контактов, поставлена в очередь. Данные обновляются каждыую минуту.", 9, 1);
             Yii::$app->session->setFlash('warning', "Задачи по выгрузке контактов, поставлена в очередь. Данные обновляются каждыую минуту." );
             $this->redirect([$redirect_url,'batch_id'=>$batch_id, 'CsvBatchesSearch[batch_id]'=>$batch_id ]);
        }
        
        if ($mode==4){
            
            
            
              $db = Yii::$app->db;
                        

                            $SQL = "update westbtl.csv_batches
                                       set status_id = 3
                                           where email not like '%\N' and
                                            validate_status not in ('passed','failed') 
                                            and  batch_id = $batch_id";

                               $db = Yii::$app->db;
                               $steps =  $db->createCommand($SQL)->execute();

                        
            
             Yii::$app->session->setFlash('warning', " Обновленно  <b>$steps строк.</b>" );
             
            
             
             $ret =  \app\models\BatchSteps::saveStep($batch_id, 'Подготовка', "Пачка подготовлена", 10, 1);
             
            $this->redirect([$redirect_url,'batch_id'=>$batch_id ]);
        }
        
        
//            $task = new CronTask();
//            $task->task_name = $task_name;
//            $task->batch_id = $batch_id;
//            $task->batch_rows_count = $batch_rows_count;
//            $task->step_limit = $step_limit;
//            $task->steps_count = $step_count;
//            $task->current_step = 1;
//            $task->status_id = 1;
//            $task->list_id = $list_id;
//            $task->last_dt = date('Y-m-d H:i:s');
//            $task->validate_statuses = $validate_statuses;
//
//            $task->save();
    }

}
