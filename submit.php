<?php
//session Start
session_start();
require( 'php/connect.php');

 $query = "INSERT INTO templates (
                    template_id,
                    html,
                    description)
                    VALUES( NULL,'".$_POST['content']."','".$_POST['description']."')";
                    
if (!mysql_query($query)){
    die('Error: '. mysql_error());
}
echo "document saved";

mysql_close();


header("Content-Type: text/html");
header("Cache-Control: no-store");
header("Pragma: no-cache");



?>
<!DOCTYPE html>

<html>
    <head>

    </head>
    <body>
        
        
        <pre>
            <?php
            print_r($_POST);
            echo '<br/>';
            print_r($_GET);
            echo '<br/>';
            print_r($_SESSION);
            echo '<br/>';
            print_r($_SESSION);
            ?>
        </pre>

    </body>
</html>