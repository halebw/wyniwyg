<?php

/**
 * WiseLoop RestTriggerServiceKey class definition<br/>
 * This trigger implements an API key verification procedure to validate the requests against a fixed key code.
 * The key code is specified in the <i>rest-settings.ini</i> file by the <i>service_key</i> field.
 * Of course, the developer can implement any kind of validation logic and key generation procedure as the key does not have to be provided necesarry by the <i>rest-settings.ini</i> file.
 * This trigger is designed just to provide a sample but it cand be used as it is.<br/>
 * This trigger should be hooked to the wlRestTrigger::ON_REQUEST_EVENT.
 * @author WiseLoop
 */
class wlRestTriggerServiceKey extends wlRestTrigger {
    
    /**
     * Runs the trigger.
     */
    public function run() {
        $serviceKey = $this->getService()->getSettings()->getValue('service_key');
        $userKey = $this->getService()->getRequest()->getParam('service_key');
        if(trim(strtolower($serviceKey)) !== trim(strtolower($userKey))) {
            throw new wlRestException("Service key is invalid", 401);
        }
    }
    
}

?>
