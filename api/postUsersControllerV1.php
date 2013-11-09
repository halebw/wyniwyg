<?php

require_once('../php/connect.php');

class postUsersControllerV1 extends wlRestController {

    public function getDefaultName() {
        return 'users';
    }

    protected function userPost() {
        $pass = $this->actionParams['password'];
        
        $myusername = $this->actionParams['user_name'];
        $mypassword = sha1($pass);

//        // To protect MySQL injection 
//        $myusername = trim($myusername);
//        $myusername = stripslashes($myusername);
//        $myusername = mysql_real_escape_string($myusername);

        $query = "SELECT user_id FROM users WHERE user_name = '" . $myusername . "' AND password = '" . $mypassword . "'";
        $result = mysql_query($query);
        if (mysql_num_rows($result) > 0) {

            //if there is a result, login = success
            
            $row = mysql_fetch_array($result);   //put the result into a PHP array
            $user_id = $row["user_id"];   //here is the user_id
            return $user_id;
        }else{
            return 'User not Found';
        }
    }

}

?>
