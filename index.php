<?php
//session Start
session_start();
require( 'php/connect.php');



header("Content-Type: text/html");
header("Cache-Control: no-store");
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></meta>

        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes" />
        <script src="js/jquery-1.10.2.min.js"></script>
        <script src="js/jquery.tinymce.min.js"></script>
        <script type="text/javascript">
//            tinymce.init({
//                selector: "textarea"
//            });
        </script>

        <title>What You Need Is What You Get</title>
        <link rel="stylesheet" href="css/styles.css" type="text/css"/>
    </head>
    <body>



        <?php
        include 'php/login.php';
        include 'php/header.php';

        print_r($_SESSION);
        echo '</br>';
        print_r($_POST);
        ?>



        <section>

            <div id="templateList" class="sidebar">
                <form method="post" action="edit.php">
                    <input type="submit" value="New" name="formID" />
                </form>
                <!--                <a href="edit.php" ><h2 class="button" onclick="editTemp('new')" >Create New</h2></a>-->
                <form method="post" action="index.php">
                    <?php
                    //load templates attached to user
                    $query = "SELECT t.description, t.template_id FROM templates t, users_templates ut WHERE t.template_id = ut.template_id AND ut.user_id = '" . $_SESSION["user_id"] . "'";
                    $result = mysql_query($query);
                    //build side list items
                    while ($row = mysql_fetch_array($result)) {
                        //add links to this area for preview to populate
                        echo '<input type="submit" name="formID"  value="'.$row["template_id"].'"> ' . $row["description"] . '</input>';
                    }
                    ?>
                </form>       

            </div>
            <div id="previewArea" class="content">
                <?php
                if(isset($_POST['formID'])){
                $query = "SELECT html FROM templates WHERE template_id = ".$_POST['formID'];
                $result = mysql_query($query);
                while ($row = mysql_fetch_array($result)){
                    echo '<div id="preview" >'.$row["html"].'</div>';
                
                }
                 print_r($result);
                };
                
               
                ?>
            </form>
            </div>
        </section>
        <footer id="footer">
            <p>Copyright &copy; 2013 Benjamin Hale</p>
        </footer>





    </body>
</html>
