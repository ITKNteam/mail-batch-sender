<?php

namespace app\components;


class ShugarCrmConnector extends Cconnector {
    
    const Authorization = 'Basic V2VzdENybTpzQT';
 //   const WSDL = 'http://dev.shavrak.ru/sugar/pinchesky/deploy/custom/service/v4_1_custom/soap.php?wsdl';


/*
    private $_options = array(
        'trace' => 0,
        'exceptions'=>0,
        "location" => 'http://dev.shavrak.ru/sugar/pinchesky/deploy/custom/service/v4_1_custom/soap.php?wsdl',
        "uri" => 'http://dev.shavrak.ru/sugar/pinchesky/'
    );

*/

//    private static function getOptions(){
//        return array(
//        'trace' => 0,
//        'exceptions'=>0,
//        "location" => 'http://westcrm.dev.shavrak.ru/custom/service/v4_1_custom/soap.php?wsdl',
//        "uri" => 'http://westcrm.dev.shavrak.ru/'
//    );
//    }
//    
//    
//    
//    private $_options = array(
//        'trace' => 0,
//        'exceptions'=>0,
//        "location" => 'http://westcrm.dev.shavrak.ru/custom/service/v4_1_custom/soap.php?wsdl',
//        "uri" => 'http://westcrm.dev.shavrak.ru/'
//    );
//    
//    
    private static function getOptions(){
        return array(
        'trace' => 0,
        'exceptions'=>0,
        "location" => 'http://westcrm.pro.shavrak.ru/custom/service/v4_1_custom/soap.php?wsdl',
        "uri" => 'http://westcrm.pro.shavrak.ru/'
    );
    }


    private $_options = array(
        'trace' => 0,
        'exceptions'=>0,
        "location" => 'http://westcrm.pro.shavrak.ru/custom/service/v4_1_custom/soap.php?wsdl',
        "uri" => 'http://westcrm.pro.shavrak.ru/'
    );

    
    

     /*  Shugar сервис
     *   
     *  AddAchievement
     *  Метод добавления достижения пользователю.
     * 
     *  Возможные ошибки бизнес-логики
     *      ExternalIdNotUnique    - Поле externalId содержит номер, который уже был отправлен ранее
     *      UserNotFound           - Пользователь с указанным GUID не найден
     *
     * @param String      $code 	 
     * @param Datetime    $creationDate	
     * @param String      $externalId	
     * @param int         $level	
     * @param String      $name	
     * @param String      $userKey	
     * @param int         $value	
     *  
     * @return object
     * 
     ***************************************************/
    
    public  function AddAchievement($code, $creationDate, $externalId, $level, $name, $userKey, $value, $app= 'WESTONLINE' ){
        try{
             $client = new SoapClient(null, $this->_options);
                          
             $header_part = '<app xmlns="mmg">'.$app.'</app>';
             $header_p = new \SoapVar( $header_part, XSD_ANYXML, null, null, null  );
             $header = new \SoapHeader($app, 'app', $header_p );
             $client->__setSoapHeaders($header);
             $response = $client->AddAchievement($code, $creationDate, $externalId, $level, $name, $userKey, $value );
        }
        catch (SoapFault $fault)
        {
            echo "Error instantiating SOAP object!\n";
            echo $fault->getMessage() . "\n";
        }
         return $response;
        
        
    }
    
    
     /*  Shugar сервис
     *   
     *  AuthenticateUser
     *  Аутентификация пользователя по логину и паролю.
     * 
     *  Возможные ошибки бизнес-логики
     *      InvalidCredentials - Аутентификация неуспешна
     *
     * @param String      $login 	 
     * @param String      $password	
     *  
     * @return object
     * 
     ***************************************************/
    
