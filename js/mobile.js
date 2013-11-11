$(document).ready(function() {
    var globalUser = null;
   

//    $('#mobileTemplateList').html() = "<p>Hello</p>";
    if (!globalUser) {
        //$.mobile.changePage("#login");
    }
    ;


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
            
            templateListFunc(globalUser);
//            alert(globalUser);

        });
        //var getAddy = "http://localhost/wyniwyg/api/templates/v1/template/" + globalUser + ".json";
    });
});

function templateListFunc(user) {
    //alert("im in here:" + user);
    //var getAddy = "http://localhost/wyniwyg/api/templates/v1/template.json";
//    $.getJSON(getAddy).done(function(data){
    $.post( "http://localhost/wyniwyg/api/templates/v1/template.json", {
        userid: user
    },
    function(data, status) {
        //alert("data: " + data + "status: " + status); 
        //$("#mobiletemplateList").append(data[1,1]);
       // alert(data.length);
       for (var i = 0; i < data.length; i++) {
           
           
            $("#mobileTemplateList").append('<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-first-child ui-last-child ui-btn-up-c"><div class="ui-btn-inner ui-li"><div class="ui-btn-text"><a href="#home" class="ui-link-inherit">'+data[i].description+'</a></div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div></li>');
        
           
       }
    });

}
;
