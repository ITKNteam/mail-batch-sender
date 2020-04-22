<?php

namespace app\models;

use Yii;

use yii\db\ActiveRecord;
use \yii\base\ErrorException;
use yii\web\IdentityInterface;
use yii\helpers\Url;

use app\models\searchModels\AdmUsersSearch;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

use yii\grid\GridView;




use mdm\admin\models\searchs\Assignment as AssignmentSearch;

//class User extends \yii\base\Object implements \yii\web\IdentityInterface
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
   
    public $profile;
    public $_username;
    public $token;
    public $cityes;
    
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }
    
//ALTER TABLE `nestle-tnt`.`users` 
//ADD COLUMN `wrong_atempt` INT default 0 AFTER `points_coefficent`,
//ADD COLUMN `locked_by_time` TIMESTAMP NULL AFTER `wrong_atempt`;

    
     /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password'], 'string'],
          //  ['password', 'match', 'pattern' => '/^(?=.*\d)(?=.*[@#\-_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=§!\?]{8,20}$/'],

            
            ['email', 'email'],
            [['email'], 'trim'],
            
            
            [['status_id', ], 'integer'],
            [['reg_date','cityes' ], 'safe'],
            
            [['username',  'authKey', 
              'accessToken', 'email', ], 'string', 'max' => 450],
            [['role'], 'string', 'max' => 45],
            [['email'], 'checkEmail'],
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Логин',
            'password' => 'Пароль',
            'authKey' => 'Auth Key',
            'accessToken' => 'Access Token',
            'status_id' => 'Статус',
            'reg_date' => 'Дата регистарции',
            'email' => 'E-MAIL',
            'role' => 'роль',
         
        

            
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsersTasks()
    {
        return $this->hasMany(UsersTask::className(), ['user_id' => 'id']);
    }
    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }
    
        /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        
        
       // if ($this->validate() && RegistrationToken::setRegistration($this->token)) {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->email;
            $user->accessToken = $this->token;
            $user->email = $this->email;
            $user->status_id = 0;
            $user->reg_date = date("Y-m-d H:i:s");
            $user->role = 'user';
           $pass = self::generatePassword();
            
            $user->setPassword($pass);
          
            if ($user->save(false)) {
                
                $body = '<h3>Вас зарегистрировали в системе West BTL </h3>'
                        . '<br><b>Логин:</b> '.$this->email.''
                        . '<br><b>Пароль:</b> '.$pass.''
                        . '<br><br><b>Ссылка в личный кабинет</b>: '.Url::base(1).'/users/lk';
              
                 $mailer = Yii::$app->get('mail');
                 $message = $mailer->compose()
                 ->setFrom('support@simpsons.ru')
                 ->setTo($this->email)
                 ->setHtmlBody($body)       
                 ->setSubject('Email sent from West BTL')
                 ->send();
                
                return true;
            }  
        }
        
        return null;
    }
    
    
    
    
    public function PasswordRecovery(){
         
         $user = static::find()->where(['username' => $this->email])->one();
         if($user){
            $password = self::generatePassword();

            $user->setPassword($password);

            if ($user->update(false)) {

                
                
                 $body = '<h3>Новые данные в системе West BTL </h3>'
                        . '<br><b>Логин:</b> '.$this->email.''
                        . '<br><b>Пароль:</b> '.$password.''
                        . '<br><br><b>Ссылка в личный кабинет</b>: '.Url::base(1).'/users/lk';
               
//                $user_name = $user->first_name.' '.$user->last_name;
//                
//                $mailer = Yii::$app->get('mail');
//                $message = $mailer->compose('reset_pass', [
//                    'user_name' => $user_name,
//                    'pass' => $password,
//                    
//                ])
//                ->setFrom('nftnt@mail.ru')
//                ->setTo($user->email)
//                ->setSubject('Сброс пароля')
//                ->send();

                    $mailer = Yii::$app->get('mail');
                    $message = $mailer->compose()
                    ->setFrom('support@simpsons.ru')
                    ->setTo($this->email)
                    ->setHtmlBody($body)       
                    ->setSubject('Email sent from West BTL')
                    ->send();

                return true;
            }
         }
        return false; 
        
    }
    
    

    public static function generatePassword(){
            $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
            $pass = array(); //remember to declare $pass as an array
            $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
            for ($i = 0; $i < 8; $i++) {
                $n = rand(0, $alphaLength);
                $pass[] = $alphabet[$n];
            }
            return implode($pass); //turn the array into a string
    }


   

    

    public static function getUserInfo(){
        
        return static::findOne(['id' => Yii::$app->user->id]);
        
    }
    
     public static function getSex(){
        return ['2'=>'ЖЕНСКИЙ','1'=>'МУЖСКОЙ'];
    }
    
     public static function userSexName($sex) {
        $sex_ar = self::getSex(); 
         if ($sex) 
           return $sex_ar[$sex];
         
        return 'Нет данных'; 
     }