     public function AuthenticateUser( $login, $password, $app= 'WESTONLINE'){
         try{
             $client = new \SoapClient(null, $this->_options);
             $header_part = '<app xmlns="mmg">'.$app.'</app>';
             $header_p = new \SoapVar( $header_part, XSD_ANYXML, null, null, null  );
             $header = new \SoapHeader($app, 'app', $header_p );
             $client->__setSoapHeaders($header);
             $response = $client->AuthenticateUser($login, $password );
        }
        catch (SoapFault $fault)
        {
            echo "Error instantiating SOAP object!\n";
            echo $fault->getMessage() . "\n";
        }
         return $response;
     }
     
    
     /*  Shugar сервис
     *   
     *  CheckEmailAvailability
     *  Проверка адреса э-почты на доступность для регистрации.
     * 
     *  Возможные ошибки бизнес-логики
     *      EmailNotUnique	Адрес уже занят
     *
     * @param String      $address 	 
     * @param String      $alias	
     *  
     * @return object
     * 
     ***************************************************/
    
     public function CheckEmailAvailability($address, $alias, $app= 'WESTONLINE'){
         try{
             
             $client = new \SoapClient(null, $this->_options);
//             $header_part = '<app xmlns="mmg">'.$app.'</app>';
//             $header_p = new \SoapVar( $header_part, XSD_ANYXML, null, null, null  );
//             $header = new \SoapHeader($app, 'app', $header_p );
           //  $client->__setSoapHeaders($header);
             $response = $client->CheckEmailAvailability($address, $alias, $app );
        }
        catch (SoapFault $fault)
        {
            echo "Error instantiating SOAP object!\n";
            echo $fault->getMessage() . "\n";
        }
         return $response;
     }
     
     
     
      /*  Shugar сервис
     *   
     *  AwardPoints
     *  Начисление баллов без получения достижения.
     * 
     *  Возможные ошибки бизнес-логики
     *           InvalidCommentLength - Длина поля «комментарий» слишком длинное
     *           ExternalIdNotUnique - Поле externalId содержит номер, который уже был отправлен ранее
     *           UserNotFound -  Пользователь с указанным GUID не найден
     *
     * @param Datetime    $awardDate	
     * @param String      $comment	
     * @param String      $externalId	
     * @param String      $name	
     * @param String      $userKey	
     * @param int         $value	
     *  
     * @return object
     * 
     ***************************************************/
    
    public  function AwardPoints($awardDate, $comment, $externalId, $name, $userKey, $value, $app= 'WESTONLINE' ){
          try{
             $client = new \SoapClient(null, $this->_options);
             $header_part = '<app xmlns="mmg">'.$app.'</app>';
             $header_p = new \SoapVar( $header_part, XSD_ANYXML, null, null, null  );
             $header = new \SoapHeader($app, 'app', $header_p );
             $client->__setSoapHeaders($header);
             $response = $client->AwardPoints($awardDate, $comment, $externalId, $name, $userKey, $value );
        }
        catch (SoapFault $fault)
        {
            echo "Error instantiating SOAP object!\n";
            echo $fault->getMessage() . "\n";
        }
         return $response;
        
    }
    
    
      /*  Shugar сервис
     *   
     *  LogEvents
     *  Запись пользовательских событий.
     * 
     *  Возможные ошибки бизнес-логики
     *           ExternalIdNotUnique - Поле externalId содержит номер, который уже был отправлен ранее

     *
     
     * @param String      $comment	
     * @param String      $data	
     * @param Datetime    $eventDate	
     * @param String      $eventType	
     * @param String      $externalId	
     * @param String      $httpCookie	
     * @param String      $httpReferrer	
     * @param String      $userKey	
     *  
     * @return object
     * 
     ***************************************************/
    
