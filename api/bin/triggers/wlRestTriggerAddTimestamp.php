<?php

/**
 * WiseLoop RestTriggerAddTimestamp class definition<br/>
 * This class implemets a trigger designed to be hooked to the wlRestTrigger::ON_DATA_EVENT that simply adds a timestamp to the data.<br/>
 * This trigger is designed just to provide a sample but it cand be used as it is.
 * @author WiseLoop
 */
class wlRestTriggerAddTimestamp extends wlRestTrigger {

    /**
     * Runs the trigger.
     */
    public function run() {
        $data = $this->getService()->getData();
        if(!$data) {
            return;
        }
        $data = wlRestUtils::encodeToArray($data);
        $data['_timestamp'] = date('Y m d H:i:s');
        $this->getService()->setData($data);
    }
    
}

?>
