<?php

include "connection.php";
session_start();

$user = $_SESSION["user"];

if (!empty($_POST["decision"])){
    $decision = $_POST["decision"];
    $friend = $_POST["friend"];

    $sql = "SELECT * FROM users WHERE username='$user'";
    $res = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($res);
    $pos = strpos($row['requests'],";$friend")+1+strlen($friend);
    $newRequests = substr($row['requests'],0,strpos($row['requests'],";$friend")) . substr($row['requests'],$pos);
    $sql = "UPDATE users SET requests='$newRequests' WHERE username='$user'";
    if ($conn->query($sql) === FALSE) { echo "Error: " . $sql . "<br>" . $conn->error; }

    if ($decision == "y"){
        $sql = "SELECT * FROM users WHERE username='$user'";
        $res = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($res);
        $newFriends = $row['friends'] . $friend . ";";
        $sql = "UPDATE users SET friends='$newFriends' WHERE username='$user'";
        if ($conn->query($sql) === FALSE) { echo "Error: " . $sql . "<br>" . $conn->error; }
        $sql = "SELECT * FROM users WHERE username='$friend'";
        $res = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($res);
        $newFriends = $row['friends'] . $user . ";";
        $sql = "UPDATE users SET friends='$newFriends' WHERE username='$friend'";
        if ($conn->query($sql) === FALSE) { echo "Error: " . $sql . "<br>" . $conn->error; }
    }
}
else if (!empty($_POST["friend"])){
    $friend = $_POST["friend"];
    if ($friend === $user){
        echo "You cannot send a request to yourself";
    } else {
        $sqlf = "SELECT * FROM users WHERE username='$friend'";
        $resf = mysqli_query($conn, $sqlf);
        $rowf = mysqli_fetch_assoc($resf);
        if(mysqli_num_rows($resf) > 0){
            $sqlu = "SELECT * FROM users WHERE username='$user'";
            $resu = mysqli_query($conn, $sqlu);
            $rowu = mysqli_fetch_assoc($resu);
            if (str_contains($rowu["friends"], ";$friend;")) { 
                echo "You are already friends with $friend";
            } else if (str_contains($rowf["requests"], ";$user;")){
                echo "You already sent a friend request to $friend";
            } else if (empty($rowf["username"])){
                echo "That user does not exist";
            } else {
                $newRequests = $rowu['requests'] . $user . ";";
                $sql = "UPDATE users SET requests='$newRequests' WHERE username='$friend'";
                if ($conn->query($sql) === FALSE) { 
                    echo "Error: " . $sql . "<br>" . $conn->error; 
                } else {
                    echo "Friend request sent to $friend";
                }
            }
        } else {
            echo "That user does not exist";
        }
    }
}
else if (!empty($_POST["accept"])){
    $sql = "SELECT * FROM users WHERE username='$user'";
    $res = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($res);
    echo $row["friends"];
} else {
    $sql = "SELECT * FROM users WHERE username='$user'";
    $res = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($res);
    echo $row["requests"];
}

?>
