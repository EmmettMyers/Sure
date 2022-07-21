<?php

include "connection.php";
session_start();

$user = $_SESSION["user"];

if($_SERVER['REQUEST_METHOD'] == "POST"){
    if (!empty($_POST["removeAll"])){
        $sql = "DELETE FROM locks WHERE user='$user'";
        if ($conn->query($sql) === FALSE){ 
            echo "Error: " . $sql . "<br>" . $conn->error; 
        } else {
            echo 'success';
        }
    } else if (!empty($_POST["action"])){
        $friend = $_POST["friend"];
        $sql = "DELETE FROM locks WHERE user='$user' AND friend='$friend'";
        if ($conn->query($sql) === FALSE){ 
            echo "Error: " . $sql . "<br>" . $conn->error; 
        } else {
            echo 'success';
        }
    } else if (!empty($_POST["checker"])){
        $friend = $_POST["friend"];
        $sql = "SELECT * FROM locks WHERE user='$user' AND friend='$friend'";
        $res = mysqli_query($conn, $sql);
        if (mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            echo $row["lockType"];
        } else {
            echo "none";
        }
    } else if (!empty($_POST["create"])){
        $friend = $_POST["friend"];
        $lockType = "";
        $lock = "";
        if (!empty($_POST["lockTxt"])){
            $lockType = "Text";
            $lock = $_POST["lockTxt"];
        } else if (!empty($_POST["lockPIN"])){
            $lockType = "PIN";
            $lock = $_POST["lockPIN"];
        } else if ($_COOKIE['gridCode'] != "0000000000000000000000000"){
            $lockType = "Grid";
            $lock = $_COOKIE['gridCode'];
        } else if ($_COOKIE['colorCode'] != "nnnnnn"){
            $lockType = "Color";
            $lock = $_COOKIE['colorCode'];
        } else if ($_COOKIE['slideCode'] != "000") {
            $lockType = "Slides";
            $lock = $_COOKIE['slideCode'];
        } else {
            echo "Enter a lock";
        }
        if ($lock != ""){
            $sql = "INSERT INTO locks (user,friend,lockType,pass) VALUES ('$user','$friend','$lockType','$lock')";
            if ($conn->query($sql) === FALSE){ 
                echo "Error: " . $sql . "<br>" . $conn->error; 
            } else {
                echo 'success';
            }
        }
    } else {
        $friend = $_POST["friend"];
        if ($_COOKIE['gridCode'] != "0000000000000000000000000"){
            $pass = $_COOKIE['gridCode'];
        } else if ($_COOKIE['colorCode'] != "nnnnnn"){
            $pass = $_COOKIE['colorCode'];
        } else if ($_COOKIE['slideCode'] != "000") {
            $pass = $_COOKIE['slideCode'];
        } else {
            $pass = $_POST["passLogin"];
        }
        $sql = "SELECT * FROM locks WHERE user='$user' AND friend='$friend'";
        $res = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($res);
        if ($pass == $row["pass"]){
            echo "correct";
        } else {
            echo "incorrect";
        }
    }
    $conn->close();
}

?>