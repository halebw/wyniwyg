<?php

/**
 * WiseLoop RestTriggerError class definition<br/>
 * This class implemets a trigger that will nicely format the service errors before sending to the requester.
 * It is highly recommended to use this trigger as it provides very useful information about the error along with the stack trace that can be used in debugging.<br/>
 * This trigger should be hooked to the wlRestTrigger::ON_ERROR_EVENT.
 * @author WiseLoop
 */
class wlRestTriggerError extends wlRestTrigger {
    
    /**
     * Runs the trigger.
     */
    public function run() {
        $ex = $this->getService()->getData();
        $code = $ex->getCode();
        if(!$code) {
            $code = 500;
        }
        $message = wlRestUtils::getStatusCodeMessage($code);
        $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];
        $body = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>' . $code . ' ' . $message . '</title></head><body><h2>[' . $code . '] ' . $message . '</h2>';
        $body.= '<p><strong>' . $ex->getMessage() . '</strong></p>';
        if(true) {
            $body.= '<p>in: ' . $ex->getFile() . ' at line '. $ex->getLine() . '</p>';
            $body.= '<p>' . str_replace("\n", "<br/>", $ex->getTraceAsString()) . '</p>';
        }
        $body.= '<hr/><address>' . $signature . '</address></body></html>';
        return $body;
    }
}

?>
