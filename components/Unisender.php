<?php

namespace app\components;


class Unisender {
  
    private $UnisenderKey = '5c4zsonozq3mbtwhgj8'; //kxxb
    
 
    /*******
     * uniConnect  - метод для вызова АПИ Юнисендер
     * $param - массив значений
     * $method - url метода 
     * 
     * возвращает массив с содержанием ошибки и полученными даннымми от Unisender
     */
    
    private static function uniConnect($param = [], $method=''){
        
        $jsonObj = '';
        $ret = "Error";
         $POST = $param;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $POST);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_URL, $method);
            $result = curl_exec($ch);
            
             if ($result) {
              // Раскодируем ответ API-сервера
              $jsonObj = json_decode($result);

              if(null===$jsonObj) {
                // Ошибка в полученном ответе
                $ret = "Invalid JSON";

              }
              elseif(!empty($jsonObj->error)) {
                // Ошибка получения перечня список
                $ret = "An error occured: " . $jsonObj->error . "(code: " . $jsonObj->code . ")";

              } else {
                // Выводим коды и названия всех имеющихся списков
                $ret = "Транспорт Unisender: данные обработанны";
//                foreach ($jsonObj->result as $one) {
//                  $ret .= "List #" . $one->id . " (" . $one->title . ")". "<br>";
//                }

              }
            } else {
              // Ошибка соединения с API-сервером
              $ret = "API access error";
            }

         
        
       return [
            'message'=>$ret,
            'UnisenderAnswer'=>$jsonObj
       ]; 
    }

    public static function createLists($title, $api_key ='5c4zsonozq3mbt'){
        $ret = '';
        
        
        // Создаём POST-запрос
            $POST = array (
              'api_key' => $api_key,
              'title'=>$title  
            );
            
            $url = 'http://api.unisender.com/ru/api/createList?format=json';
            
            
            $ret = self::uniConnect($POST, $url);
            return $ret;
        
    }
    public static function getLists( $api_key ='5c4zsonozq3mbtw'){
        $ret = '';
        
  
        // Создаём POST-запрос
            $POST = array (
              'api_key' => $api_key,
            );
            
            $url = 'http://api.unisender.com/ru/api/getLists?format=json';
            
            
            $ret = self::uniConnect($POST, $url);
            return $ret;
        
    }
    
    
