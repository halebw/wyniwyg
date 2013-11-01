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
        <script src="js/tinymce.min.js"></script>
<!--        <script type="text/javascript">
            tinymce.init({
                selector: "textarea"
            });
        </script>-->
        <script>

            function init() {

                tinymce.init({
                    selector: "#edoc"
                });


                //var tarea = document.getElementById('doc');
//                var button = document.getElementById('insWord');

//                button.addEventListener("click", function() {
                function addText(textToAdd){
                    var editor = tinymce.get('edoc');
                    tinyMCE.activeEditor.execCommand('mceInsertContent', false, textToAdd);
                };
            }

            window.onload = init;
        </script>

        <title>What You Need Is What You Get</title>
        <link rel="stylesheet" href="css/styles.css" type="text/css"/>
    </head>
    <body>

        <header id="header">


            <div id="title">
                <a href="index.php"><h1>WYNIWYG</h1></a>

            </div>
            <div id="loginLinks">
                <!--                <a href="#">Login</a>
                                <a href="#">Sign Up</a>-->
                <a href="login.php">Logout</a>
            </div>
        </header>

        <?php
        //Section for login information
        include 'php/login.php';
        ?>



        <section>

            <div id="templateList" class="sidebar">
                <!--                <form method="POST" >
                                    <input type="button" value="New" action="edit.php" />
                                </form>-->

                <ul>
                    <?php
                    //load templates attached to user
                    $query = "SELECT name, input_text FROM variables";
                    $result = mysql_query($query);
                    //build side list items
                    while ($row = mysql_fetch_array($result)) {
                        //add links to this area for preview to populate
                       
                        echo '<button onclick="addText('.$row["input_text"].')" > ' . $row["name"] . '</button><br/>';
                    }
                    ?>
                </ul>       

            </div>
            <div id="previewArea" class="content">
                <form method="post" action="edit.php">
                <textarea id="edoc"></textarea>
                <submit />
                </form>
              

            </div>
        </section>
        <footer id="footer">
            <p>Copyright &copy; 2013 Benjamin Hale</p>
        </footer>





    </body>
</html>
