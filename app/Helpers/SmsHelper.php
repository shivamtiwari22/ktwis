<?php

use Twilio\Rest\Client as RestClient;

function sms($number,$message){

    $account_sid = 'AC7797b5de506c880a4a4302d7fe9119f3';
    $auth_token = '69b98e10d69bd80d28e47c4a8665972d' ;
    $twilio_number = '+18063045087';

    $client = new RestClient($account_sid, $auth_token);
    $sent = $client->messages->create($number, [
        'from' => $twilio_number,
        'body' => $message,
    ]);
    
}

