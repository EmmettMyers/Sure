<?php

include "ajax/connection.php";
session_start();

if (empty($_SESSION["user"])) echo '<script>window.location.href = "index.php";</script>';

$user = $_SESSION["user"];
$sql = "SELECT * FROM users WHERE username='$user'";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);
$friends = $row["friends"];
$redirect = $row["redirect"];
$email = $row["email"];

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
    <link rel="stylesheet" href="scss/style.css">
    <script src="js/passwords.js"></script>
    <script src="js/home.js"></script>
    <title>Sure Home</title>
    <link rel="icon" href="https://www.pngall.com/wp-content/uploads/2/S-Letter-PNG-Image-HD.png">
    <style type="text/css">.disclaimer { display: none; }</style>
</head>
<body style="overflow: hidden;">

<script>
    function getUser(){
        return <?php echo(json_encode($user)); ?>;
    }
    function getEmail(){
        return <?php echo(json_encode($email)); ?>;
    }
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
                <li><div class="dropdown-item" onclick="location.href='index.php'">Logout</div></li>
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
            <div class="input-group" style="width: 675px;">
                <input type="text" id="redirect" class="form-control ms-3" placeholder="URL">
                <input name="submit" type="submit" id="redirectSend" value="Send" class="me-4 btn modalEnd">
            </div>
        </div>
        <div id="buttonArea">
            <div class="rounded-pill" id="resetLocks">Remove All Chat Locks</div>
            <div class="rounded-pill" id="deleteAcc">Delete Account</div>
        </div>
        <div id="emailSent" class="fw-bold ps-3 pt-2">Reset lock verification email sent to <?php echo $email?></div>
    </div>
</div>

<div id="lockSettings">
    <div class="lockSetContent">
        <div class="changeLock rounded-pill">Change Lock</div>
        <div class="deleteLock rounded-pill">Delete Lock</div>
    </div>
</div>

<div id="chatSettings">
    <div class="chatSetContent">
        <div class="deleteChats rounded-pill">Delete Chats</div>
        <div class="removeFriend rounded-pill">Remove Friend</div>
    </div>
</div>

<div id="c0" class="chatBox"><div class="top"><div class="h4 chatName"></div></div><div class="lockBox"></div><div class="body"></div></div>
<div id="c1" class="chatBox"><div class="top"><div class="h4 chatName"></div></div><div class="lockBox"></div><div class="body"></div></div>
<div id="c2" class="chatBox"><div class="top"><div class="h4 chatName"></div></div><div class="lockBox"></div><div class="body"></div></div>
<div id="c3" class="chatBox"><div class="top"><div class="h4 chatName"></div></div><div class="lockBox"></div><div class="body"></div></div>
<div id="c4" class="chatBox"><div class="top"><div class="h4 chatName"></div></div><div class="lockBox"></div><div class="body"></div></div>
<div id="c5" class="chatBox"><div class="top"><div class="h4 chatName"></div></div><div class="lockBox"></div><div class="body"></div></div>

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

<div class="d-none" id="createLockTemplate">
    <div class="pt-2 createLock">
        <div id="lockPasswords">
            <p id="lockError" class="text-center text-danger fw-bold"></p>
            <p class="text-center">Choose a lock type:</p>
            <div class="btn-group col-sm-12">
                <button type="button" class="btn">Text</button>
                <button type="button" class="btn">PIN</button>
                <button type="button" class="btn">Grid</button>
                <button type="button" class="btn">Color</button>
                <button type="button" class="btn">Slides</button>
            </div>
            <div id="Text" class="ms-4 me-4 mt-3 passCreate">
                <p class="text-center mt-3">Create a text lock with no restrictions</p>
                <div class="input-group">
                    <span style="background: lightgrey;" class="input-group-text">Lock</span>
                    <input type="text" id="pass" name="passTxt" class="form-control">
                </div>
            </div>
            <div id="PIN" class="ms-4 me-4 mt-3 passCreate">
                <p class="text-center mt-3">Create a PIN lock using only numbers</p>
                <div class="input-group">
                    <span style="background: lightgrey;" class="input-group-text">PIN</span>
                    <input type="text" id="pin" name="passPIN" class="form-control pin">
                </div>
            </div>
            <div id="Grid" class="mt-3 passCreate">
                <p class="text-center mt-3">Create a pattern lock by clicking the boxes</p>
            </div>
            <div id="Color" class="ms-5 me-5 passCreate">
                <p class="text-center mt-3">Create an order lock by changing the box colors</p>
                <div class="row justify-content-evenly colorBoxes"></div>
                <div class="row justify-content-evenly mt-1 colorPickers"></div>
            </div>
            <div id="Slides" class="ms-4 passCreate">
                <p class="text-center mt-3">Create a triple value lock by moving the sliders</p>
                <input type="range" class="slider" value="0" min="0" max="99" 
                oninput="document.getElementById('val1').innerHTML = this.value"/>
                <label id="val1">0</label>
                <input type="range" class="slider" value="0" min="0" max="99" 
                oninput="document.getElementById('val2').innerHTML = this.value"/>
                <label id="val2">0</label>
                <input type="range" class="slider" value="0" min="0" max="99" 
                oninput="document.getElementById('val3').innerHTML = this.value"/>
                <label id="val3">0</label>
            </div>
            <div class="text-center">
                <input id="lockSubmit" type='submit' class='btn mt-3 mb-5 modalEnd' value='Submit'>
            </div>
        </div>
    </div>
</div>

<div id="lock">
    <div class="lockLogin">
        <p class="text-center text-white fw-bold h3 mt-4 mb-4">This chat is locked.</p>
        <div style="display: none;" class='input-group mt-4 loginTxt log'>
            <span class='ms-3 input-group-text fw-bold text-white bg-secondary userTxt'>&#128274;</span>
            <input type='text' class='form-control text passLogin' placeholder=''>
            <input type='submit' class='me-3 btn modalEnd' value='Go'>
        </div>
        <div style="display: none; padding: 0; background: none;" class="border-0 text-center logGrid log"></div>
        <div style="display: none; padding: 0; background: none;" class="border-0 text-center mt-3 mb-2 logColors log">
            <div class="row justify-content-evenly logColorBoxes"></div>
            <div class="row justify-content-evenly mt-1 logColorPickers"></div>
        </div>
        <div style="display: none; padding: 0; background: none;" class="border-0 text-center mt-3 mb-2 logSlides log">
            <input type="range" class="s1 slider" value="0" min="0" max="99"><label class="text-white val1">0</label>
            <input type="range" class="s2 slider" value="0" min="0" max="99"><label class="text-white val2">0</label>
            <input type="range" class="s3 slider" value="0" min="0" max="99"><label class="text-white val3">0</label><br>
        </div>
    </div>
</div>

<script type="text/javascript"src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
<script type="text/javascript">
   (function(){
      emailjs.init("kKFrgWjpc5Zvz1uTC");
   })();
</script>

</body>
</html>