    public  function LogEvents($comment, $data, $eventDate, $eventType, $externalId, $httpCookie, $httpReferrer, $userKey, $app= 'WESTONLINE' ){
            try{
             $client = new \SoapClient(null, $this->_options);
             $header_part = '<app xmlns="mmg">'.$app.'</app>';
//             $header_p = new \SoapVar( $header_part, XSD_ANYXML, null, null, null  );
//             $header = new \SoapHeader($app, 'app', $header_p );
//             $client->__setSoapHeaders($header);
             $response = $client->LogEvents($comment, $data, $eventDate, $eventType, $externalId, $httpCookie, $httpReferrer, $userKey, $app );
        }
        catch (SoapFault $fault)
        {
            echo "Error instantiating SOAP object!\n";
            echo $fault->getMessage() . "\n";
        }
         return $response;
        
    }
    
    
     /*  Shugar сервис
     *   
     *  RegisterBasic
     *  Запись пользовательских событий.
     * 
     *  Возможные ошибки бизнес-логики
     *        ExternalIdNotUnique - Поле externalId содержит номер, который уже был отправлен ранее
     *        AgeLimit - Возраст регистрируемого пользователя меньше 18 лет
     *
     * 
     * 
     * @param Datetime    $birthDate	
     * @param String      $address	
     * @param String      $alias	
     * @param String      $externalId	
     * @param String      $httpCookie	
     * @param String      $httpReferrer	
     * @param String      $invitationLinkKey	
     * @param String      $familyName	
     * @param String      $givenName	
     * @param String      $patronymic	
     * @param String      $utmCampaign	
     * @param String      $utmContent	
     * @param String      $utmMedium	
     * @param String      $utmSource	
     * @param String      $utmTerm	
     * @param String      $password	
     *  
     * @return object
     * 
     ***************************************************/
    
    public static function RegisterBasic($birthDate, $address, $alias, $externalId, $httpCookie, $httpReferrer, $invitationLinkKey,
                                   $familyName, $givenName, $patronymic, $utmCampaign, $utmContent, $utmMedium, $utmSource, 
                                    $utmTerm, $password, $app='WESTONLINE' ){
         
        
    try{
           //$client = new \SoapClient(null, $this->_options);
           $client = new \SoapClient(null, self::getOptions());
        
           
            $header_part = '<app xmlns="mmg">'.$app.'</app>';
//            $header_p = new \SoapVar( $header_part, XSD_ANYXML, null, null, null  );
//            $header = new \SoapHeader($app, 'app', $header_p );
//            $client->__setSoapHeaders($header);
         
            $response = $client->RegisterBasic($birthDate, $address, $alias, $externalId, $httpCookie, $httpReferrer, $invitationLinkKey,
                                   $familyName, $givenName, $patronymic, $utmCampaign, $utmContent, $utmMedium, $utmSource, $utmTerm, $password, $app);
          
        } catch (SoapFault $fault)
        {
             $ex = new \SoapFault($fault, "msg");
           // echo "Error instantiating SOAP object!\n";
          //  echo $fault->getMessage() . "\n";
            return [ 'failedCauseOf' => 'None',
                    'isSuccessfully' => 0,
                    'details' =>  'Error instantiating SOAP object!',
                    'entity' => $ex->faultstring];
                   // 'entity' => $fault->getMessage()];
        }
         
         return $response;
        
    }
 
    
    /*  Shugar сервис
     *   
     *  UpdateProfile
     *  Обновление профиля.
     * 
     *  Возможные ошибки бизнес-логики
     *       MobilePhoneNumberWrongFormat	Неверный формат номера телефона
     * 
     ***************************************************/
    
