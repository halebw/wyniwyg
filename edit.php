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
                    selector: "textarea",
                    theme: "modern",
                    apply_source_formatting: true,
                    height: 400,
                    plugins: [
                        "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                        "save table contextmenu directionality emoticons template paste textcolor"
                    ],
                    content_css: "css/content.css",
                    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons",
                    style_formats: [
                        {title: 'Bold text', inline: 'b'},
                        {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                        {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                        {title: 'Example 1', inline: 'span', classes: 'example1'},
                        {title: 'Example 2', inline: 'span', classes: 'example2'},
                        {title: 'Table styles'},
                        {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
                    ]
                });
//                    selector: "#textarea"
//                });


                //var tarea = document.getElementById('doc');
//                var button = document.getElementById('insWord');

//                button.addEventListener("click", function() {



            }
            function addText(textToAdd) {
                var editor = tinymce.get('edoc');
                tinyMCE.activeEditor.execCommand('mceInsertContent', false, textToAdd);
            }
            ;

            window.onload = init;
        </script>

        <title>What You Need Is What You Get</title>
        <link rel="stylesheet" href="css/styles.css" type="text/css"/>
    </head>
    <body>



        <?php
        //Section for login information
        require 'php/login.php';
        require 'php/header.php';
        ?>



        <section>

            <div id="templateList" class="sidebar">
                <!--                <form method="POST" >
                                    <input type="button" value="New" action="edit.php" />
                                </form>-->
                <h2>Variable List</h2>
                <ul>
                    <?php
                    //load templates attached to user
                    $query = "SELECT name, input_text FROM variables";
                    $result = mysql_query($query);
                    //build side list items
                    while ($row = mysql_fetch_array($result)) {
                        //add links to this area for preview to populate

                        echo '<button 
                            onclick="addText(' . $row["input_text"] . ')">' . $row["name"] . '</button><br/>';
                    }
                    //onclick="addText(' . $row["input_text"] . ')"
                    ?>
                </ul>       

            </div>
            <div id="previewArea" class="content">
                <?php
//                include ('php/console.php');
//                ?>
                <!--                <form method="POST" action="submit.php">
                                    <textarea id="edoc" style="min-height: 500px; ">
                
                                    </textarea>
                                    <input type="submit" value="Submit"/>
                                </form>-->
                <form method="post" action="submit.php" >
                    <?php
                    if ($_POST['submit'] == 'Edit') {
                        //if submit is from existing template, this will fill the fields with the existing data
                        $query = "SELECT html, description FROM templates WHERE template_id = " . $_POST['formID'];
                        $result = mysql_query($query);
                        while ($row = mysql_fetch_array($result)) {
                            $descript = $row['description'];
                            $htmlText = $row['html'];
                            echo '<input name="formID" type="hidden" value="' . $_POST["formID"] . '"/>';
                        }
                    } else {
                        $descript = "";
                        $htmlText = "";
                        echo '<input name="formID" type="hidden" value="New" />';
                    }

                    echo ' Description:<input name="description" type="text" value="' . $descript . '"/>';

                    //echo '<input name="content" id="textarea" type="textarea" value=' . $htmlText . '/>';
                    echo '<textarea id="textarea" name="content" >' . $htmlText . '</textarea>';
                    ?>
                    <input name="submit" type="submit"/>
                    <?php
                    ?> 
<!--                           <textarea name="content" style="width:100%"></textarea>-->
                </form>
                <!--                <form method="POST" action="submit.php">
                                    <input type="text" />
                                    <input type="submit"/>
                                </form>-->

            </div>
        </section>
        <footer id="footer">
            <p>Copyright &copy; 2013 Benjamin Hale</p>
        </footer>





    </body>
</html>
