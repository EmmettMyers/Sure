<?php

include "connection.php";
session_start();
session_unset();

if($_SERVER['REQUEST_METHOD'] == "POST"){
    if (!empty($_POST["passLogin"])||$_COOKIE['slideCode']!="000"||$_COOKIE['colorCode']!="nnnnnn"||$_COOKIE['gridCode']!="0000000000000000000000000"){
        $user = test_input($_POST["userLogin"]);
        if ($_COOKIE['gridCode'] != "0000000000000000000000000"){
            $pass = test_input($_COOKIE['gridCode']);
        } else if ($_COOKIE['colorCode'] != "nnnnnn"){
            $pass = test_input($_COOKIE['colorCode']);
        } else if ($_COOKIE['slideCode'] != "000") {
            $pass = test_input($_COOKIE['slideCode']);
        } else {
            $pass = test_input($_POST["passLogin"]);
        }
        $sql_u = "SELECT * FROM users WHERE username='$user'";
        $res_u = mysqli_query($conn, $sql_u);
        if (mysqli_num_rows($res_u) == 0) {
            echo "User not found";
        } else {
            $sql = "SELECT * FROM users WHERE username='$user' AND password='$pass'";
            $res = mysqli_query($conn, $sql);
            if (mysqli_num_rows($res) > 0) {
                $_SESSION["user"] = $user;
                echo "home.php";
            } else {
                echo "Incorrect password";
            }
        }
    } else if (!empty($_POST["userLogin"])){
        $user = test_input($_POST["userLogin"]);
        $sql_u = "SELECT * FROM users WHERE username='$user'";
        $res_u = mysqli_query($conn, $sql_u);
        if (mysqli_num_rows($res_u) > 0) {
            $row = mysqli_fetch_assoc($res_u);
            echo $row["passwordType"];
        } else {
            echo "User not found";
        }
    }
    $conn->close();
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>