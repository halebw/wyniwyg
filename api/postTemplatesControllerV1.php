<?php

require_once('../php/connect.php');

class postTemplatesControllerV1 extends wlRestController {

    public function getDefaultName() {
        return 'templates';
    }

    protected function templatePost() {

        $userID = $this->actionParams['userid'];
        $userIDNum = (int) $userID;
        
        if(is_numeric($userID[0])){
        $query = "SELECT t.template_id, t.description, t.html FROM templates t, users_templates ut WHERE t.template_id = ut.template_id AND ut.user_id = '" . $userIDNum . "'";
        $result = mysql_query($query);
        $rows = [];
        $i = 0;
        while ($row = mysql_fetch_array($result)) {
            
            $rows[$i] = $row;
            $i++;
        }
        return $rows;
        }else{
            return 'Error, param not numeric';
        }
    }
}

?>