    public function UpdateProfile($userKey, $birthDate, $address, $alias, $gender, $fullNumber, $familyName, $givenName, $patronymic,
                                  $notificationAllow,  $smoker, $password, $app='PS' ){
        try{
           $client = new \SoapClient(null, self::getOptions());
        
            $header_part = '<app xmlns="mmg">'.$app.'</app>';
            $header_p = new \SoapVar( $header_part, XSD_ANYXML, null, null, null  );
            $header = new \SoapHeader($app, 'app', $header_p );
            $client->__setSoapHeaders($header);
         
            $response = $client->UpdateProfile($userKey,    $birthDate,          $address,    $alias, 
                                               $gender,     $fullNumber,         $familyName, $givenName, 
                                               $patronymic, $notificationAllow,  $smoker,     $password);
            
            
//             print "<pre>\n";
//
//                print_r( "Request :\n".htmlspecialchars($client->__getLastRequest())."\n");
//                print "Response:\n".htmlspecialchars($client->__getLastResponse())."\n";
//                print "</pre>";
         
        } catch (SoapFault $fault)
        {
            echo "Error instantiating SOAP object!\n";
            echo $fault->getMessage() . "\n";
        }
         
         return $response;
        
        }
    /*  Shugar сервис
     *   
     *  ClubProfileData
     *  Профиль.
     * 
     *  Возможные ошибки бизнес-логики
     *       MobilePhoneNumberWrongFormat	Неверный формат номера телефона
     * 
     ***************************************************/
    
    public function ClubProfileData($userKey, $app='WESTONLINE' ){
        try{
           $client = new \SoapClient(null, $this->_options);
        
            $header_part = '<app xmlns="mmg">'.$app.'</app>';
            $header_p = new \SoapVar( $header_part, XSD_ANYXML, null, null, null  );
            $header = new \SoapHeader($app, 'app', $header_p );
            $client->__setSoapHeaders($header);
         
            $Profile = $client->ClubProfileData($userKey);
            $response = array(
                'failedCauseOf' => $Profile['failedCauseOf'],
                'isSuccessfully' => $Profile['isSuccessfully'],
                'details' => $Profile['details'],
                'entity' => array(
                    'creationDate'=>$Profile['entity'][0],
                    'environment'        => $Profile['entity'][1], //environment" type="xsd:string"
                    'login'              => $Profile['entity'][2], //login" type="xsd:string"
                    'registrationStep'   => $Profile['entity'][3], //registrationStep" type="xsd:string"
                    'address'            => $Profile['entity'][4], //address" type="xsd:string"
                    'birthDate'          => $Profile['entity'][5], //birthDate" type="xsd:date"
                    'contactsChangesLeft'=> $Profile['entity'][6], //contactsChangesLeft" type="xsd:decimal"
                    'email'              => $Profile['entity'][7], //email" type="xsd:string"
                    'fullNumber'         => $Profile['entity'][8], //phone" type="xsd:string"
                    'gender'             => $Profile['entity'][9], //gender" type="xsd:boolean"
                    'isGrey'             => $Profile['entity'][10], //xsd:element name="isGrey" type="xsd:boolean"
                    'familyName'         => $Profile['entity'][11], //familyName" type="xsd:string"
                    'givenName'          => $Profile['entity'][12], //givenName" type="xsd:string"
                    'patronymic'         => $Profile['entity'][13], //patronymic" type="xsd:string"
                    'notificationAllowed'=> $Profile['entity'][14], //notificationAllowed" type="xsd:boolean"
                    'referrerKey'        => $Profile['entity'][15], //referrerKey" type="xsd:boolean"
                    'smoker'             => $Profile['entity'][16], //smoker" type="xsd:boolean"
                    'achievements'       => $Profile['entity'][17], //achievements" type="tns:UserAchievementDataArray"
                    'balance'            => $Profile['entity'][18], //balance" type="xsd:decimal"
                    'balanceOperations'  => $Profile['entity'][19], //balanceOperations" type="tns:BalanceOperationEntryArray"
                    'canUsePanel'        => $Profile['entity'][20], //canUsePanel" type="tns:boolean"
                    'orders'             => $Profile['entity'][21], //orders" type="xsd:string"
                    'smokingHabbits'     => $Profile['entity'][22] //smokingHabbits" type="tns:smokingHabbits"
                )
            );
         
        } catch (SoapFault $fault)
        {
            echo "Error instantiating SOAP object!\n";
            echo $fault->getMessage() . "\n";
        }
         
         return $response;
        
        }
        
        
        
       
        
