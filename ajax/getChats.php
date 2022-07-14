<?php

include "connection.php";
session_start();

$user = $_SESSION["user"];
$receiver = $_COOKIE["receiver"];

if (!empty($_POST["message"])){
    $chat = $_POST["message"];
    date_default_timezone_set('America/Chicago');
    $time = date('m/d/Y h:i:s a', time());
    $sql = "INSERT INTO messages (sender,receiver,content,time) VALUES ('$user','$receiver','$chat','$time')";
    if ($conn->query($sql) === FALSE) { echo "Error: " . $sql . "<br>" . $conn->error; }
}

$chat = array();

$sql_c = "SELECT * FROM messages WHERE sender='$user' AND receiver='$receiver'";
$res_c = mysqli_query($conn, $sql_c);
if (mysqli_num_rows($res_c) > 0) {
    while ($row = mysqli_fetch_assoc($res_c)){
        $setChat = $row['content'] . "{{" . $row['time'] . "}}";
        array_push($chat, $setChat);
    }
}

array_push($chat, "<-|->");

$sql_c = "SELECT * FROM messages WHERE sender='$receiver' AND receiver='$user'";
$res_c = mysqli_query($conn, $sql_c);
if (mysqli_num_rows($res_c) > 0) {
    while ($row = mysqli_fetch_assoc($res_c)){
        $setChat = $row['content'] . "{{" . $row['time'] . "}}";
        array_push($chat, $setChat);
    }
}

echo implode($chat);

?>
