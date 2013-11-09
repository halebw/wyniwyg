$(document).ready(function() {
    var globalUser = null;
    
    $("#loginBtn").click(function() {
        var user = $("#usernameField").val();
        var pass = $("#passwordField").val();
        var hashPass = Sha1.hash(pass);
        //alert(hashPass);
        $.post("http://localhost/wyniwyg/api/users/v1/user.json",
                {
                    user_name: user,
                    password: hashPass
                },
        function(data, status) {
            //alert("Data:" + data + "status:" + status);
            globalUser = data;
           $.mobile.changePage("#home");
           alert(globalUser);
        });
    });

});




//function loginScript() {
//    alert('called');
//    var userAPI = "localhost/wyniwyg/api/users/v1/user.xml";
//    
//    
//    
////    xmlhttp.open("POST", userAPI, true);
////    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
////    xmlhttp.send("user_name=user&password=pass");
//    
////    xmlDoc=xmlhttp.responceXML;
//    
////    alert (xmlDoc);
////    $.getJSON(userAPI, {user_name: "user", password: "pass"})
////            .done(function(data) {
////        alert(data);
////    });
////    $.ajax(
////            type: "post"
////            url: userAPI, 
////            data: {user_name: "user", password: "pass"},
////            success: alert("success"),
////            dataType: "$."
////        
////        ).done(function(data) {
////        alert("Data Loaded: " + data);
////    });
//}

