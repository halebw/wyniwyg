$(document).ready(function() {
    
    var globalUser = 1;
    var globalData ={};
   //store all data in global variable

//    $('#mobileTemplateList').html() = "<p>Hello</p>";
    if (!globalUser) {
        //$.mobile.changePage("#login");
    }
    ;
   
//------------------Login function
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
            //alert(globalUser);

        });
        //var getAddy = "http://localhost/wyniwyg/api/templates/v1/template/" + globalUser + ".json";
    });
    
    $("#mobileTemplateList").on("click", "li", function(event){
        var target = $(event.target);
        // get template ID
        //alert(target.data("index"));
        createTemplateFields(globalData, globalUser);
        $.mobile.changePage("#forms");
    })
    
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
       globalData = data;
       var htmlReplace = "";
       for (var i = 0; i < data.length; i++) {
            $("#mobileTemplateList").append('<li  data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-first-child ui-last-child ui-btn-up-c"><div class="ui-btn-inner ui-li"><div class="ui-btn-text"><a href="#home" class="ui-link-inherit" data-index= "' + data[i].template_id + '">'+data[i].description+'</a></div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div></li>');
            //htmlReplace += "<li><a href='#'>"+data[i].description+"</a></li>";
        }
        
        //$("#mobileTemplateLilst").replaceWith(htmlReplace);
    });

};

function createTemplateFields(data, user){
    $('#forms div h1.ui-title').replaceWith(data.description);
    
};
