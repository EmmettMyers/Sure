<?php

include "connection.php";
session_start();

$user = $_SESSION["user"];

if (!empty($_POST["redirect"])){
    $url = $_POST["redirect"];
    $sql = "UPDATE users SET redirect='$url' WHERE username='$user'";
    if ($conn->query($sql) === FALSE){ 
        echo "Error: " . $sql . "<br>" . $conn->error; 
    } else {
        echo 'success';
    }
} else if (!empty($_POST["change"])){
    $image = $_POST["change"];
    $sql = "UPDATE users SET picture='$image' WHERE username='$user'";
    if ($conn->query($sql) === FALSE){ 
        echo "Error: " . $sql . "<br>" . $conn->error; 
    } else {
        echo 'success';
    }
} else if (!empty($_POST["friends"])) {
    $pictures = "";
    $friends = $_POST["friends"];
    while (strpos($friends,";") > -1){
        $friend = substr($friends, 0, strpos($friends,";"));
        $sql = "SELECT * FROM users WHERE username='$friend'";
        $res = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($res);
        $pictures = $pictures . $row["picture"] . ";";
        $friends = substr($friends, strpos($friends,";")+1);
    }
    echo $pictures;
} else {
    $sql = "SELECT * FROM users WHERE username='$user'";
    $res = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($res);
    echo $row["picture"];
}

?>