//    
    

    


    public static function getCurentUserRole(){
        $roleName = 'guest';
        
        if (!Yii::$app->user->isGuest){
            $user = static::getUserInfo();
            $roleName = $user->role;
        }
        return $roleName;
    }
    
    

    

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        
         if (Yii::$app->getSession()->has('user-'.$id)) {
            return new self(Yii::$app->getSession()->get('user-'.$id));
        }
        else {
         return static::findOne(['id' => $id]);
        }
         
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
          throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
        return null;
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {

          return static::findOne(['username' => $username]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
         return $this->getPrimaryKey();
       // return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }
   
    
            

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
         return $this->getAuthKey() === $authKey;
       // return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
       // return $this->password === $password;
        
       return Yii::$app->security->validatePassword($password, $this->password);
    }
    
   
   
   
    public function checkEmail($attribute, $params)
    {
        
        if (!$this->hasErrors()) {
           
            if ( !User::validateEmail($this->email)) {
                
                $this->addError($attribute, 'Не удалось зарегистрировать. Этот email уже зарегистрирвоан. '.Url::base(1).'/users/password-recovery');
            }
        }
        
    }
    
   
    
     /**
     * Validates email
     *
     * @param  string  $email email to validate
     * @return boolean if email not found is valid for current user
     */
    public static function validateEmail($email)
    {
       $res = static::find()->where(['email' => $email])->count();
       return $res ? false : true;
    }
     /**
     * Validates lock
     *
     * @return boolean if return not found is valid for current user
     */
    public static function isLockedUser($username)
    {
        $res = User::find()->where('status_id = 0 '
                                    . 'and username = :username', 
                                    [':username'=>$username])->count();
        return $res ?  true: false;
    }
    
   
    
    public static function changeRole($user_id, $role){
        $user = static::find()->where(['id'=>$user_id])->one();
        $user->role = $role;
        $user->update(false);
        return 1;
        
    }
    
    public static function changeStatus($user_id, $status_id){
        $user = static::find()->where(['id'=>$user_id])->one();
        $user->status_id = $status_id;
        $user->update(false);
        return 1;
        
    }

    /**
     * @param \nodge\eauth\ServiceBase $service
     * @return User
     * @throws ErrorException
     */
    public static function findByEAuth($service) {
        if (!$service->getIsAuthenticated()) {
            throw new ErrorException('EAuth user should be authenticated before creating identity.');
        }

        $id = $service->getServiceName().'-'.$service->getId();
        $attributes = array(
            'id' => $id,
            'username' => $service->getAttribute('name'),
            'authKey' => md5($id),
            'profile' => $service->getAttributes(),
           
        );
        $attributes['profile']['service'] = $service->getServiceName();
        print_r($attributes);
        Yii::$app->getSession()->set('user-'.$id, $attributes);
        return new self($attributes);
    }
    
/**
     * @inheritdoc
     * @return UsersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UsersQuery(get_called_class());
    }
    
    
    
    public static function getUserRating(){
        $sql_set = 'SET group_concat_max_len = 20480;';
        $sql = 'SELECT 
                        id,
                        username,
                        likes_count,
                        FIND_IN_SET(id,
                                (SELECT 
                                        GROUP_CONCAT(id
                                                ORDER BY likes_count DESC, id)
                                    FROM
                                        users  
                                        where  user_pic is not null
                                            and sex = 0
                                            and points_balance <> 0
                                            and statusId = 1  
                                            ORDER BY likes_count DESC, id
                                        )) AS rank
                    FROM
                        users 
                    where id = :user_id -- id пользователя
                    
                        order by rank';


             
            //$c = static::find()->count();            
            $inf = [
                 'rank'=>0,
                 'likes_count'=>0,
             ];
        if (!Yii::$app->user->isGuest){
            

            $mysql_setting = Yii::$app->db->createCommand($sql_set)->query();
             $rating = Yii::$app->db->createCommand($sql)
                     ->bindValue(':user_id', Yii::$app->user->getId())
                    ->queryAll();
             
             
             
             
             $inf = [
                 'rank'=>$rating[0]['rank'],
                 'likes_count'=>$rating[0]['likes_count'],
             ];
            
            
            
        }
        return $inf;
        
    }
    
    public static function lastUserPics(){
        
         $searchModel = new AdmUsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
       return  GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            
            
              [
                 'label'=>'Профиль',
                 'format' => 'raw',
                 'value'=>function ($data) {return Html::a(
                                                Html::img( '/data/uploads/userpics/'.$data->id.'/100x100_'.$data->user_pic).'<br>'.   
                                                $data->first_name.' '.$data->first_name, '/UserManager/useradmin/profile?uid='.$data->id,
                                                ['title' => Yii::t('yii', 'Провиль'), 'data-pjax' => '0']);
                            }
                 
             ],
                    
            
                    
               
            [
                 'label'=>'Блокировка',
                 'format' => 'raw',
                 'value'=>function ($data) { $status = $data->statusId ? 0:1;
                                            return Html::a( $data->statusId ? 'Блокировать' : 'Разблокировать', '/UserManager/useradmin/block?uid='.$data->id.'&status_id='.$status,
                                                ['title' => Yii::t('yii', 'Блокировка'), 'data-pjax' => '0']);
                            }
             ],        
          
            
                     
                     
                     
            
        ],
    ]); 
    }
    
    public static function getRoleName(){
        $roleName = 'guest';
        
        if (!Yii::$app->user->isGuest){
            $inf =   Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
            $inf2 = array_keys( $inf);
            $roleName =  $inf2['0']; 
        }
        return $roleName;
    }
    
  }

