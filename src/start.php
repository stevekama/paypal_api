<?php

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

session_status();

$_SESSION['user_id'] = 1; 

require __DIR__ . '/../vendor/autoload.php';

// API 
$api = new ApiContext(
    new OAuthTokenCredential(
        'Aa2x4F8qqit70d4HZbqzsVS6ptam_uqvMgMVfLfqtKXH7zpvQ1ljYRESol2CBdHRQnNtbZ_fMh6DqWV7',
        'ECasTuh-DN9JJCj8A-toI3zfyyxzIHjR0tBwdHj79ljk4fE9Cu_JxM5s0vMmglStPKKTrgJaA_ecqOW3'
    )
);

$api->setConfig([
    'mode' => 'sandbox',
    'http.ConnectionTimeOut' => 30,
    'log.LogEnabled' => false,
    'log.FileName' => '',
    'log.LogLevel' => 'FINE',
    'validation.level' => 'log'

]);

$db = new PDO('mysql:host=localhost;dbname=paypaltest', 'root', '');

$user = $db->prepare("
    SELECT * FROM users 
    WHERE id = :user_id

");

$user->execute([
    'user_id'=>$_SESSION['user_id']    
]);

$user = $user->fetchObject();