    /*  Shugar сервис
     *   
     *  FillSmokingHabbits
     *   Заполнение профиля курильщика.
     * 
     *  primaryCigaretteBrand	String	Основной бренд сигарет
        primaryCigarettePacksAmount	String	Количество пачек основного бренда, выкуриваемых в день
        primaryCigaretteSmokingExperience	String	Сколько человек курит сигареты основного бренда
        primaryCigaretteType	String	Основной тип сигарет
        secondaryCigaretteBrand	String	Альтернативный бренд сигарет
        secondaryCigarettePacksAmount	String	Количество пачек альтернативного бренда, выкуриваемых в день
        secondaryCigaretteSmokingExperience	String	Сколько человек курит сигареты альтернативного бренда
        secondaryCigaretteType	String	Альтернативный тип сигарет

     * 
     *  Возможные ошибки бизнес-логики
     *     TBD
     * 
     ***************************************************/    
        
    public function FillSmokingHabbits($userKey, $primaryCigaretteBrand, $primaryCigarettePacksAmount, $primaryCigaretteSmokingExperience,
                                       $primaryCigaretteType, $secondaryCigaretteBrand, $secondaryCigarettePacksAmount, $secondaryCigaretteSmokingExperience,
                                       $secondaryCigaretteType, $app='WESTONLINE' ){
        try{
           $client = new \SoapClient(null, $this->_options);
        
            $header_part = '<app xmlns="mmg">'.$app.'</app>';
            $header_p = new \SoapVar( $header_part, XSD_ANYXML, null, null, null  );
            $header = new \SoapHeader($app, 'app', $header_p );
            $client->__setSoapHeaders($header);
         
            $response = $client->FillSmokingHabbits($userKey, $primaryCigaretteBrand, $primaryCigarettePacksAmount, $primaryCigaretteSmokingExperience,
                                                   $primaryCigaretteType, $secondaryCigaretteBrand, $secondaryCigarettePacksAmount, $secondaryCigaretteSmokingExperience,
                                                    $secondaryCigaretteType);
            
   
         
        } catch (SoapFault $fault)
        {
            echo "Error instantiating SOAP object!\n";
            echo $fault->getMessage() . "\n";
        }
         
         return $response;
        
        }
        
        
        public function changeUserPassword($userKey, $password){
            
            $ret = array();
            $Profile  = $this->ClubProfileData($userKey);
           
            if ($Profile['failedCauseOf'] == 'UserNotFound'){
               $ret['failedCauseOf']= 'UserNotFound';
               $ret['isSuccessfully']= '0';
            } else {
           
            
            $response =  $this->UpdateProfile($userKey, 
                                    $Profile['entity']['birthDate'],
                                    $Profile['entity']['email'], 
                                    $Profile['entity']['login'], //alias
                                    $Profile['entity']['gender']?null:0,
                                    $Profile['entity']['fullNumber']?null:0,
                                    $Profile['entity']['familyName'], 
                                    $Profile['entity']['givenName'], 
                                    $Profile['entity']['patronymic'],
                                    $Profile['entity']['notificationAllowed']?null:0, 
                                    $Profile['entity']['smoker']?null:0, 
                                    $password);
         
                           // echo '<pre>';        
                          //  print_r($response);
         //    $responceArray =  ShugarCrmConnector::objectToArray($response);   
        //     if ($responceArray['isSuccessfully'] == '1')
             if ($response['isSuccessfully'] == '1')
              {
                   $ret['failedCauseOf']= 'PasswordChanged';
                   $ret['isSuccessfully']= '1';
              }  
              else {
                 $ret['failedCauseOf']= 'MobilePhoneNumberWrongFormat';
                 $ret['isSuccessfully']= '0'; 
              }
               
            }
            
            return $ret;
              
             
            
            
        }
        
        
        
    /*  Shugar сервис
     *   
     *  setAgencySurvey
     *   Заполнение данных после исследования.
     * 
        userKey      varchar(255),
        hostess_id   varchar(45),
        activity_dt  date,
        activity_loc varchar(45), 
        test_res    TEXT,
        test_id      int,		
        activity_type int,
        activity_id   int,
        advanced_data  TEXT

     * 
     *  Возможные ошибки бизнес-логики
     *     TBD
     * 
     ***************************************************/    
        
