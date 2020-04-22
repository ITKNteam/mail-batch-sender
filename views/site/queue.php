<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



function test(){
    sleep(1);
    echo date('H:i:s').'<br>';
}

  $q = new \SplQueue();

    $q[] = 1;
    $q[] = 2;
    $q[] = test();
    $q[] = test();

    
 print_r($q);   


function addQuee( $q){

  
    foreach ($q as $elem)  {
     echo $elem."\n";
    }

    return 'ok';
}

// echo addQuee($q);