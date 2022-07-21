<?php

include "connection.php";
session_start();

$user = $_SESSION["user"];
$friend = $_POST["friend"];
$action = $_POST["action"];
$content = $_POST["content"];

if ($action == "delete"){
    $sql = "DELETE FROM messages WHERE sender='$user' AND receiver='$friend' AND content='$content'";
    if ($conn->query($sql) === FALSE) { echo "Error: " . $sql . "<br>" . $conn->error; }
} else {
    $sql = "UPDATE messages SET content='$content' WHERE sender='$user' AND receiver='$friend'";
    if ($conn->query($sql) === FALSE) { echo "Error: " . $sql . "<br>" . $conn->error; }
}

?>
