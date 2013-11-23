<?php

/**
 * WiseLoop RestTriggerAntiSpam class definition<br/>
 * This class implemets a trigger that will protect the service against abusive usage limiting the number of requests per unit of time comming from the same IP address.
 * The number of request is specified in the <i>rest-settings.ini</i> file by the <i>max_hits_per_5seconds</i> field.
 * The time interval is set to 5 seconds by default but it can be modified by tunning the SECONDS constant.<br/>
 * This trigger should be hooked to the wlRestTrigger::ON_REQUEST_EVENT.
 * @author WiseLoop
 */
class wlRestTriggerAntiSpam extends wlRestTrigger {
    
    CONST SECONDS = 5;
    
    /**
     * Runs the trigger.
     */
    public function run() {
        @session_start();
        $now = time();
        $ip = $this->getService()->getRequest()->getRemoteAddress();
        $key = "rest-$ip";
        $keyTime = "$key-time";

        if(!isset($_SESSION[$key])) {
            $_SESSION[$key] = 0;
            $_SESSION[$keyTime] = $now;
        }

        $diff = $now - $_SESSION[$keyTime];
        if($diff < self::SECONDS) {
            $_SESSION[$key] = $_SESSION[$key] + 1;
            $maxRequests = $this->getService()->getSettings()->getValue('max_hits_per_5seconds');
            if($_SESSION[$key] >= $maxRequests) {
                throw new wlRestException('Too many requests. Requests are limited to ' . $maxRequests . ' per ' . self::SECONDS . ' seconds. Please try again later.', 503);
            }
        } else {
            $_SESSION[$key] = 0;
            $_SESSION[$keyTime] = $now;
        }
    }
}

?>
