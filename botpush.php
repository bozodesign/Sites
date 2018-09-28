<?php



require "vendor/autoload.php";

$access_token = '8Le9lmjYEwsc5YPmUQyfAe6vfshkOeh8WPRbRycCechhIeI07GYSvKZpBKhvlyfpSSCvDVppj3UA5zmGMIhRv9/dDHa/0EObr0Dzl8ZjmnAnGRITnHblXotuoG25wvm0zUYv1jAmoeUxpL9l81N/0QdB04t89/1O/w1cDnyilFU=';

$channelSecret = '9ce9fda59806969a398362cc1954fa30';

$pushID = 'U0213d568be91b71137f9d1c707cd1c59';

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($access_token);
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $channelSecret]);

$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('hello world');
$response = $bot->pushMessage($pushID, $textMessageBuilder);

echo $response->getHTTPStatus() . ' ' . $response->getRawBody();







