<?php 

function base_url(){
    $url = "http://localhost/paypal_api/";
    return $url;
}

function redirect_to($new_location){
    header("Location: ".$new_location);
    exit;
}