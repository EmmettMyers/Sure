<?php

include "connection.php";
session_start();

if($_SERVER['REQUEST_METHOD'] == "POST"){
    if (!empty($_POST["delete"])){
        $user = $_SESSION["user"];
        $sql = "DELETE FROM users WHERE username='$user';
                DELETE FROM messages WHERE sender='$user';
                DELETE FROM messages WHERE receiver='$user';
                DELETE FROM locks WHERE user='$user'; ";
        if ($conn->multi_query($sql) === FALSE) { echo "Error: " . $sql . "<br>" . $conn->error; }
        $query = "SELECT * FROM users";
        $result = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
            $friend = $row[0];
            if(strpos($row[6], $user) > -1){
                $newFriends = substr($row[6],0,strpos($row[6],$user)) . substr($row[6],strpos($row[6],$user)+strlen($user)+1);
                $sql = "UPDATE users SET friends='$newFriends' WHERE username='$friend'";
                if ($conn->query($sql) === FALSE) { echo "Error: " . $sql . "<br>" . $conn->error; }
            } else if (strpos($row[7], $user) > -1){
                $newRequests = substr($row[7],0,strpos($row[7],$user)) . substr($row[7],strpos($row[7],$user)+strlen($user)+1);
                $sql = "UPDATE users SET requests='$newRequests' WHERE username='$friend'";
                if ($conn->query($sql) === FALSE) { echo "Error: " . $sql . "<br>" . $conn->error; }
            }
        }
    } else if (!empty($_POST["username"]) && !empty($_POST["email"])){
        $user = test_input($_POST["username"]);
        $email = test_input($_POST["email"]);
        $sql_u = "SELECT * FROM users WHERE username='$user'";
        $sql_e = "SELECT * FROM users WHERE email='$email'";
        $res_u = mysqli_query($conn, $sql_u);
        $res_e = mysqli_query($conn, $sql_e);
        if(mysqli_num_rows($res_u) > 0){
            echo "That username is already taken";
        } else if (mysqli_num_rows($res_e) > 0) {
            echo "That email is already taken";
        } else {
            $passType = "";
            $pass = "";
            if (!empty($_POST["passTxt"])){
                $passType = "Text";
                $pass = test_input($_POST["passTxt"]);
            } else if (!empty($_POST["passPIN"])){
                $passType = "PIN";
                $pass = test_input($_POST["passPIN"]);
            } else if ($_COOKIE['gridCode'] != "0000000000000000000000000"){
                $passType = "Grid";
                $pass = test_input($_COOKIE['gridCode']);
            } else if ($_COOKIE['colorCode'] != "nnnnnn"){
                $passType = "Color";
                $pass = test_input($_COOKIE['colorCode']);
            } else if ($_COOKIE['slideCode'] != "000") {
                $passType = "Slides";
                $pass = test_input($_COOKIE['slideCode']);
            } else {
                echo "Enter a password";
            }
            if ($pass != ""){
                $image = "https://i0.wp.com/researchictafrica.net/wp/wp-content/uploads/2016/10/default-profile-pic.jpg?fit=300%2C300&ssl=1";
                $sql = "INSERT INTO users (username,email,passwordType,password,picture,redirect,friends,requests) VALUES ('$user','$email','$passType','$pass','$image','https://www.google.com/',';',';')";
                if ($conn->query($sql) === FALSE){ 
                    echo "Error: " . $sql . "<br>" . $conn->error; 
                } else {
                    echo 'success';
                }
            }
        }
    }
    else if (!empty($_POST["username"])){
        echo "Email not entered";
    } else if (!empty($_POST["email"])){
        echo "Username not entered";
    } else {
        echo "Username and email not entered";
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