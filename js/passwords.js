var script = document.createElement('script');
script.src = 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js';
document.getElementsByTagName('head')[0].appendChild(script);
if (window.history.replaceState) { window.history.replaceState( null, null, window.location.href ); }

var cID = 0; // id of color box selected by user
var grid = [[0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0]]; // String value of grid passwords
var colorCode = "nnnnnn"; // String value of color passwords

$(document).ready(function(){
            
            
    // creates color boxes and grid boxes
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
        $(".section").remove();
        $(this).addClass('active');
        $("#" + $(this).text()).toggle();
        var box = "#"+$($(this).parent().parent().parent().parent().parent()).attr("id");
        if ($("#createLockTemplate").html() == ""){
            $(box).css("height",$(box+" .createLock").height()+48);
        }
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
        cID = $(this).attr('id');
        var colors = ['red','orange','gold','green','blue','purple'];
        for (var color of colors) $("#p"+cID).append("<div style='background:"+color+";' class='section'></div>");
        var box = "#"+$($(this).parent().parent().parent().parent().parent().parent()).attr("id");
        if ($("#createLockTemplate").html() == ""){
            $(box).css("height",$(box+" .createLock").height()+48);
        }
    });

    $(document).on('click', '.section', function(){ 
        var box = "#"+$($(this).parent().parent().parent().parent().parent().parent().parent()).attr("id");
        $(".section").remove();
        if ($("#createLockTemplate").html() == ""){
            $(box).css("height",$(box+" .createLock").height()+48);
        }
        var colorLetter = "";
        ($(this).css("background")=='gold') ? colorLetter = "y" : colorLetter = $(this).css("background").substring(0,1);
        var letterSpot = (cID.substring(1,2))-1;
        colorCode = colorCode.substring(0,letterSpot) + colorLetter + colorCode.substring(letterSpot+1);
        document.cookie="colorCode=" + colorCode;
        $("#"+cID).css("background", $(this).css("background"));
    });

    $(document).on('input', '.slider', function(){ 
        $(".val"+$(this).attr("class").substring(1,2)).html($(this).val());
        var slideValues = "";
        var sliders = document.getElementsByClassName("slider");
        if ($(this).parent().attr("class") == "passCreate"){
            for(var x = 3; x < 6; x++) slideValues += sliders[x].value;
        } else {
            for(var x = 0; x < 3; x++) slideValues += sliders[x].value;
        }
        document.cookie="slideCode="+slideValues;
    });

    $(document).on('keyup', '.pin', function(){ 
        this.value = this.value.replace(/[^0-9\.]/g,'');
    });

});

// resets passwords
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

// shows login type based on account
function showLogin(type, box){
    if (type == "Grid"){
        $(box+" .logGrid").css("display", "block");
        for (var x = 0; x < 5; x++) $(box+" .logGrid").append("<div id='"+x+"' class='row justify-content-center'></div>");
        for (var x = 0; x < 5; x++) $(box+" .logGrid .row").append("<div id='"+x+"' class='box' style='border: white 2px solid;'></div>");
        $(box+" .logGrid").append("<input name='submit' type='submit' class='btn mt-2 modalEnd' value='Go'>");
    }
    else if (type == "Color"){
        $(box+" .logColors").css("display", "block");
        for (var x = 1; x <= 6; x++){ 
            $(box+" .logColorBoxes").append("<div id='c"+x+"' class='colorBox'></div>");
            $(box+" .logColorPickers").append("<div id='pc"+x+"' class='colorPick'></div>");
        }
        $(box+" .logColors").append("<input name='submit' type='submit' class='btn mt-2 modalEnd' value='Go'>");
    }
    else {
        $(box+" .logSlides").css("display", "block");
        $(box+" .logSlides").append("<input name='submit' type='submit' class='btn mt-2 modalEnd' value='Go'>");
    }
}

function emptyPassInputs(box){
    $(box+" .log").css("display", "none");
    $(box+" .logGrid").empty();
    $(box+" .logColorBoxes").empty();
    $(box+" .logColorPickers").empty();
    $(box+" .logColors .modalEnd").remove();
    $(box+" .logSlides .modalEnd").remove();
}