    public function setAgencySurvey($userKey, $hostess_id, $activity_dt, $activity_loc,
                                       $test_res, $test_id, $activity_type, $activity_id,
                                       $advanced_data, $app='WESTONLINE' ){
        try{
           $client = new \SoapClient(null, $this->_options);
        
            $header_part = '<app xmlns="mmg">'.$app.'</app>';
            $header_p = new \SoapVar( $header_part, XSD_ANYXML, null, null, null  );
            $header = new \SoapHeader($app, 'app', $header_p );
            $client->__setSoapHeaders($header);
         
            $response = $client->setAgencySurvey($userKey, $hostess_id, $activity_dt, $activity_loc,
                                                    $test_res, $test_id, $activity_type, $activity_id,
                                                    $advanced_data);
            
   
         
        } catch (SoapFault $fault)
        {
            echo "====== REQUEST HEADERS =====" . PHP_EOL;
            echo htmlspecialchars($client->__getLastRequestHeaders());
            echo "========= REQUEST ==========" . PHP_EOL;
            var_dump($client->__getLastRequest());

             echo "========= REQUEST ==========" . PHP_EOL;
             echo htmlspecialchars($client->__getLastRequest());
            echo "========= RESPONSE =========" . PHP_EOL;

//            echo "Error instantiating SOAP object!\n";
//            echo $fault->getMessage() . "\n";
//            
//            echo "Error instantiating SOAP object!\n";
//            echo $fault->getMessage() . "\n";
        }
         
         return $response;
        
        }

        
        /*  Shugar сервис
     *   
     *  RegisterBasicMassive
     *   Регистрация пользователей пачкой
     * 
        familyName
        givenName
        birthDate
        gender
        notificationAllow
        smoker
        externalId
        patronymic
        password
        fullNumber
        address
         * 
         * $response = $client->RegisterBasicMassive('[{"familyName": "Иванов", "givenName": "Иван", "address": "ivan@1.ru", "birthDate": "1970-01-02", "fullNumber": "12345"}, {"familyName": "Петров", "givenName": "Петр", "address": "petr@1.ru", "birthDate": "1980-05-06", "fullNumber": "765432"}]');


     * 
     *  Возможные ошибки бизнес-логики
     *     TBD
     * 
     ***************************************************/    
        
    public function RegisterBasicMassive($p_json_array, $app='WESTONLINE' ){
        try{
           $client = new \SoapClient(null, $this->_options);
        
            $header_part = '<app xmlns="mmg">'.$app.'</app>';
            $header_p = new \SoapVar( $header_part, XSD_ANYXML, null, null, null  );
            $header = new \SoapHeader($app, 'app', $header_p );
            $client->__setSoapHeaders($header);
         
            $response = $client->RegisterBasicMassive($p_json_array);
            
   
         
        } catch (SoapFault $fault)
        {
            echo "====== REQUEST HEADERS =====" . PHP_EOL;
            echo htmlspecialchars($client->__getLastRequestHeaders());
            echo "========= REQUEST ==========" . PHP_EOL;
            var_dump($client->__getLastRequest());

             echo "========= REQUEST ==========" . PHP_EOL;
             echo htmlspecialchars($client->__getLastRequest());
            echo "========= RESPONSE =========" . PHP_EOL;

//            echo "Error instantiating SOAP object!\n";
//            echo $fault->getMessage() . "\n";
//            
//            echo "Error instantiating SOAP object!\n";
//            echo $fault->getMessage() . "\n";
        }
         
         return $response;
        
        }
        
        public static function objectToArray($d) {
                if (is_object($d)) {
                    // Gets the properties of the given object
                    // with get_object_vars function
                    $d = get_object_vars($d);
                }
                else {
                    // Return array
                    return $d;
                }
            }

    
}