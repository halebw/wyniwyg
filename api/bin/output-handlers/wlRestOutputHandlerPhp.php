<?php

/**
 * WiseLoop PHP RestOutputHandler class definition<br/>
 * This class implements an output handler that will render the data as a serialized PHP array.<br/>
 * This output handler is registered by default when creating a new REST Service.
 * @see wlRestOutputHandler
 */
class wlRestOutputHandlerPhp extends wlRestOutputHandler {
    
    /**
     * Renders the data as a serialized PHP array (like PHP <i>serialize</i> function does).
     * @param mixed $data
     * @return mixed the processed data to be sent to the requester
     * @see wlRestService
     */
    public function render($data) {
        return serialize(wlRestUtils::encodeToArray($data));
    }
    
}

?>
