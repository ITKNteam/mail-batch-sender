<?php

/* 
 * @created at 2016
 * @author Kirill Shavrak.
 * kirill@shavrak.ru
 */

namespace app\components;

class BulkEmailChecker  {
    
    
    

    
    public static function check($email='sandbox-failed@bulkemailchecker.com'){
                $api_key = 'n0xdy3NFTkRMLg8YDa4f7';
                  // set the api key and email to be validated
                // use curl to make the request
                $url = 'http://api-v3.bulkemailchecker2.com/?key='.$api_key.'&email='.$email;
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); 
                curl_setopt($ch, CURLOPT_TIMEOUT, 15); 
                $response = curl_exec($ch);
                curl_close($ch);

                // decode the json response
                $result = json_decode($response, true);
                return $result;

         
       
        
        
    }



    
}