var script = document.createElement('script');
script.src = 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js';
document.getElementsByTagName('head')[0].appendChild(script);
if (window.history.replaceState) { window.history.replaceState( null, null, window.location.href ); }

var chatWindows = ["","","","","",""];
var changedRedirect = "";

$(document).ready(function(){

    var top = { level: 0, box: "" };

    showFriendChats(getFriends());
    $(".chatBox").draggable({ handle: ".top" });

    $.ajax({ method: "POST", url: "ajax/friendRequest.php"}) .done(function(msg){
        var requests = msg.substring(1);
        var numRequests = 0;
        while (requests.includes(";") && requests.length>1){
            numRequests++;
            requests = requests.substring(requests.indexOf(";")+1);
        }
        if (numRequests != 0) $(".badge").html(numRequests);
    });

    const boxes = document.querySelectorAll('.chatBox');
    boxes.forEach(box => {
        resize_box.observe(box);
    });

    $('body').dblclick(function(){
        if (changedRedirect != "")
            window.location.href = changedRedirect;
        else 
            window.location.href = getRedirect();
    });

    $(document).on('mousedown', '.chatBox', function(){
        if (top.box != $(this).attr('id')) top.level++;
        top.box = $(this).attr('id');
        $(this).css("z-index", top.level);
    });

    $(document).on('click', '.circle', function(){
        var friend = $(this).attr('id'); 
        document.cookie="receiver="+friend;
        $('.message').val('');
        if (chatWindows.indexOf(friend) > -1){
            $(this).removeClass('active');
            $("#c"+chatWindows.indexOf(friend)).toggle();
            chatWindows[chatWindows.indexOf(friend)] = "";
        } else {
            $(this).addClass('active');
            var opening = chatWindows.indexOf("");
            $("#c"+opening).append($("#chatBoxTemplate").html());
            $("#c"+opening+" .chatName").html(friend);
            $("#c"+opening).toggle();
            var tDiff = Math.floor(Math.random() * $(window).height()/9);
            var rDiff = Math.floor(Math.random() * $(window).width()/11);
            var diffSign = Math.floor(Math.random() * 2);
            (diffSign == 0) ? tDiff *= -1 : rDiff *= -1;
            var top = ($(window).height()/5) + tDiff;
            var right = ($(window).width()/5) + rDiff;
            $("#c"+opening).css({"top":top,"right":right});
            chatWindows[opening] = friend;
            setChats(friend);
        }
    });

    $(document).on('click', '.close', function(){
        var friend = chatWindows[$(this).parent().attr('id').substring(1)];
        $("#"+friend).removeClass('active');
        $("#c"+chatWindows.indexOf(friend)).toggle();
        chatWindows[chatWindows.indexOf(friend)] = "";
    });

    $(document).on('click', '.windowLogo', function(){
        var id = $(this).parent().attr('id');
        if ($(this).width() == "25"){
            $("#"+id).css({"width":"400px","height":"400px","margin":"auto","resize":"both"});
            $("#"+id+" .top").css("pointer-events", "auto");
            $("#"+id+" .windowLogo").css({"width":"30px","top":"8px","right":"36px"});
            $("#"+id+" .windowLogo").attr("src", "http://findicons.com/files/icons/2315/default_icon/256/open_in_new_window.png");
        } else {      
            $("#"+id).css({"width":$(window).width()-150,"height":"100vh","margin-left":"150px","resize":"none","top":"0","bottom":"0","left":"0","right":"0"});
            $("#"+id+" .top").css("pointer-events", "none");
            $("#"+id+" .windowLogo").css({"width":"25px","top":"12px","right":"40px"});
            $("#"+id+" .windowLogo").attr("src", "http://cdn.onlinewebfonts.com/svg/img_509774.png");
        }
    });

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

    $(document).on('click', '.accept, .decline', function(){
        var friend = $(this).parent().attr('id').substring(3);
        var decision = "";
        ($(this).attr('class').includes("accept")) ? decision = "y" : decision = "n";
        $.ajax({ method: "POST", url: "ajax/friendRequest.php", data: {friend: friend,decision: decision} }) .done(function(){
            $("#insertRequests").empty();
            setRequests();
            if (decision == "y") {
                $.ajax({ method: "POST", url: "ajax/friendRequest.php", data: {accept:"accept"} }) 
                .done(function(friends){ showFriendChats(friends); });
            }
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

});

function setRequests(){
    $.ajax({ method: "POST", url: "ajax/friendRequest.php"}) .done(function(msg){
        var requests = msg.substring(1);
        if (requests.length==0){
            $(".requestTitle").html("You do not have any friend requests");
        } else { 
            $(".requestTitle").html("You have friend requests below:");
            while (requests.includes(";") && requests.length>1){
                var request = requests.substring(0,requests.indexOf(";"));
                $("#insertRequests").append("<div id='req"+request+"' class='request'><img class='rounded-circle circle' src='https://i0.wp.com/researchictafrica.net/wp/wp-content/uploads/2016/10/default-profile-pic.jpg?fit=300%2C300&ssl=1'><p id='name'>"+request+"</p><div class='rounded-pill accept'>Accept</div><div class='rounded-pill decline'>Decline</div></div>");
                requests = requests.substring(requests.indexOf(";")+1);
            }
        }
    });
}

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
                $(area).append("<div class='theirHolder'><div class='theirChatName'>"+friend+"</div><div class='rounded-pill theirChat'>"+sortedChats[x][1]+"</div></div>");
            }
        }
        $(area).animate({ scrollTop: $('.chatArea')[0].scrollHeight}, 10);
    });
}

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

const resize_box = new ResizeObserver(function(entries){
    const [changed] = entries;
    var id = changed.target.id;
    var num = $("#"+id).height()-85;
    $("#"+id+" .chatArea").css("height", num+"px");
});
