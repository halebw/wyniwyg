<?php
//session Start
session_start();
require( 'php/connect.php');
print_r($_POST);
echo '<br/>';
print_r($_SESSION);

if ($_POST['formID'] == 'New') {


    $query = "INSERT INTO templates (
                    template_id,
                    html,
                    description)
                    VALUES( NULL,'" . $_POST['content'] . "','" . $_POST['description'] . "')";

    $query2 = "INSERT INTO users_templates (
                    user_template_id,
                    user_id,
                    template_id)
                    VALUES(NULL, '" . $_SESSION['user_id'] . "', (SELECT MAX( template_id) FROM templates))";
    

    if (!mysql_query($query)) {
        die('Error: ' . mysql_error());
    }
    echo "document saved";
    if (!mysql_query($query2)) {
        die('Error: ' . mysql_error());
    }
    mysql_close();
}else{
    
    $query = "UPDATE templates
                    SET 
                    html = '". $_POST['content']."',
                    description = '".$_POST['description']."'
                    WHERE
                    template_id = ".$_POST['formID'];
    
    if(!mysql_query($query)){
        die('Error: '. mysql_error());
    }
    mysql_close();
}

header("Content-Type: text/html");
header("Cache-Control: no-store");
header("Pragma: no-cache");

header("Location: index.php");
?>
<!DOCTYPE html>

<html>
    <head>

    </head>
    <body>


        <pre>
<?php

?>
        </pre>

    </body>
</html>