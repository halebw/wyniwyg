<?php

/**
 * WiseLoop RestTriggerBanByIp class definition<br/>
 * This trigger implemets a simple filter that will reject all the requests comming from an IP list.
 * The IP list is specified in the <i>rest-settings.ini</i> file by the <i>banned_ips</i> field.<br/>
 * This trigger should be hooked to the wlRestTrigger::ON_REQUEST_EVENT.
 * @author WiseLoop
 */
class wlRestTriggerBanByIp extends wlRestTrigger {
    
    /**
     * Runs the trigger.
     */
    public function run() {
        $banned = $this->getService()->getSettings()->getValue('banned_ips');
        if($banned) {
            $banned = ',' . str_replace(array(';', '|', ' '), ',', $banned) . ',';
        }
        $ip = $this->getService()->getRequest()->getRemoteAddress();
        if(strpos($banned, ",$ip,") !== false) {
            throw new wlRestException("Requests from $ip are not allowed", 406);
        }
    }
    
}

?>
