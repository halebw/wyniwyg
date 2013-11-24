<?php

require_once('../php/connect.php');

class getVariablesControllerV1 extends wlRestController {

    public function getDefaultName() {
        return 'variables';
    }

    protected function variablesGet() {

        $params = $this->getService()->getRequest()->getActionParams();
        
        if(isset($params[0])){
        $query = "SELECT v.variable_id, v.name, v.input_text, t.name, t.form_type FROM
variables v LEFT JOIN types t ON v.type_id= t.type_id WHERE v.variable_id = '".$params[0]."'";
        $result = mysql_query($query);
        $rows = [];
        $i = 0;
        while ($row = mysql_fetch_array($result)) {
            
            $rows[$i] = $row;
            $i++;
        }
        return $rows;
        }else{
           $query = "SELECT v.variable_id, v.name AS 'name', v.input_text, t.name AS 'tname', t.form_type FROM
variables v LEFT JOIN types t ON v.type_id= t.type_id ";
        $result = mysql_query($query);
        $rows = [];
        $i = 0;
        while ($row = mysql_fetch_array($result)) {
            
            $rows[$i] = $row;
            $i++;
        }
        return $rows;
        }
        
    }
}

?>
