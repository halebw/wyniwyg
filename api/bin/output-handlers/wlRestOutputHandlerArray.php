<?php

/**
 * WiseLoop Array RestOutputHandler class definition<br/>
 * This class implements an output handler that will render the data as a standard PHP array.<br/>
 * This output handler is registered by default when creating a new REST Service.
 * @see wlRestOutputHandler
 */
class wlRestOutputHandlerArray extends wlRestOutputHandler {
    
    /**
     * Renders the data as a standard PHP array (like PHP <i>print_r</i> function does).
     * @param mixed $data
     * @return mixed the processed data to be sent to the requester
     * @see wlRestService
     */
    public function render($data) {
        return print_r(wlRestUtils::encodeToArray($data), true);
    }
    
}

?>
