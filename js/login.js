var script = document.createElement('script');
script.src = 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js';
document.getElementsByTagName('head')[0].appendChild(script);
if (window.history.replaceState) { window.history.replaceState( null, null, window.location.href ); }

$(document).ready(function(){

    $(document).on('click', '#accSubmit', function(){
        var user = $("#username").val();
        var email = $("#email").val();
        if (user.includes(";") || user.length > 50){
            $(".accError").html("Invalid username");
        } else if (!(email.includes("@") && email.includes(".") && email.lastIndexOf(".")>email.lastIndexOf("@"))){
            $(".accError").html("Invalid email address");
        } else {
            var pass = $("#pass").val();
            var pin = $("#pin").val();
            $.ajax({ method: "POST", url: "ajax/signup.php", 
            data: {username: user, email: email, passTxt: pass, passPIN: pin } }) 
            .done(function(msg){
                resetCookies();
                if (msg == "success"){
                    emptyLoginInputs();
                    window.location.href = "userCreated.php";
                } else {
                    $(".accError").html(msg);
                }
            });
        }
    });

    $(document).on('click', '#sendCode', function(){
        var email = $("#forgotEmail").val();
        var code = "";
        for (var x = 0; x < 6; x++) code += Math.floor(Math.random() * 10);
        $.ajax({ method: "POST", url: "ajax/forgotPass.php", data: {code:code, email:email} })
        .done(function(msg){
            if (msg == "No accounts exist with that email"){
                $("#forgotError").html(msg);
            } else {
                $("#forgotError").html("");
                var templateParams = {
                    email: email,
                    user: msg,
                    code: code
                };
                emailjs.send('service_4d1p0wc', 'template_izljlum', templateParams);
            }
        });
    });

    $(document).on('click', '#codeEnter', function(){
        var enteredCode = $("#codeForm").val();
        $.ajax({ method: "POST", url: "ajax/forgotPass.php", data: {enteredCode:enteredCode} })
        .done(function(msg){
            if (msg == "success"){
                emptyLoginInputs();
                $("#forgotError").html("");
                $("#page1").css("display","none");
                $("#page2").css("display","block");
                var passScreen = $("#accPasswords").html();
                $("#accPasswords").empty();
                $("#forgotPasswords").append(passScreen);
            } else {
                $("#forgotError").html("Incorrect code");
            }
        });
    });

    $(document).on('click', '#newPassEnter', function(){
        var pass = $("#pass").val();
        var pin = $("#pin").val();
        $.ajax({ method: "POST", url: "ajax/forgotPass.php", 
        data: {passTxt: pass, passPIN: pin } }) 
        .done(function(msg){
            resetCookies();
            if (msg == "success"){
                var passScreen = $("#forgotPasswords").html();
                $("#forgotPasswords").empty();
                $("#accPasswords").append(passScreen);
                window.location.href = "userCreated.php";
            } else {
                $("#newPassError").html(msg);
            }
        });
    });

    $(document).on('click', '.btn-close, .loginBottom', function(){
        emptyLoginInputs();
    });

    $(document).on('click', '#userSubmit', function(){
        emptyPassInputs("");
        resetCookies();
        $(".error").html("");
        var user = $("#userLogin").val();
        if (user == "") $("#userLogin").attr("placeholder", "Username");
        else {
            $.ajax({ method: "POST", url: "ajax/login.php", 
            data: { userLogin: user } }) 
            .done(function(msg){
                if (!msg.includes("not")){
                    $("#userLogin").attr({"placeholder":"", "value":user});
                    if (msg == "Text" || msg == "PIN"){
                        $("#loginTxt").toggle();
                        $("#passLogin").attr("placeholder", msg);
                    } else {
                        showLogin(msg, "");
                    }
                } else {
                    $(".error").html(msg);
                }
            });
        } 
    });

    $(document).on('click', '.log .modalEnd', function(){
        var user = $("#userLogin").val();
        var pass = $("#passLogin").val();
        $.ajax({ method: "POST", url: "ajax/login.php", 
        data: { userLogin: user, passLogin: pass } }) 
        .done(function(msg){
            resetCookies();
            if (msg == "home.php"){
                window.location.href = msg;
            } else {
                $(".error").html(msg);
                if (msg.includes("not")) emptyPassInputs("");
            }
        });
    });

});

function emptyLoginInputs(){
    $("#forgotError").html("");
    $("#newPassError").html("");
    $(".accError").html("");
    $(".modal :text").val("");
}