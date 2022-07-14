var script = document.createElement('script');
script.src = 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js';
document.getElementsByTagName('head')[0].appendChild(script);
if (window.history.replaceState) { window.history.replaceState( null, null, window.location.href ); }

var id = 0; // which color box is selected by user
var grid = [[0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0]];
var colorCode = "nnnnnn";

$(document).ready(function(){

    for (var x = 0; x < 5; x++) $("#Grid").append("<div id='"+x+"' class='row justify-content-center'></div>");
    for (var x = 0; x < 5; x++) $("#Grid .row").append("<div id='"+x+"' class='box' style='border: white 2px solid;'></div>");
    for (var x = 1; x <= 6; x++){ 
        $(".colorBoxes").append("<div id='c"+x+"' class='colorBox'></div>");
        $(".colorPickers").append("<div id='pc"+x+"' class='colorPick'></div>");
    }

    $(document).on('click', '.btn-group .btn', function(){
        resetCookies();
        $('#pass, #pin').val("");
        $(".passCreate").css("display", "none");
        $(".btn-group .btn").removeClass('active');
        $(this).addClass('active');
        $("#" + $(this).text()).toggle();
    });

    $(document).on('click', '.box', function(){
        if ($(this).hasClass('active')){
            $(this).removeClass('active');
            grid[$(this.parentNode).attr('id')][$(this).attr('id')] = 0;
        } else {
            $(this).addClass('active');
            grid[$(this.parentNode).attr('id')][$(this).attr('id')] = 1;
        }
        setGridCode();
    });

    $(document).on('click', '.colorBox', function(){
        $(".section").remove();
        id = $(this).attr('id');
        var colors = ['red','orange','gold','green','blue','purple'];
        for (var color of colors) $("#p"+id).append("<div style='background:"+color+";' class='section'></div>");
    });

    $(document).on('click', '.section', function(){ 
        $(".section").remove();
        var colorLetter = "";
        ($(this).css("background")=='gold') ? colorLetter = "y" : colorLetter = $(this).css("background").substring(0,1);
        var letterSpot = (id.substring(1,2))-1;
        colorCode = colorCode.substring(0,letterSpot) + colorLetter + colorCode.substring(letterSpot+1);
        document.cookie="colorCode=" + colorCode;
        $("#"+id).css("background", $(this).css("background"));
    });

    $(".slider").mousemove(function(){
        var slideValues = "";
        var sliders = document.getElementsByClassName("slider");
        for(var x = 0; x < 3; x++) slideValues += sliders[x].value;
        document.cookie="slideCode="+slideValues;
    });

    jQuery('.pin').keyup(function () { 
        this.value = this.value.replace(/[^0-9\.]/g,'');
    });

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
        emptyPassInputs();
        resetCookies();
        $(".error").html("");
        var user = $("#userLogin").val();
        $.ajax({ method: "POST", url: "ajax/login.php", 
        data: { userLogin: user } }) 
        .done(function(msg){
            if (!msg.includes("not")){
                $("#userLogin").attr({"placeholder":"", "value":user});
                if (msg == "Text" || msg == "PIN"){
                    $("#loginTxt").toggle();
                    $("#passLogin").attr("placeholder", msg);
                } else {
                    showLogin(msg);
                }
            } else {
                $(".error").html(msg);
            }
        });
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
                if (msg.includes("not")) emptyPassInputs();
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

function emptyPassInputs(){
    $(".log").css("display", "none");
    $("#logGrid").empty();
    $(".logColorBoxes").empty();
    $(".logColorPickers").empty();
    $("#logColors .modalEnd").remove();
    $("#logSlides .modalEnd").remove();
}

function resetCookies(){
    document.cookie="gridCode=0000000000000000000000000";
    $(".box").removeClass('active');
    grid = [[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0]];
    document.cookie="colorCode=nnnnnn";
    $(".colorBox").css("background", "lightgrey");
    document.cookie="slideCode=000";
    $(".slider").val(0);
    $("label").html("0");
}

function setGridCode(){
    var gridString = "";
    for(var x = 0; x < 5; x++)
        for(var y = 0; y < 5; y++)
            gridString += grid[x][y];
    document.cookie="gridCode=" + gridString;
}

function showLogin(type){ jQuery(function($){
    if (type == "Grid"){
        $("#logGrid").css("display", "block");
        for (var x = 0; x < 5; x++) $("#logGrid").append("<div id='"+x+"' class='row justify-content-center'></div>");
        for (var x = 0; x < 5; x++) $("#logGrid .row").append("<div id='"+x+"' class='box' style='border: white 2px solid;'></div>");
        $("#logGrid").append("<input name='submit' type='submit' class='btn mt-2 modalEnd' value='Go'>");
    }
    else if (type == "Color"){
        $("#logColors").css("display", "block");
        for (var x = 1; x <= 6; x++){ 
            $(".logColorBoxes").append("<div id='c"+x+"' class='colorBox'></div>");
            $(".logColorPickers").append("<div id='pc"+x+"' class='colorPick'></div>");
        }
        $("#logColors").append("<input name='submit' type='submit' class='btn mt-2 modalEnd' value='Go'>");
    }
    else {
        $("#logSlides").css("display", "block");
        $("#logSlides").append("<input name='submit' type='submit' class='btn mt-2 modalEnd' value='Go'>");
    }
});}