/*
 * importContacts - массовый импорт и синхронизация контактов
 * Импортировать можно данные не более 500 подписчиков за вызов.
 * Технические ограничения: максимальное количество пользовательских полей равно 50. 
 * Таймаут на один вызов составляет 30 секунд с момента полной передачи запроса на сервер. 
 * Если по истечении таймаута ответ не получен, то рекомендуется сделать до двух повторных попыток,
 *    и если ответа снова нет, тогда обращаться в техническую поддержку.
 */

    public static function importContacts($list_id, $contacts = [], $api_key ='5c4zsonozq3mbtwhgj883psnis4xyzkzaxuzbb3o'){
    $ret = '';
        
        
        $url = 'http://api.unisender.com/ru/api/importContacts?format=json';
        
        
            // Создаём POST-запрос
            $POST = array (
              'api_key' => $api_key,
              'field_names[0]' => 'email',
              'field_names[1]' => 'Name',
              'field_names[2]' => 'phone',
              'field_names[3]' => 'email_list_ids',
              'field_names[4]' => 'balcan_personal_message',
              
              
                
            );
            $i =0;
            foreach ($contacts as $contact){
                
              $POST['data[' . $i .'][0]'] = $contact['email'];
              $POST['data[' . $i .'][1]'] = $contact['Name'];
              $POST['data[' . $i .'][2]'] = $contact['phone'];
              $POST['data[' . $i .'][3]'] = $list_id;
              $POST['data[' . $i .'][4]'] = $contact['balcan_personal_message'];
              
              
              
              ++$i;
            }
          
        
            
            $ret = self::uniConnect($POST, $url);
            return $ret;
    
    }
    
    
    public static function exportContacts($list_id, 
                                          $api_key ='5c4zsonozq3mbtwhgj883psnis4xyzkzaxuzbb3o',
                                          $offset = 0,  
                                          $limit=1000, 
                                          $field_names = [], 
                                          $email = '', $email_status=''
            ){
        
        $url = 'http://api.unisender.com/ru/api/exportContacts?format=json';
        
        // Создаём POST-запрос
        $POST = array (
              'api_key' => $api_key,
              'list_id' => $list_id,
              //'field_names'=>  $field_names,
              'offset' => $offset,
              'limit' => $limit,
                
            );


            $ret = self::uniConnect($POST, $url);
            return $ret;
        
    }
    
    
    
    /*
     * subscribe - подписать адресата на один или несколько списков рассылки
     *  $double_optin - Число от 0 до 3 - есть ли подтверждённое согласие подписчика, и что делать, если превышен лимит подписок.
                        Если 0, то мы считаем, что подписчик только высказал желание подписаться, но ещё не подтвердил подписку. В этом случае подписчику будет отправлено письмо-приглашение подписаться. 
     *                      Текст письма будет взят из свойств первого списка из list_ids. 
     *                      Кстати, текст можно поменять с помощью метода updateOptInEmail или через веб-интерфейс.
                        Если 1, то мы считаем, что у Вас уже есть согласие подписчика. Но при этом для защиты от злоупотреблений есть суточный лимит подписок. 
     *                          Если он не превышен, мы не посылаем письмо-приглашение. 
     *                          Если же он превышен, подписчику высылается письмо с просьбой подтвердить подписку. 
     *                          Текст этого письма можно настроить для каждого списка с помощью метода updateOptInEmail или через веб-интерфейс. 
     *                          Лимиты мы согласовываем в индивидуальном порядке.
                        Если 2, то также считается, что у Вас согласие подписчика уже есть, но в случае превышения лимита мы возвращаем код ошибки too_many_double_optins.
                        Если 3, то также считается, что у Вас согласие подписчика уже есть, подписчик добавляется со статусом «новый».
     */
    public static function subscribe($list_id, $email, $name, $phone, $personal_message, $britain_percent,
            $fields = [],
            $double_optin = 0,  $api_key ='5c4zsonozq3mbtwhgj883psnis4xyzkzaxuzbb3o'){
         
         $url = 'http://api.unisender.com/ru/api/subscribe?format=json';
//          
//         $POST = array (
//              'api_key' => $api_key,
//              'list_ids' => $list_id,
//              'fields[email]' => $email,
//              'fields[Name]' => $name,
//              'double_optin'=>$double_optin
//            );
         
         $settings = [
                'api_key' => $api_key,
                'list_ids' => $list_id,
                'double_optin'=>$double_optin
         ];
         $res = array_merge($settings,$fields);
         
         // Создаём POST-запрос
//            $POST = array (
//           
//              'fields[email]' => $email,
//              'fields[Name]' => $name,
//              'fields[phone]' => $phone,
//              'fields[personal_message]' => $personal_message,
//              'fields[britain_percent]' => $britain_percent,
//              
//                
//            );
            
         
            
         //   $ret = self::uniConnect($POST, $url);
            $ret = self::uniConnect($res, $url);
            return $ret;
        
    }
    
    /*
     * Более адекватная и универсальная функция подписки контатктов
     * subscribe - подписать адресата на один или несколько списков рассылки
     *  $double_optin - Число от 0 до 3 - есть ли подтверждённое согласие подписчика, и что делать, если превышен лимит подписок.
                        Если 0, то мы считаем, что подписчик только высказал желание подписаться, но ещё не подтвердил подписку. В этом случае подписчику будет отправлено письмо-приглашение подписаться. 
     *                      Текст письма будет взят из свойств первого списка из list_ids. 
     *                      Кстати, текст можно поменять с помощью метода updateOptInEmail или через веб-интерфейс.
                        Если 1, то мы считаем, что у Вас уже есть согласие подписчика. Но при этом для защиты от злоупотреблений есть суточный лимит подписок. 
     *                          Если он не превышен, мы не посылаем письмо-приглашение. 
     *                          Если же он превышен, подписчику высылается письмо с просьбой подтвердить подписку. 
     *                          Текст этого письма можно настроить для каждого списка с помощью метода updateOptInEmail или через веб-интерфейс. 
     *                          Лимиты мы согласовываем в индивидуальном порядке.
                        Если 2, то также считается, что у Вас согласие подписчика уже есть, но в случае превышения лимита мы возвращаем код ошибки too_many_double_optins.
                        Если 3, то также считается, что у Вас согласие подписчика уже есть, подписчик добавляется со статусом «новый».
     */
    public static function subscribe2($list_id, 
                                       $fields = [],
                                       $double_optin = 0,
                                        $api_key ='5c4zsonozq3mbtwhgj883psnis4xyzkzaxuzbb3o'){
         
         $url = 'http://api.unisender.com/ru/api/subscribe?format=json';

         $settings = [
                'api_key' => $api_key,
                'list_ids' => $list_id,
                'double_optin'=>$double_optin
         ];
         $res = array_merge($settings,$fields);
         
      
            $ret = self::uniConnect($res, $url);
            return $ret;
        
    }
    
    
    public static function updateOptInEmail($list_id, $email_from_name, $email_from_email, $email_subject, $email_text,  $api_key ='5c4zsonozq3mbtwhgj883psnis4xyzkzaxuzbb3o'){
        $ret = '';
        
        
//        $email_from_name = 'Wet BTL'; //Имя отправителя. Произвольная строка, не совпадающая с e-mail адресом (аргумент sender_email).
//        $email_from_email = 'kxxxxb@yandex.ru'; //E-mail адрес отправителя. Этот e-mail должен быть проверен (для этого надо создать вручную хотя бы одно письмо с этим обратным адресом через веб-интерфейс, затем нажать на ссылку «отправьте запрос подтверждения» и перейти по ссылке из письма).
//        $email_subject = '{{Name}}, Вы согласны получать наши новости?'; //Строка с темой письма. Может включать поля подстановки.
//        $email_text =  "<h3> Здравствуйте, {{Name}}. </h3> ".
//                            "<br>Недавно вы прошли тест, определяющий, на сколько Вы Британец."
//                           . "<br>Мы подготвили его результат, и хотим поделится им с Вами. "
//                           . "<br>Для этого Вам необходимо подтвердить, что вы готовы получать новости от нас, <a href='{{ConfirmUrl}}'> кликнув по ссылке </a>. "
//                           . "<br>Обещаем писать только по делу. "
//                          . "<br>Спасибо!  ";       
//        
//        
//$email_text = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
//                <html xmlns="http://www.w3.org/1999/xhtml">
//                    <head>
//                        <title>P&S</title>        
//                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
//                        <meta name="viewport" content="width=device-width, initial scale=1.0"/>
//                        <style type="text/css">
//                            /* Preheader declaration in style block in addition to inline for Outlook */
//                            .preheader { display:none !important; visibility:hidden; opacity:0; color:transparent; height:0; width:0; }
//                            html { -webkit-text-size-adjust:none; -ms-text-size-adjust: none;}
//                            @media only screen and (min-device-width: 600px) {
//                                .table600{
//                                    width:600px !important;
//                                }
//                            }
//                            @media only screen and (max-device-width: 600px), only screen and (max-width: 600px){ 
//                                *[class="table600"]{
//                                    width: 100% !important;
//                                }
//                            }
//                            .table600{
//                                width:600px;
//                            }
//
//                        </style>
//                    </head>
//                    <body style="margin: 0; padding: 0;" bgcolor="#ffffff">
//                        <span class="preheader" style="display: none !important; visibility: hidden; opacity: 0; color: transparent; height: 0; width: 0;">
//                            {{Name}}, Вы согласны получать наши новости?
//                        </span>
//                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
//                            <tr>
//                                <td align="center" bgcolor="#ffffff">
//
//                                    <!--[if gte mso 9]>
//                                    <table width="600" border="0" cellspacing="0" cellpadding="0">
//                                    <tr><td>
//                                    <![endif]-->
//
//                                    <table border="0" cellspacing="0" bgcolor="#000000" cellpadding="0" class="table600" width="100%" style="max-width: 600px; min-width:320px;">                        
//                                        <tr>
//                                            <td width="100%" background="http://static.atoms.ru/img/bg-ps.jpg" style="background-repeat: no-repeat; background-size: cover;">
//                                                <table cellpadding="0" cellspacing="0" width="100%">
//                                                    <tr>
//                                                        <td width="15%" class="devicebottomtd1">&nbsp;</td>
//                                                        <td width="88%" style="padding-top: 10%; padding-bottom: 20%;">
//                                                            <p style="font-size:18px;color:#fbdf96;font-family: Arial, Verdana, sans-serif; text-align: left; line-height: 1.4;">
//                                                                <b> Здравствуйте, {{Name}}. </b>
//                                                            </p>
//                                                            <p style="margin-top: 6%; font-size:14px;color:#fbdf96;font-family: Arial, Verdana, sans-serif; text-align: left; line-height: 1.6;">
//                                                                <br>Недавно вы прошли тест, на знание Британской кльтуры.
//                                                                <br>Мы подготовили результат, и хотим поделится им с вами. 
//                                                                <br>Для этого необходимо подтвердить, что вы согласны получать наши новости, <a href="{{ConfirmUrl}}"> кликнув по этой ссылке </a>.
//                                                                <br>Обещаем писать только по делу.
//                                                                <br>Спасибо! 
//
//                                                            </p>
//                                                        </td>
//                                                        <td width="15%" class="devicebottomtd1">&nbsp;</td>
//                                                    </tr>
//                                                </table>
//                                            </td>
//                                        </tr>
//                                    </table>
//
//                                    <!--[if gte mso 9]>
//                                    </td></tr>
//                                    </table>
//                                    <![endif]-->
//                                </td>
//                            </tr>
//                        </table>
//                    </body>
//                </html>';
        
        
        // Создаём POST-запрос
            $POST = array (
              'api_key' => $api_key,
              'sender_name' => $email_from_name,
              'sender_email' => $email_from_email,
              'subject' => $email_subject,
              'list_id' => $list_id,
              'body' => $email_text,
            );
            
            $url = 'http://api.unisender.com/ru/api/updateOptInEmail?format=json';
            
            
            $ret = self::uniConnect($POST, $url);
            return $ret;
        
    }
    
    
    public static function createEmailMessage($list_id, $email_subject, $email_text, $email_from_name,  $email_from_email, $api_key ='5c4zsonozq3mbtwhgj883psnis4xyzkzaxuzbb3o'){
        $ret = '';
        $url = 'http://api.unisender.com/ru/api/createEmailMessage?format=json';
        
        
        //$img_file = UPLOAD_DIR.$img_file_name;
        
        
     //   $email_from_name = 'Wet BTL'; //Имя отправителя. Произвольная строка, не совпадающая с e-mail адресом (аргумент sender_email).
     //   $email_from_email = 'kxxxxb@yandex.ru'; //E-mail адрес отправителя. Этот e-mail должен быть проверен (для этого надо создать вручную хотя бы одно письмо с этим обратным адресом через веб-интерфейс, затем нажать на ссылку «отправьте запрос подтверждения» и перейти по ссылке из письма).
        //$email_subject = '{{Name}},  читай результаты теста от Simpsons в этом письме!!'; //Строка с темой письма. Может включать поля подстановки.
        $body = ''; /*Текст письма в формате HTML с возможностью добавлять поля подстановки.
                    * Текст может включать и относительные ссылки на изображения, хранящиеся в папке пользователя на нашем сервере – такие изображения будут включены в само письмо.
                    * Ссылки на изображения на сервере должны иметь вид: "/ru/user_file?resource=images&name=IMAGE", где вместо IMAGE должно быть имя файла из вашей папки на сервере, напримерimage.jpg или folder/image.jpg. 
                    * Если же изображение не хранится на нашем сервере, то вы можете вставить картинку, передав её как файл-вложение (см. описание аргумента attachments).
                    * Предполагается, что HTML-текст содержит только содержимое тега body. Если вы передаёте текст HTML целиком, то тестируйте такие письма дополнительно – заголовки вне body могут быть подвергнуты модификациям. 
                    * Кроме того, чтобы уменьшить расхождение в отображении в различных почтовых программах, мы автоматически добавляем дополнительную разметку в каждое письмо 
                    * (таблица с невидимыми границами, которая также задаёт шрифт по умолчанию и выравнивание текста по левой границе). 
                    * Вы можете попросить отключить это для ваших писем, обратившись в техподдержку.    
                    */
        
        //$attachments = ''; 
        /*Ассоциативный массив файлов-вложений. В качестве ключа указывается имя файла, 
                            * в качестве значения - бинарное содержимое файла (base64 использовать нельзя!), например:
                            * 
                            * attachments[quotes.txt]=text%20file%content
                            * 
                            * Используя скрипт PHP, содержимое файла можно получить через функцию file_get_contents. Например: $api_query = array(....,"attachments[test.pdf]"=>file_get_contents('test.pdf'),...);
                            * В сообщение вложения будут добавлены в том же порядке, в котором перечислены. Можно вставлять в текст письма inline-картинки, добавляя их как файлы-вложения и ссылаясь на них в HTML так: img src="3_name.jpg" . 
                            * Вместо числа три надо подставить порядковый номер вложения, а вместо name.jpg - имя вложения.
                            * */

        
        
//            $email_text =  " Привет, {{Name}}, ".
//                              "<br><table background='$img' width='350' height='200'>"
//                               . "<tr><td>Вот наша рассылка с картинкой </td></tr>"
//                                . " </table>";         
        //      $api_query = array("attachments[AppIconSib.png]"=>file_get_contents($img));
        
        
        // Создаём POST-запрос
          $POST = array (
            'api_key' => $api_key,
            'sender_name' => $email_from_name,
            'sender_email' => $email_from_email,
            'subject' => $email_subject,
            'list_id' => $list_id,
           // 'wrap_type' => $email_wrap,
            //'categories' => $email_categories,
            'body' => $email_text,
         //   "attachments[$img_file_name]"=>file_get_contents($img_file)  
        //    $api_query
            
                
            );
            
            
            
            
            $ret = self::uniConnect($POST, $url);
            return $ret;
        
    }
    
   
    
    /** 
     * createCampaign запланировать массовую отправку e-mail или SMS сообщения
     * Запланировать или начать немедленно рассылку e-mail или SMS-сообщения. 
     * Одно и то же сообщение можно отправлять несколько раз, но моменты отправки не должны быть ближе, чем на час друг к другу.
     * Этот метод используется для отправки уже созданных сообщений. 
     * Для предварительного создания сообщений надо использовать методы createEmailMessage и createSmsMessage
     * 
     */
    public static function createCampaign($email_id, $api_key ='5c4zsonozq3mbtwhgj883psnis4xyzkzaxuzbb3o') {
        $ret = '';
        $url = 'http://api.unisender.com/ru/api/createCampaign?format=json';
        
        
        
       // $email_id =  37202370;
        $email_stats_read = 1;
        $email_stats_links = 1;
                
        
        $POST = array (
            'api_key' => $api_key,
            'message_id' => $email_id,
        //    'start_time' => $email_starttime,
            'track_read' => $email_stats_read,
            'track_links' => $email_stats_links
          );
        
           $ret = self::uniConnect($POST, $url);
          return $ret;
    }

    
    
    
     public static function getCampaignDeliveryStats($id, $api_key ='5c4zsonozq3mbtwhgj883psnis4xyzkzaxuzbb3o') {
        $ret = '';
        $url = 'http://api.unisender.com/ru/api/getCampaignDeliveryStats?format=json';
        
         
        $POST = array (
            'api_key' => $api_key,
            'campaign_id' => $id,
            //'fields'=> ['email', 'send_result', 'last_update','not_sent',]
          );
        
           $ret = self::uniConnect($POST, $url);
          return $ret;
    } 
    
    public static function getCampaignAggregateStats($id, $api_key ='5c4zsonozq3mbtwhgj883psnis4xyzkzaxuzbb3o') {
        $ret = '';
        $url = 'http://api.unisender.com/ru/api/getCampaignAggregateStats?format=json';
        
         
        $POST = array (
            'api_key' => $api_key,
            'campaign_id' => $id,
          );
        
           $ret = self::uniConnect($POST, $url);
          return $ret;
    } 
    public static function getCampaignStatus($id, $api_key ='5c4zsonozq3mbtwhgj883psnis4xyzkzaxuzbb3o') {
        $ret = '';
        $url = 'http://api.unisender.com/ru/api/getCampaignStatus?format=json';
        
         
        $POST = array (
            'api_key' => $api_key,
            'campaign_id' => $id,
          );
        
           $ret = self::uniConnect($POST, $url);
          return $ret;
    } 
   
    
    public static function createField($new_field_name,$new_field_type='text', $api_key ='5c4zsonozq3mbtwhgj883psnis4xyzkzaxuzbb3o') {
        $ret = '';
        $url = 'http://api.unisender.com/ru/api/createField?format=json';
        
         
        $POST = array (
            'api_key' => $api_key,
            'name' => $new_field_name,
            'type' => $new_field_type
          );
        
           $ret = self::uniConnect($POST, $url);
          return $ret;
    } 
    
    
}
