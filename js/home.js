var script = document.createElement('script');
script.src = 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js';
document.getElementsByTagName('head')[0].appendChild(script);
if (window.history.replaceState) { window.history.replaceState( null, null, window.location.href ); }

var chatWindows = ["","","","","",""];
var changedRedirect = "";

$(document).ready(function(){

    var top = { level: 0, box: "" }; // the box on top of other boxes

    showFriendChats(getFriends());
    $(".chatBox").draggable({ handle: ".top" });

    // checks if there are any friend requests
    $.ajax({ method: "POST", url: "ajax/friendRequest.php"}) .done(function(msg){
        var requests = msg.substring(1);
        var numRequests = 0;
        while (requests.includes(";") && requests.length>1){
            numRequests++;
            requests = requests.substring(requests.indexOf(";")+1);
            requests = requests.substring(requests.indexOf(";")+1);
        }
        if (numRequests != 0) $(".badge").html(numRequests);
    });

    const boxes = document.querySelectorAll('.chatBox');
    boxes.forEach(box => { resize_box.observe(box); });

    // redirect on double click function
    $('body').dblclick(function(){
        (changedRedirect != "") ? window.location.href = changedRedirect : window.location.href = getRedirect();
    });

    $(document).on('mousedown', '.chatBox', function(){
        if (top.box != $(this).attr('id')) top.level++;
        top.box = $(this).attr('id');
        $(this).css("z-index", top.level);
    });

    // click on a friend chat circle, opens up chat box
    $(document).on('click', '.circle', function(){
        var friend = $(this).attr('id'); 
        document.cookie="receiver="+friend;
        $('.message').val('');
        if (chatWindows.indexOf(friend) > -1){
            var box = "#c"+chatWindows.indexOf(friend);
            $(this).removeClass('active');
            $(box).toggle();
            if ($(box+" .lockBox").html() != "" && $(box+" .lockBox").html().includes("createLock")){
                resetCookies();
                $(box+" .btn").removeClass('active');
                $(box+" .passCreate").css("display","none");
                $(box+" .lockBox").empty();
            }
            $(box).css({"height":"400px","width":"400px"});
            $(box+" .lockBox").empty();
            chatWindows[chatWindows.indexOf(friend)] = "";
        } else {
            setInterval(friend);
            $(this).addClass('active');
            var opening = chatWindows.indexOf("");
            if (opening == -1) return;
            var box = "#c"+opening;
            $(box).append($("#chatBoxTemplate").html());
            $(box+" .chatName").html(friend);
            $(box).toggle();
            var tDiff = Math.floor(Math.random() * $(window).height()/9);
            var rDiff = Math.floor(Math.random() * $(window).width()/11);
            var diffSign = Math.floor(Math.random() * 2);
            (diffSign == 0) ? tDiff *= -1 : rDiff *= -1;
            var top = ($(window).height()/5) + tDiff;
            var right = ($(window).width()/5) + rDiff;
            $(box).css({"top":top,"right":right});
            chatWindows[opening] = friend;
            setChats(friend);
            $.ajax({ method: "POST", url: "ajax/chatLocks.php",
            data: {friend: friend, checker:"isThereLock"} }).done(function(msg){
                if (msg != "none"){
                    $(box+" .textBox").css("display","none");
                    $(box+" .lockBox").append($("#lock").html());
                    $(box+" .lockLogo").css("pointer-events", "none");
                    $(box+" .settingsLogo").css("pointer-events", "none");
                    if (msg == "PIN" || msg == "Text"){
                        $(box+" .loginTxt").show();
                        $(box+" .passLogin").attr("placeholder", msg);
                        $(box).css("height","225px");
                    } else {
                        showLogin(msg, box);
                        if (msg == "Grid"){ 
                            $(box).css("height","450px");
                        } else if (msg == "Color"){
                            $(box).css("width","500px"); 
                            $(box).css("height","275px");
                        } else if (msg == "Slides"){
                            $(box).css("height","325px");
                            $(box).css("width","600px");
                        }
                    }
                }
                realTime(friend);
            });
        }
    });
    
    
    // closes chatbox
    $(document).on('click', '.close', function(){
        var friend = chatWindows[$(this).parent().attr('id').substring(1)];
        var box = "#c"+chatWindows.indexOf(friend);
        $("#"+friend).removeClass('active');
        $(box).toggle();
        if ($(box+" .lockBox").html() != "" && $(box+" .lockBox").html().includes("createLock")){
            resetCookies();
            $(box+" .btn").removeClass('active');
            $(box+" .passCreate").css("display","none");
            var lockBox = $(box+" .lockBox").html();
            $(box+" .lockBox").empty();
            $("#createLockTemplate").append(lockBox);
        }
        $(box).css({"height":"400px","width":"400px"});
        $(box+" .lockBox").empty();
        chatWindows[chatWindows.indexOf(friend)] = "";
    });

    // maximize chatbox
    $(document).on('click', '.windowLogo', function(){
        var id = $(this).parent().attr('id');
        if ($(this).width() == "25"){
            $("#"+id).css({"width":"400px","height":"400px","resize":"both","right":$(window).width()/5,"top":$(window).height()/5,"left":"auto"});
            $("#"+id+" .top").css("pointer-events", "auto");
            $("#"+id+" .windowLogo").css({"width":"30px","top":"8px","right":"36px"});
            $("#"+id+" .windowLogo").attr("src","http://findicons.com/files/icons/2315/default_icon/256/open_in_new_window.png");
        } else {      
            $("#"+id).css({"width":$(window).width()-150,"height":"100vh","margin-left":"150px","resize":"none","top":"0","left":"0"});
            $("#"+id+" .top").css("pointer-events", "none");
            $("#"+id+" .windowLogo").css({"width":"25px","top":"12px","right":"40px"});
            $("#"+id+" .windowLogo").attr("src","http://cdn.onlinewebfonts.com/svg/img_509774.png");
        }
    });

    // resize chatbox's chat area based on window width
    $(window).resize(function(){
        for (var x = 0; x < 6; x++)
            if ($("#c"+x+" .windowLogo").width() == "25")
                $("#c"+x).css("width", $(window).width()-150);
    });

    $(document).on('click', '.chatSend', function(){
        var friend = chatWindows[$(this).parent().parent().attr('id').substring(1)];
        var chat = $("#"+$(this).parent().parent().attr('id')+" .message").val();
        $.ajax({ method: "POST", url: "ajax/getChats.php", data: {message: chat} }) .done(function(){
            setChats(friend);
            $('.message').val('');
        });
    });

    $(document).on('click', '#requestSend', function(){
        var friend = $("#fRequest").val();
        $.ajax({ method: "POST", url: "ajax/friendRequest.php", data: {friend: friend} }) .done(function(msg){
            $("#sentMessage").css("display","block");
            $("#sentMessage").html(msg);
            if (msg.includes("sent to")){
                $("#sentMessage").css({"color":"green","background":"lime"});
            } else {
                $("#sentMessage").css({"color":"darkred","background":"red"});
            }
        });
    });


    $(document).on('click', '#addFriendsMenu, #addFriends .exit', function(){
        if ($("#requests").css("display") == "block") $("#requests").css("display","none");
        if ($("#settings").css("display") == "block") $("#settings").css("display","none");
        $("#addFriends").toggle();
        $("#fRequest").val("");
        $("#sentMessage").css("display","none");
    });

    $(document).on('click', '#requestsMenu, #requests .exit', function(){
        if ($("#addFriends").css("display")=="block") $("#addFriends").css("display","none");
        if ($("#settings").css("display") == "block") $("#settings").css("display","none");
        if ($("#requests").css("display") == "none"){
            $("#requests").css("display","block");
            setRequests();
        } else {
            $("#requests").css("display","none");
            $("#insertRequests").empty();
        }
    });

    // accept or decline friend request
    $(document).on('click', '.accept, .decline', function(){
        var friend = $(this).parent().attr('id').substring(3);
        var decision = "";
        ($(this).attr('class').includes("accept")) ? decision = "y" : decision = "n";
        $.ajax({ method: "POST", url: "ajax/friendRequest.php", data: {friend: friend,decision: decision} }) .done(function(){
            $.ajax({ method: "POST", url: "ajax/friendRequest.php"}) .done(function(msg){
                var requests = msg.substring(1);
                var numRequests = 0;
                while (requests.includes(";") && requests.length>1){
                    numRequests++;
                    requests = requests.substring(requests.indexOf(";")+1);
                }
                (numRequests == 0) ? $(".badge").html("") : $(".badge").html(numRequests);
                $("#insertRequests").empty();
                setRequests();
                if (decision == "y") {
                    $.ajax({ method: "POST", url: "ajax/friendRequest.php", data: {accept:"accept"} }) 
                    .done(function(friends){ showFriendChats(friends); });
                }
            });
        });
    });

    $(document).on('click', '#settingsMenu, #settings .exit', function(){
        if ($("#requests").css("display") == "block") $("#requests").css("display","none");
        if ($("#addFriends").css("display")=="block") $("#addFriends").css("display","none");
        $("#settings").toggle();
        $.ajax({ method: "POST", url: "ajax/profilePic.php" }) 
        .done(function(src){ $("#profilePic").attr("src", src); });
    });

    $(document).on('click', '#tryAddress', function(){
        var image = $("#imgForm").val();
        $("#profilePic").attr("src", image);
    });

    $(document).on('click', '#submitPic', function(){
        var image = $("#imgForm").val();
        $("#imgForm").val("");
        $.ajax({ method: "POST", url: "ajax/profilePic.php", data: {change:image} });
    });

    $("#profilePic").on("error", function(){
        $("#profilePic").attr("src", "imgError.png");
    });

    $(document).on('click', '#redirectSend', function(){
        var url = $("#redirect").val();
        if (!url.includes("https://www.")) url = "https://www." + url;
        changedRedirect = url;
        $("#redirect").val("");
        $.ajax({ method: "POST", url: "ajax/profilePic.php", data: {redirect:url} });
    });

    $(document).on('click', '.lockLogo', function(){
        var box = "#"+$($(this).parent()).attr("id");
        var friend = chatWindows[box.substring(2)];
        $.ajax({ method: "POST", url: "ajax/chatLocks.php",
        data: {friend: friend, checker:"isThereLock"} }).done(function(msg){
            if (msg != "none"){
                if ($(box+" .lockBox").html().includes("chatSet")){
                    $(box+" .lockBox").empty();
                    $(box+" .lockBox").append($("#lockSettings").html());
                } else if ($(box+" .lockBox").html() == ""){
                    $(box+" .lockBox").append($("#lockSettings").html());
                } else {
                    $(box+" .lockBox").empty();
                }
            } else {
                $(box+" .lockBox").empty();
                if ($("#createLockTemplate").html() == "" && $(box+" .lockBox").html() == ""){
                    var oldBox = "";
                    for (var x = 0; x < 5; x++)
                        if ($("#c"+x+" .lockBox").html() != "")
                            oldBox = "#c"+x;
                    var oldLockBox = $(oldBox+" .lockBox").html();
                    $(oldBox+" .lockBox").empty();
                    $("#createLockTemplate").append(oldLockBox);
                    $(oldBox+" .textBox").show();
                    $(oldBox).css({"height":"400px","width":"400px"});
                    $(box+" .windowLogo").css("pointer-events", "auto");
                }
                resetCookies();
                $(box+" .btn").removeClass('active');
                $(box+" .passCreate").css("display","none");
                if ($("#createLockTemplate").html() != ""){
                    var lockBox = $("#createLockTemplate").html();
                    $("#createLockTemplate").empty();
                    $(box+" .lockBox").append(lockBox);
                    $(box+" .textBox").css("display","none");
                    $(box).css({"height":$(box+" .createLock").height()+48,"width":"525px"});
                    $(box+" .windowLogo").css("pointer-events", "none");
                } else {
                    var lockBox = $(box+" .lockBox").html();
                    $(box+" .lockBox").empty();
                    $("#createLockTemplate").append(lockBox);
                    $(box+" .textBox").show();
                    $(box).css({"height":"400px","width":"400px"});
                    $(box+" .windowLogo").css("pointer-events", "auto");
                }
            }
        });
    });

    $(document).on('click', '#lockSubmit', function(){
        var box = "#"+($(this).parent().parent().parent().parent().parent()).attr('id');
        var friend = chatWindows[$($(this).parent().parent().parent().parent().parent()).attr('id').substring(1)];
        var pass = $("#pass").val();
        var pin = $("#pin").val();
        $.ajax({ method: "POST", url: "ajax/chatLocks.php",
        data: {friend: friend, lockTxt: pass, lockPIN: pin, create: "yes"} })
        .done(function(msg){
            if (msg != "success"){
                $("#lockError").html(msg);
            } else {
                $("#lockError").html("");
                var lockBox = $(box+" .lockBox").html();
                $(box+" .lockBox").empty();
                $("#createLockTemplate").append(lockBox);
                $(box+" .textBox").show();
                $(box).css({"height":"400px","width":"400px"});
                $(box+" .windowLogo").css("pointer-events", "auto");
            }
        });
    });

    $(document).on('click', '.log .modalEnd', function(){
        var box = "#"+$($(this).parent().parent().parent().parent()).attr('id');
        var friend = chatWindows[$($(this).parent().parent().parent().parent()).attr('id').substring(1)];
        var pass = $(".passLogin").val();
        $.ajax({ method: "POST", url: "ajax/chatLocks.php", 
        data: { friend: friend, passLogin: pass } }) 
        .done(function(msg){
            resetCookies();
            if (msg == "correct"){
                emptyPassInputs(box);
                $(box+" .lockBox").empty();
                $(box+" .textBox").show();
                $(box+" .lockLogo").css("pointer-events", "auto");
                $(box+" .settingsLogo").css("pointer-events", "auto");
                $(box).css({"height":"400px","width":"400px"});
            }
        });
    });

    $(document).on('click', '.changeLock, .deleteLock', function(){
        var action = $(this).attr('class').substring(0, $(this).attr('class').indexOf("L"));
        var box = "#"+$($(this).parent().parent().parent()).attr('id');
        var friend = chatWindows[$($(this).parent().parent().parent()).attr('id').substring(1)];
        $.ajax({ method: "POST", url: "ajax/chatLocks.php", 
        data: { friend: friend, action: action } }) .done(function(){
            $(box+" .lockBox").empty();
            if (action == "change"){
                if ($("#createLockTemplate").html() == ""){
                    var oldBox = "";
                    for (var x = 0; x < 5; x++)
                        if ($("#c"+x+" .lockBox").html() != "")
                            oldBox = "#c"+x;
                    var oldLockBox = $(oldBox+" .lockBox").html();
                    $(oldBox+" .lockBox").empty();
                    $("#createLockTemplate").append(oldLockBox);
                    $(oldBox+" .textBox").show();
                    $(oldBox).css({"height":"400px","width":"400px"});
                    $(box+" .windowLogo").css("pointer-events", "auto");
                }
                resetCookies();
                var lockBox = $("#createLockTemplate").html();
                $("#createLockTemplate").empty();
                $(box+" .lockBox").append(lockBox);
                $(box+" .btn").removeClass('active');
                $(box+" .passCreate").css("display","none");
                $(box+" .textBox").css("display","none");
                $(box).css({"height":$(box+" .createLock").height()+48,"width":"525px"});
                $(box+" .windowLogo").css("pointer-events", "none");
            }
        });
    });

    $(document).on('click', '.settingsLogo', function(){
        var box = "#"+$($(this).parent()).attr("id");
        if ($(box+" .lockBox").html().includes("createLock")){
            var lockBox = $(box+" .lockBox").html();
            $(box+" .lockBox").empty();
            $("#createLockTemplate").append(lockBox);
            $(box+" .textBox").show();
            $(box).css({"height":"400px","width":"400px"});
            $(box+" .windowLogo").css("pointer-events", "auto");
            $(box+" .lockBox").append($("#chatSettings").html());
        } else if ($(box+" .lockBox").html().includes("lockSet") || $(box+" .lockBox").html().includes("createLock") ){
            $(box+" .lockBox").empty();
            $(box+" .lockBox").append($("#chatSettings").html());
        } else if ($(box+" .lockBox").html() == ""){
            $(box+" .lockBox").append($("#chatSettings").html());
        } else {
            $(box+" .lockBox").empty();
        }
    });

    $(document).on('click', '.removeFriend', function(){
        var box = "#"+$($(this).parent().parent().parent()).attr('id');
        var friend = chatWindows[box.substring(2)];
        $.ajax({ method: "POST", url: "ajax/friendRequest.php", 
        data: { friend: friend, delete: "yes" } }) .done(function(msg){
            showFriendChats(msg);
            $(box).toggle();
            $(box).css({"height":"400px","width":"400px"});
            $(box+" .lockBox").empty();
            chatWindows[chatWindows.indexOf(friend)] = "";
        });
    });

    $(document).on('click', '.deleteChats', function(){
        var box = "#"+$($(this).parent().parent().parent()).attr('id');
        var friend = chatWindows[box.substring(2)];
        $.ajax({ method: "POST", url: "ajax/getChats.php", 
        data: { friend: friend } }) .done(function(){
            setChats(friend);
            $(box+" .lockBox").empty();
        });
    });

    $(document).on('click', '#resetLocks', function(){
        var user = getUser();
        var email = getEmail();
        var templateParams = {
            email: email,
            user: user
        };
        emailjs.send('service_4d1p0wc', 'template_ophfn24', templateParams);
        $("#emailSent").show();
        setTimeout(function() { $("#emailSent").fadeOut().empty(); }, 3000);
    });

    $(document).on('click', '#deleteAcc', function(){
        $.ajax({ method: "POST", url: "ajax/signup.php", 
        data: { delete: "yes" } }) .done(function(){
            window.location.href = "index.php";
        });
    });
    
    // on click of a chat you sent, shows edit and delete options
    $(document).on('click', '.myChat', function(){
        if ($(this).parent().html().includes("textOptions"))
            $(this).parent().find(".textOptions").remove();
        else 
            $(this).parent().prepend("<div class='textOptions'><div class='edit'>Edit</div>&#9679;<div class='delete'>Delete</div></div>");
    });
    $(document).on('click', '.edit', function(){
        $(this).parent().parent().find(".myChat").attr("contenteditable", "true");
        $(this).parent().parent().find(".myChat").css("pointer-events", "none");
        $(this).parent().parent().find(".textOptions").html("<div class='save'>Save Edits</div>&#9679;<div class='cancel'>Cancel</div>");
    });
    $(document).on('click', '.delete', function(){
        var friend = chatWindows[$(this).parent().parent().parent().parent().attr("id").substring(1)];
        var content = $(this).parent().parent().find(".myChat").html();
        $.ajax({ method: "POST", url: "ajax/chatOptions.php", 
        data: { friend: friend, action: "delete", content: content } }) .done(function(){
            setChats(friend);
        });
    });
    $(document).on('click', '.save', function(){
        var friend = chatWindows[$(this).parent().parent().parent().parent().attr("id").substring(1)];
        var content = $(this).parent().parent().find(".myChat").html();
        $.ajax({ method: "POST", url: "ajax/chatOptions.php", 
        data: { friend: friend, action: "edit", content: content } }) .done(function(){
            setChats(friend);
        });
    });
    $(document).on('click', '.cancel', function(){
        $(this).parent().parent().find(".myChat").attr("contenteditable", "false");
        $(this).parent().parent().find(".myChat").css("pointer-events", "auto");
        $(this).parent().parent().find(".textOptions").html("<div class='edit'>Edit</div>&#9679;<div class='delete'>Delete</div>");
    });

});

