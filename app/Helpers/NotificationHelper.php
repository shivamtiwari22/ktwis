<?php
use Kutia\Larafirebase\Facades\Larafirebase;

  function notification($title,$body,$FcmToken){
      
    //   Larafirebase::withTitle($title)
    //   ->withBody($body)
    //   ->sendMessage($FcmToken);
 
    $data = [
        'registration_ids' => $FcmToken,
        'notification' => [
            'title' => $title,
            'body' => $body,
        ],
    ];

    $encodedData = json_encode($data);


    $headers = [
        'Authorization:key= AAAANfvJHnU:APA91bF1EvQ-KNzuluSeCn7BqsA3H3Hn9NfW63agYJw5hc7d_KXqo8DZuLjBtOsjsXwnRAaStBbeGu-et5yHmgl18dBkXJmlLOT7atMTa_Q8gVx-RvbLAB9I4NsBa1Q4DmVe08JFaZuA' ,
        'Content-Type: application/json',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
    $result = curl_exec($ch);
    if ($result === false) {
        die('Curl failed: ' . curl_error($ch));
    }
   
    curl_close($ch);

  }

