<?php


namespace app\components;

class Cconnector  {
    
    public $app = '';
    public $wsdl = '';
    public $auth = array();


    protected $soap;
    protected $header;

    
    public  function getClient(){
     try{
         $client = new SoapClient($this->wsdl, array('trace' => 0, 'exceptions'=>0, 
                                                                              'style' => SOAP_RPC));
        }
        catch (SoapFault $fault)
        {

            echo "====== REQUEST HEADERS =====" . PHP_EOL;
            echo htmlspecialchars($client->__getLastRequestHeaders());
            echo "========= REQUEST ==========" . PHP_EOL;
            var_dump($client->__getLastRequest());

             echo "========= REQUEST ==========" . PHP_EOL;
             echo htmlspecialchars($client->__getLastRequest());
            echo "========= RESPONSE =========" . PHP_EOL;

            echo "Error instantiating SOAP object!\n";
            echo $fault->getMessage() . "\n";
        }  
        $header = new SoapHeader('WESTONLINE', 'WestCrm', $this->auth);
         
        $response = $client->__soapCall($method, $params, null, $header);
        
        return $response;
        
        
    }



    
}