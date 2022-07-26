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
    <script src="js/login.js" defer></script>
    <script src="js/passwords.js" defer></script>
    <link rel="icon" href="https://www.pngall.com/wp-content/uploads/2/S-Letter-PNG-Image-HD.png">
    <style type="text/css">.disclaimer { display: none; }</style>
    <title>Sure Login</title>
</head>

<body>

<div class="container">
    <p id="homeTitle" class="text-center mt-5 mb-1 fw-bold text-black">Sure</p>
    <p class="text-center text-danger fw-bold error"></p>
    <div class='input-group mx-auto' id="usernameInput">
        <span class='input-group-text fw-bold text-white bg-black userTxt'>@</span>
        <input type='text' id='userLogin' class='form-control text userTxt' placeholder='Username' value=''>
        <input id='userSubmit' type='submit' class='btn modalEnd' value='Enter'>
    </div>
    <div style="display: none; width: 700px;" id='loginTxt' class='input-group mx-auto mt-3 log'>
        <span class='input-group-text fw-bold text-white bg-black userTxt'>&#128274;</span>
        <input type='text' id='passLogin' class='form-control text' placeholder=''>
        <input type='submit' class='btn modalEnd' value='Go'>
    </div>
    <div style="display: none;" class="text-center mt-3 mb-2 logGrid log"></div>
    <div style="display: none;" class="text-center mt-3 mb-2 logColors log">
        <div class="row justify-content-evenly logColorBoxes"></div>
        <div class="row justify-content-evenly mt-1 logColorPickers"></div>
    </div>
    <div style="display: none;" class="text-center mt-3 mb-2 logSlides log">
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
                        <input type="range" class="s1 slider" value="0" min="0" max="99"><label class="val1">0</label>
                        <input type="range" class="s2 slider" value="0" min="0" max="99"><label class="val2">0</label>
                        <input type="range" class="s3 slider" value="0" min="0" max="99"><label class="val3">0</label>
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