// recursive function for consistent update of chats
function realTime(friend){
    if (chatWindows.indexOf(friend) < 0) return;
    setChats(friend);
    setTimeout(function() {
        realTime(friend);
    }, 1000);
}

// show friend requests
function setRequests(){
    $("#insertRequests").empty();
    $.ajax({ method: "POST", url: "ajax/friendRequest.php"}) .done(function(requests){
        if (requests.length==0){
            $(".requestTitle").html("You do not have any friend requests");
        } else { 
            $(".requestTitle").html("You have friend requests below:");
            while (requests.includes(";") && requests.length>1){
                var request = requests.substring(0,requests.indexOf(";"));
                requests = requests.substring(requests.indexOf(";")+1);
                var pic = requests.substring(0,requests.indexOf(";"));
                requests = requests.substring(requests.indexOf(";")+1);
                $("#insertRequests").append("<div id='req"+request+"' class='request'><img class='rounded-circle circle' src='"+pic+"'><p id='name'>"+request+"</p><div class='rounded-pill accept'>Accept</div><div class='rounded-pill decline'>Decline</div></div>");
            }
        }
    });
}

// show texts from opened chatbox
function setChats(friend){
    $.ajax({ method: "POST", url: "ajax/getChats.php"}) .done(function(chat){
        var allChats = [];
        while (chat.includes("}}")){
            var thisChat = chat.substring(0,chat.indexOf("{"));
            if (thisChat.includes("<-|->")){
                chat = chat.substring(chat.indexOf(">")+1);
            } else {
                var time = chat.substring(chat.indexOf("{")+2, chat.indexOf("}"));
                var sender = ""; (chat.includes("<-|->")) ? sender = "me" : sender = "them";
                var chatSender = [sender,thisChat,time];
                chat = chat.substring(chat.indexOf("}")+2);
                allChats.push(chatSender);
            }
        }
        var sortedChats = [allChats[0]];
        for (var x = 1; x < allChats.length; x++){
            for (var y = 0; y < sortedChats.length; y++){
                if (Date.parse(allChats[x][2]) < Date.parse(sortedChats[y][2])){
                    sortedChats.splice(y, 0, allChats[x]); break;
                } else if (y == sortedChats.length-1){
                    sortedChats.push(allChats[x]); break;
                }
            }
        }
        var area = "#c" + chatWindows.indexOf(friend) + " .chatArea";
        $(area).empty();
        for (var x = 0; x < sortedChats.length; x++){
            if (sortedChats[x][0] == "me"){
                $(area).append("<div class='w-100 myHolder'><div class='rounded-pill myChat'>"+sortedChats[x][1]+"</div></div>");
            } else {
                if (x > 0 && sortedChats[x-1][0] == "them")
                    $(area).append("<div class='theirHolder'><div class='rounded-pill theirChat'>"+sortedChats[x][1]+"</div></div>");
                else
                    $(area).append("<div class='theirHolder'><div class='theirChatName'>"+friend+"</div><div class='rounded-pill theirChat'>"+sortedChats[x][1]+"</div></div>");
            }
        }
        $(area).animate({ scrollTop: $('.chatArea')[0].scrollHeight}, 10);
    });
}

// show friend circles that open the chatboxes
function showFriendChats(friendList){
    $("#friends").empty();
    var friends = friendList.substring(1);
    $.ajax({ method: "POST", url: "ajax/profilePic.php", data: {friends:friends} }) .done(function(pics){ 
        while (friends.includes(";") && friends.length>1){
            var friend = friends.substring(0,friends.indexOf(";"));
            var pic = pics.substring(0,pics.indexOf(";"));
            $("#friends").append("<li class='nav-item me-2'><img src='"+pic+"' class='rounded-circle circle' id='"+friend+"'><p class='fw-bold text-center text-white name'>"+friend+"</p></li>");
            friends = friends.substring(friends.indexOf(";")+1);
            pics = pics.substring(pics.indexOf(";")+1);
        }
    });
}

// changes chat area height when window is resized
const resize_box = new ResizeObserver(function(entries){
    const [changed] = entries;
    var id = changed.target.id;
    var num = $("#"+id).height()-85;
    $("#"+id+" .chatArea").css("height", num+"px");
});
