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
    <script src="js/login.js"></script>
    <title>Sure Login</title>
</head>

<body>

<?php

include "ajax/connection.php";
session_start();
session_unset();
$showPass = false;
$error = "";

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
        echo '<script>resetCookies();</script>';
        $sql_u = "SELECT * FROM users WHERE username='$user' AND password='$pass'";
        $res_u = mysqli_query($conn, $sql_u);
        if (mysqli_num_rows($res_u) > 0) {
            $_SESSION["user"] = $user;
            echo '<script>window.location.href = "home.php";</script>';
        } else {
            $showPass = true;
            $error = "Incorrect password";
        }
    } else if (!empty($_POST["userLogin"])){
        $user = test_input($_POST["userLogin"]);
        $sql_u = "SELECT * FROM users WHERE username='$user'";
        $res_u = mysqli_query($conn, $sql_u);
        if (mysqli_num_rows($res_u) > 0) {
            $row = mysqli_fetch_assoc($res_u);
            $showPass = true;
            $_SESSION["passType"] = $row["passwordType"];
        } else {
            $error = "User not found";
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

<div class="container">
    <p id="homeTitle" class="text-center mt-5 mb-1 fw-bold text-black">Sure</p>
    <p class="text-center text-danger fw-bold error"><?php echo $error;?></p>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <?php
            if ($showPass){
                echo "<div class='input-group sm-5'>
                <span class='input-group-text fw-bold text-white bg-black userTxt'>@</span>
                <input type='text' name='userLogin' class='form-control text userTxt' value='$user'>
                <input id='userSubmit' type='submit' class='btn modalEnd' value='Enter'>
                </div>";
                if ($_SESSION["passType"] == "Text" || $_SESSION["passType"] == "PIN"){
                    $placeHold = ($_SESSION["passType"] == "PIN") ? "PIN" : "Password";
                    echo "<div id='loginTxt' class='input-group sm-5 mt-3 passLogin'>
                    <span class='input-group-text fw-bold text-white bg-black userTxt'>&#128274;</span>
                    <input type='text' name='passLogin' class='form-control text' placeholder='$placeHold'>
                    <input name='submit' type='submit' class='btn modalEnd' value='Go'>
                    </div>";
                } else if ($_SESSION["passType"] == "Grid"){
                    echo "<script>showLogin('grid');</script>";
                } else if ($_SESSION["passType"] == "Color"){
                    echo "<script>showLogin('color');</script>";
                } else {
                    echo "<script>showLogin('slides');</script>";
                }
            } else {
                echo "<div class='input-group sm-5'>
                <span class='input-group-text fw-bold text-white bg-black userTxt'>@</span>
                <input type='text' name='userLogin' class='form-control text userTxt' placeholder='Username'>
                <input id='userSubmit' type='submit' class='btn modalEnd' value='Enter'>
                </div>";
            }
        ?>
        <div style="display: none;" class="text-center mt-3 mb-2 logGrid"></div>
        <div style="display: none;" class="text-center mt-3 mb-2 logColors">
            <div class="row justify-content-evenly logColorBoxes"></div>
            <div class="row justify-content-evenly mt-1 logColorPickers"></div>
        </div>
        <div style="display: none;" class="text-center mt-3 mb-2 logSlides">
            <input type="range" class="slider" value="0" min="0" max="99" 
            oninput="document.getElementById('logVal1').innerHTML = this.value"/>
            <label id="logVal1">0</label>
            <input type="range" class="slider" value="0" min="0" max="99" 
            oninput="document.getElementById('logVal2').innerHTML = this.value"/>
            <label id="logVal2">0</label>
            <input type="range" class="slider" value="0" min="0" max="99" 
            oninput="document.getElementById('logVal3').innerHTML = this.value"/>
            <label id="logVal3">0</label><br>
        </div>
    </form>
    <ul class="list-inline p-2 d-flex justify-content-center">
        <li class="loginBottom list-inline-item h6" data-bs-toggle="modal" data-bs-target="#createAcc">Create Account</li>
        <li style="color: rgb(112,112,112);" class="list-inline-item h6 ms-3 me-4">&#9679;</li>
        <li class="loginBottom list-inline-item h6" data-bs-toggle="modal" data-bs-target="#forgotPass">Forgot Password?</li>
    </ul>
</div>

<div id="createAcc" class="modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fw-bold h2 ms-1">Create Account</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">    
                <p class="text-center text-danger fw-bold accError"></p>
                <div class="input-group mb-3">
                    <span style="background: lightgrey;" class="input-group-text">Username</span>
                    <input type="text" id="username" name="username" class="form-control">
                </div>
                <div class="input-group">
                    <span style="background: lightgrey;" class="input-group-text">Email</span>
                    <input type="text" id="email" name="email" class="form-control">
                </div>
                <div id="accPasswords">
                    <p class="text-center mt-3">Choose a password type:</p>
                    <div class="btn-group col-sm-12">
                        <button type="button" class="btn">Text</button>
                        <button type="button" class="btn">PIN</button>
                        <button type="button" class="btn">Grid</button>
                        <button type="button" class="btn">Color</button>
                        <button type="button" class="btn">Slides</button>
                    </div>
                    <div id="Text" class="mt-3 passCreate">
                        <p class="text-center mt-3">Create a text password with no restrictions</p>
                        <div class="input-group">
                            <span style="background: lightgrey;" class="input-group-text">Password</span>
                            <input type="text" id="pass" name="passTxt" class="form-control">
                        </div>
                    </div>
                    <div id="PIN" class="mt-3 passCreate">
                        <p class="text-center mt-3">Create a PIN password using only numbers</p>
                        <div class="input-group">
                            <span style="background: lightgrey;" class="input-group-text">PIN</span>
                            <input type="text" id="pin" name="passPIN" class="form-control pin">
                        </div>
                    </div>
                    <div id="Grid" class="mt-3 passCreate">
                        <p class="text-center mt-3">Create a pattern password by clicking the boxes</p>
                    </div>
                    <div id="Color" class="passCreate">
                        <p class="text-center mt-3">Create an order password by changing the box colors</p>
                        <div class="row justify-content-evenly colorBoxes"></div>
                        <div class="row justify-content-evenly mt-1 colorPickers"></div>
                    </div>
                    <div id="Slides" class="passCreate">
                        <p class="text-center mt-3">Create a triple value password by moving the sliders</p>
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
                </div>
            </div>
            <div class="modal-footer mt-2">
                <input name="submit" type="submit" id="accSubmit" class="btn modalEnd">
            </div>
        </div>
    </div>
</div>

<div id="forgotPass" class="modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div id="page1">
                <div class="modal-header">
                    <h4 class="modal-title fw-bold h2 ms-1">Forgot Password</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-center">Enter your email</p>
                    <div class="input-group mb-3">
                        <span style="background: lightgrey;" class="input-group-text">Email</span>
                        <input type="text" id="forgotEmail" class="form-control">
                    </div>
                    <p class="text-center">Click the button below to send a code to your email<br><span style="font-size: 12px;">(code may appear in spam mail)</span></p>
                    <button type="button" id="sendCode" class="btn-lg col-md-12 text-center modalEnd">Send code</button>
                    <p class="text-center mt-3">Enter the 6 digit code below</p>
                    <div class="input-group mb-3">
                        <span style="background: lightgrey;" class="input-group-text">Code</span>
                        <input type="text" id="codeForm" maxlength="6" min="0" max="6" required class="form-control">
                    </div>
                    <p id="forgotError" class="mb-0 mt-0 text-center text-danger fw-bold error"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="codeEnter" class="btn modalEnd">Enter</button>
                </div>
            </div>
            <div id="page2" style="display: none;">
                <div class="modal-header">
                    <h4 class="modal-title fw-bold h2 ms-1">Create New Password</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="newPassError" class="text-center text-danger fw-bold"></p>
                    <div id="forgotPasswords"></div>
                </div>
                <div class="mt-4 modal-footer">
                    <button type="button" id="newPassEnter" class="btn modalEnd">Submit</button>
                </div>
            </div>
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