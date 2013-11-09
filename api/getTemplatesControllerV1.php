<?php

require_once('../php/connect.php');

class getTemplatesControllerV1 extends wlRestController {

    public function getDefaultName() {
        return 'templates';
    }

    protected function templateGet() {

        $params = $this->getService()->getRequest()->getActionParams();
        
        if(is_numeric($params[0])){
        $query = "SELECT * FROM templates t, users_templates ut WHERE t.template_id = ut.template_id AND ut.user_id = '" . $params[0] . "'";
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
