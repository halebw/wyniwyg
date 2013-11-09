<?php
//session Start
session_start();
if(isset($_POST['mobile']) && ($_POST['mobile'] == 'mobile')){
    
    header ("Location: mindex.php");
    
}
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

//        print_r($_SESSION);
//        echo '</br>';
//        print_r($_POST);
        ?>



        <section>

            <div id="templateList" class="sidebar">
                <?php
                //only displays sidebar once user is logged in.
                if (isset($_SESSION['user_id'])) {
                    echo'<h3>Avaliable Templates</h3>';

//                <!--                <a href="edit.php" ><h2 class="button" onclick="editTemp('new')" >Create New</h2></a>-->
                    echo '<form method="post" action="index.php">';


                    //load templates attached to user
                    $query = "SELECT t.description, t.template_id FROM templates t, users_templates ut WHERE t.template_id = ut.template_id AND ut.user_id = '" . $_SESSION["user_id"] . "'";
                    $result = mysql_query($query);
                    //build side list items
                    while ($row = mysql_fetch_array($result)) {
                        //add links to this area for preview to populate
                        echo '<button type="submit" name="formID"  value="' . $row["template_id"] . '"> ' . $row["description"] . '</button><br /> ';
                    }
                    echo '</form>';
                } else {
                    echo'<div></div>';
                }
                echo '<br /><br /><form method="post" action="edit.php">';
                echo '<button id="newBtn" type="submit" value="New" name="submit" >Create New</button>';
//                echo '<input type="hidden" name="formID" value="New" />';
                echo '</form>';
                ?>


            </div>
            <div id="previewArea" class="content">
                <?php
                if (isset($_POST['formID'])) {
                    $query = "SELECT html FROM templates WHERE template_id = " . $_POST['formID'];
                    $result = mysql_query($query);

                    while ($row = mysql_fetch_array($result)) {
                        echo '<div id="preview" >' . $row["html"] . '</div>';
                        echo '<form method="post" action="edit.php">';
                        echo '<input type="hidden" name="formID" value ="' . $_POST["formID"] . '" ></input>';
                        echo '<input type="submit" name="submit" value="Edit"  />';
                        echo '</form>';
                    }
                } else {
                    echo'<div id="preview" ></div>';
                };
//                include ('php/console.php');
                ?>
                </form>
            </div>
        </section>
        <footer id="footer">
            <p>Copyright &copy; 2013 Benjamin Hale</p>
        </footer>
    </body>
</html>
