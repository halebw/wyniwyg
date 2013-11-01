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
            tinymce.init({
                selector: "textarea"
            });
        </script>

        <title>What You Need Is What You Get</title>
        <link rel="stylesheet" href="css/styles.css" type="text/css"/>
    </head>
    <body>

        

        <?php
        include 'php/login.php';
        include 'php/header.php';
        
        print_r($_SESSION);
        ?>



        <section>

            <div id="templateList" class="sidebar">
                <form method="post" action="edit.php">
                    <input type="submit" value="New" />
                </form>
                <!--                <a href="edit.php" ><h2 class="button" onclick="editTemp('new')" >Create New</h2></a>-->
                <ul>
                    <?php
                    //load templates attached to user
                    $query = "SELECT t.description FROM templates t, users_templates ut WHERE t.template_id = ut.template_id AND ut.user_id = '" . $_SESSION["user_id"] . "'";
                    $result = mysql_query($query);
                    //build side list items
                    while ($row = mysql_fetch_array($result)) {
                        //add links to this area for preview to populate
                        echo '<li> ' . $row["description"] . '</li>';
                    }
                    ?>
                </ul>       

            </div>
            <div id="previewArea" class="content">
<!--                <form method="post">
                    <textarea></textarea>
                    <button type="submit" value="SubmitTest "/>
                </form>-->

            </div>
        </section>
        <footer id="footer">
            <p>Copyright &copy; 2013 Benjamin Hale</p>
        </footer>





    </body>
</html>
