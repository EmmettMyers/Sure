<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel = "stylesheet">
    <script src = "https://code.jquery.com/jquery-1.10.2.js"></script>
    <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/home.js"></script>
    <title>Sure Home</title>
</head>
<body>

<?php

include "ajax/connection.php";
session_start();

if (empty($_SESSION["user"])) echo '<script>window.location.href = "index.html";</script>';
$user = $_SESSION["user"];
$sql = "SELECT * FROM users WHERE username='$user'";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);
$friends = $row["friends"];
$redirect = $row["redirect"];

if($_SERVER['REQUEST_METHOD'] == "POST"){
    if (!empty($_POST["message"])){
        $receiver = $_COOKIE["receiver"];
        $chat = $_POST["message"];
        date_default_timezone_set('America/Denver');
        $time = date('m/d/Y h:i:s a', time());
        $sql = "INSERT INTO messages (sender,receiver,content,time) VALUES ('$user','$receiver','$chat','$time')";
        if ($conn->query($sql) === FALSE) { echo "Error: " . $sql . "<br>" . $conn->error; }
    }
    $conn->close();
}

?>

<script>
    function getFriends(){
        return <?php echo(json_encode($friends)); ?>;
    }
    function getRedirect(){
        return <?php echo(json_encode($redirect)); ?>;
    }
</script>

<div id="navBar" class="navbar bg-black">
    <div class="container-fluid">
        <div id="menuContainer" class="dropdown dropend">
            <button id="menu" type="button" class="btn btn-primary" data-bs-toggle="dropdown">
                <img id="menuLogo" src="https://www.freeiconspng.com/thumbs/menu-icon/menu-icon-24.png">
            </button>
            <ul id="menuDrop" class="dropdown-menu bg-black">
                <li><div class="dropdown-item" id="addFriendsMenu">Add Friends</div></li>
                <li><div class="dropdown-item" id="requestsMenu">Friend Requests &nbsp;<span class="badge bg-success"></span></div></li>
                <li><div class="dropdown-item" id="settingsMenu">Settings</div></li>
                <li><div class="dropdown-item" onclick="location.href='index.html'">Logout</div></li>
            </ul>
        </div>
        <ul id="friends" class="navbar-nav"></ul>
    </div>
</div>

<div id="addFriends">
    <div class="content">
        <label for="fRequest" class="h5 text-black ms-3 mt-2 fw-bold form-label">Type a username below to send a friend request:</label>
        <div class="h3 fw-bold exit">X</div>
        <div class="input-group">
            <input type="text" id="fRequest" class="form-control ms-3" placeholder="Username" name="fRequest">
            <input name="submit" type="submit" id="requestSend" value="Send" class="me-4 btn modalEnd">
        </div>
        <div id="sentMessage" class="fw-bold ps-3 pt-2"></div>
    </div>
</div>

<div id="requests">
    <div class="content">
        <p class="h5 text-black ms-3 mt-2 fw-bold requestTitle"></p>
        <div class="h3 fw-bold exit">X</div>
        <div id="insertRequests"></div>
    </div>
</div>

<div id="settings">
    <div class="content">
        <p class="h4 text-black ms-3 mt-2 fw-bold">Settings</p>
        <div class="h3 fw-bold exit">X</div>
        <div id="picArea">
            <img id="profilePic" class="rounded-circle" src="">
            <div class="ms-3 mt-2">
                <p id="changeTxt" class="mb-0 mt-1 text-black fw-bold">Change profile picture:</p>
                <input type="text" id="imgForm" class="mt-1 form-control" placeholder="Image Address">
            </div>
            <div class="rounded-pill" id="tryAddress">Try Image</div>
            <div class="rounded-pill" id="submitPic">Submit Image</div>
        </div>
        <div id="redirectArea">
            <p class="mt-4 ms-4 mb-2 text-black fw-bold">Change double-click redirect URL:</p>
            <div class="input-group">
                <input type="text" id="redirect" class="form-control ms-3" placeholder="URL">
                <input name="submit" type="submit" id="redirectSend" value="Send" class="me-4 btn modalEnd">
            </div>
        </div>
    </div>
</div>

<div id="c0" class="chatBox" draggable="true"><div class="top"><div class="h4 chatName"></div></div><div class="body"></div></div>
<div id="c1" class="chatBox" draggable="true"><div class="top"><div class="h4 chatName"></div></div><div class="body"></div></div>
<div id="c2" class="chatBox" draggable="true"><div class="top"><div class="h4 chatName"></div></div><div class="body"></div></div>
<div id="c3" class="chatBox" draggable="true"><div class="top"><div class="h4 chatName"></div></div><div class="body"></div></div>
<div id="c4" class="chatBox" draggable="true"><div class="top"><div class="h4 chatName"></div></div><div class="body"></div></div>
<div id="c5" class="chatBox" draggable="true"><div class="top"><div class="h4 chatName"></div></div><div class="body"></div></div>

<div class="d-none" id="chatBoxTemplate">
    <img class="settingsLogo" src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/58/Ic_settings_48px.svg/2048px-Ic_settings_48px.svg.png">
    <img class="lockLogo" src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9e/Black_Lock.svg/1200px-Black_Lock.svg.png">
    <img class="windowLogo" src="http://findicons.com/files/icons/2315/default_icon/256/open_in_new_window.png">
    <div class="h2 fw-bold close">X</div>
    <div class="w-100 chatArea"></div>
    <div class="input-group mb-3 textBox">
        <input type="text" name="message" class="form-control message">
        <button class="btn chatSend" type="submit">Send</button>
    </div>
</div>

</body>
</html>