<?php
   session_start();

  session_destroy();
   if(!$_SESSION){
       header("Location: ../index.php");
   }else{
       header("Location: logout.php");
   }
   

?>
