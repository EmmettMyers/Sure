<?php

include "connection.php";
session_start();

if (!empty($_POST["enteredCode"])){
    if ($_POST["enteredCode"] == $_SESSION["code"]){
        echo "success";
    } else {
        echo "fail" . $_POST["enteredCode"] . $_SESSION["code"];
    }
} else if (!empty($_POST["code"])){
    $email = $_POST["email"];
    $sql_e = "SELECT * FROM users WHERE email='$email'";
    $res_e = mysqli_query($conn, $sql_e);
    if(mysqli_num_rows($res_e) > 0){
        $_SESSION["email"] = $email;
        $_SESSION["code"] = $_POST["code"];
        $sql = "SELECT * FROM users WHERE email='$email'";
        $res = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($res);
        echo $row['username'];
    } else {
        echo "No accounts exist with that email";
    }
} else {
    $email = $_SESSION["email"];
    $passType = "";
    $pass = "";
    if (!empty($_POST["passTxt"])){
        $passType = "Text";
        $pass = $_POST["passTxt"];
    } else if (!empty($_POST["passPIN"])){
        $passType = "PIN";
        $pass = $_POST["passPIN"];
    } else if ($_COOKIE['gridCode'] != "0000000000000000000000000"){
        $passType = "Grid";
        $pass = $_COOKIE['gridCode'];
    } else if ($_COOKIE['colorCode'] != "nnnnnn"){
        $passType = "Color";
        $pass = $_COOKIE['colorCode'];
    } else if ($_COOKIE['slideCode'] != "000") {
        $passType = "Slides";
        $pass = $_COOKIE['slideCode'];
    } else {
        echo "Enter a password";
    }
    if ($pass != ""){
        $sql = "UPDATE users SET passwordType='$passType', password='$pass' WHERE email='$email'";
        if ($conn->query($sql) === FALSE){ 
            echo "Error: " . $sql . "<br>" . $conn->error; 
        } else {
            echo 'success';
        }
    }
}

?>
