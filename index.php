<?php
require 'vendor/autoload.php';
use Guzzle\Http\Client;
    
$client = new Client('https://scribblelive.p.mashape.com/', 
    array
    (
        'request.options' => array(
            'headers' => array("X-Mashape-Authorization" => "8HZNmWVe6XXI0Abu50NgzZoQ6V5Lfh1l"),
            'query' => array("Token" => "OBtoB7Tk"),
    )
));

$request = $client->get('/event/39048/page/last?PageSize=10');

$json = $request->send()->json();

echo $json["Title"];
?>