<?php

namespace App\Vendor\cosmicjs;

class CosmicCurl {

  public function get($url){

    $ch = curl_init($url);                                                                      
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                                                                    
    $result = curl_exec($ch);
    return $result;
  }

  public function post($url, $object_string){

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $object_string);                                                                  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
      'Content-Type: application/json',                                                                                
      'Content-Length: ' . strlen($object_string))                                                                       
    );

    $output = curl_exec($ch);
    curl_close($ch);
    return $output;

  }

  public function put($url, $object_string){

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $object_string);                                                                  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
      'Content-Type: application/json',                                                                                
      'Content-Length: ' . strlen($object_string))                                                                       
    );

    $output = curl_exec($ch);
    curl_close($ch);
    return $output;

  }


  public function delete($url, $object_string){

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $object_string);                                                                  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
      'Content-Type: application/json',                                                                                
      'Content-Length: ' . strlen($object_string))                                                                       
    );

    $output = curl_exec($ch);
    curl_close($ch);
    return $output;

  }

}
?>