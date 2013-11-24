$(document).ready(function() {

    globalUser = null;
    globalData = {};
    globalDescription = "";
    globalHTML = "";
    globalIndex = null;
    previewHTML = "";
    varsToReplace = new Array();
    
    getVarsAddy = "http://localhost/wyniwyg/api/variables/v1/variables/.json";
    allVariables = {};
    //var fullFieldListText = '[{"variable_id": 1,"name": "First Name","type_id": 1,"input_text": "\'^^First Name^^\'"}, {"variable_id": 2,"name": "Last Name","type_id": 1,"input_text": "\'^^Last Name^^\'"}, {"variable_id": 3,"name": "Title","type_id": 1,"input_text": "\'^^Title^^\'"}, {"variable_id": 4,"name": "Senders Name","type_id": 1,"input_text": "\'^^Senders Name^^\'"}, {"variable_id": 5,"name": "Phone Number","type_id": 1,"input_text": "\'^^Phone Number^^\'"}, {"variable_id": 6,"name": "Address 1","type_id": 1,"input_text": "\'^^Address 1^^\'"}, {"variable_id": 7,"name": "City State, Zip","type_id": 1,"input_text": "\'^^City State, Zip^^\'"}, {"variable_id": 8,"name": "Personal Message","type_id": 2,"input_text": "\'^^Personal Message^^\'"}]';
    //var fullFieldList = JSON.parse(fullFieldListText);
    //store all data in global variable

//    $('#mobileTemplateList').html() = "<p>Hello</p>";
    if (!globalUser) {
        //$.mobile.changePage("#login");
    }


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

            $.mobile.changePage("#home", {transition: "slideup"});
            ////send to make template list
            templateListFunc(globalUser);
            //alert(globalUser);

        });
        //var getAddy = "http://localhost/wyniwyg/api/templates/v1/template/" + globalUser + ".json";
    });
    ///
    $("#mobileTemplateList").on("click", "li", function(event) {
        event.stopPropagation();
        event.preventDefault();
        var target = $(event.target);
        // get template ID
        globalIndex = target.data("index");
        createTemplateFields();
        $.mobile.changePage("#forms", {reverse: false, transition: "slide"});

    })



});

function templateListFunc(user) {

    $.post("http://localhost/wyniwyg/api/templates/v1/template.json", {
        userid: user
    },
    function(data, status) {

        globalData = data;
        //clear template list
        $("#mobileTemplateList").empty();
        //draw new template list
        for (var i = 0; i < data.length; i++) {

            $("#mobileTemplateList").append('<li  data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-first-child ui-last-child ui-btn-up-c"><div class="ui-btn-inner ui-li"><div class="ui-btn-text"><a href="#home" class="ui-link-inherit" data-index= "' + i + '">' + data[i].description + '</a></div><span class="ui-icon ui-icon-arrow-r ui-icon-shadow">&nbsp;</span></div></li>');
        }


    });

}


function createTemplateFields() {

    //header to match template name
    $('#forms div h1').empty();
    $('#forms div h1').append(globalData[globalIndex].description);
    $("#formFields").empty();
    //determine total number of fields to fill out in form
    $.get(getVarsAddy, function(data, status) {
        //loads all variables and types into global object array
        allVariables = data;
    });

    //determien fileds in html (match ^^Vars^^ to field names
   
        var fieldOccurs = (globalData[globalIndex].html).split("^^").length - 1;

        var remainingString = globalData[globalIndex].html;
        var pos = 0;
        var endOfString = remainingString.length;
        for (var i = 0; i < fieldOccurs; i++) {
            endOfString = remainingString.length - pos;
            remainingString = remainingString.slice(pos);

            var varI = remainingString.slice(remainingString.indexOf("^^"), remainingString.indexOf("^:^") + 3);
            //alert(varI + " : " + pos +" : " + endOfString);
            varsToReplace[i] = varI;
            var varSend = "'" + varI + "'";
            
            drawInput(varSend);
            pos = remainingString.indexOf("^:^") + 3;


        }
    


    //draw form of fields on stage

    //send to preview

}

//once ^^Var^^ is found, this will determine which type it is and draw appropriate input
function drawInput(varText) {

    for (var i = 0; i < allVariables.length; i++) {
        if (varText == allVariables[i].input_text) {
            //create input field
            $("#formFields").append(allVariables[i].name + '<br/><input id="input' + allVariables[i].variable_id + '" type="' + allVariables[i].form_type + '" name="' + allVariables[i].input_text + '" /><br/>');
            break;
        }
    }

}

function previewTemplate() {
    //use find and replace for each form item in template
    previewHTML = globalData[globalIndex].html;
    replaceTXT = "";
     //var count = $('#formFields').children('input').length;
    for (var i = 0; i < varsToReplace.length; i++) {
        for (var z = 0; z < allVariables.length; z++){
            var newVarsToReplace = "'"+varsToReplace[i]+"'";
            if(allVariables[z].input_text == newVarsToReplace){
                var inputID = "#input"+allVariables[z].variable_id;
                replaceTXT = $(inputID).val();
                break;
            }
        }
        //alert(something);
        
        previewHTML = previewHTML.replace(varsToReplace[i], replaceTXT);        
    }
    $("#previewArea").empty();
    $("#previewArea").append(previewHTML);
   
   
    //print final string on stage

}

