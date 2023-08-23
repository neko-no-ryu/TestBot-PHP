<?php
include 'define.php';

$data = json_decode(file_get_contents('php://input'), TRUE);
file_put_contents('filelog.txt', '$data: '.print_r($data, 1)."\n", FILE_APPEND);
if (isset($data['message'])) {
    $data = $data['message'];
    $message = mb_strtolower(($data['text'] ? $data['text'] : $data['data']), 'utf-8');

} else if (isset($data['action'])){
    $chat_id = -901048680;
    $data = $data['action']['data'];
    $text = "`" . $data['card']['name'] . "` была перемещена из `". $data['listBefore']['name'] . "` в `". $data['listAfter']['name'] . "`!";
    file_get_contents("https://api.telegram.org/bot" . TOKEN . "/sendMessage?chat_id=". $chat_id ."&text=". $text ."&parse_mode=HTML");

}
$db = new PDO('sqlite:base.db');


if(strpos($message, '/start') !== false) {
    $method = 'sendMessage';
    $send_data = ['text' => 'Привет, ' . $data['from']['username'] .'!'];
    $sql = "INSERT INTO `users`(id, username, id_chat) SELECT :id, :username, :id_chat WHERE NOT EXISTS (SELECT 1 FROM `users` WHERE id = :id)";
    $query = $db -> prepare($sql);
    $query -> execute(['id' => $data['from']['id'], 'username' => $data['from']['username'], "id_chat" => $data['chat']['id']]);

} else if (strpos($message, '/createlist') !== false) {
    $message = str_replace('/createlist ', '', $message);
    $id_board = '64e0ca6d7a5160105b36669f';
    $query = array(
        'name' => $message,
        'idBoard' => $id_board,
        'key' => TRELLO_API_KEY,
        'token' => TRELLO_API_TOKEN
    );
    
    $myCurl = curl_init();
    curl_setopt_array($myCurl, array(
    CURLOPT_URL => 'https://api.trello.com/1/lists',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($query)
    ));
    $response = curl_exec($myCurl);
    curl_close($myCurl);
    
    $method = 'sendMessage';
    $send_data = ['text' => $response];
} else if (strpos($message, '/setboard') !== false) {
    $message = str_replace('/setboard ', '', $message);
    $method = 'sendMessage';
    $query = $db -> prepare("UPDATE users SET id_board = :id_board WHERE id = :id");
    $query -> execute(['id' => $data['from']['id'], 'id_board' => $message]);
    $send_data = ['text' => "Активной доской для Вас была установлена: ". $message];

} else {
    $method = 'sendMessage';
    $send_data = ['text' => "Извини, не знаю такой команды! :`("];
}


$send_data['chat_id'] = $data['chat']['id'];
$res = sendTelegram($method, $send_data);

function sendTelegram($method, $data, $headers = []) {
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://api.telegram.org/bot' . TOKEN . '/' . $method,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array_merge(array('Content-Type: application/json'), $headers)
    ]);

    $result = curl_exec($curl);
    curl_close($curl);
    return (json_decode($result, 1) ? json_decode($result, 1) : $result);
}
