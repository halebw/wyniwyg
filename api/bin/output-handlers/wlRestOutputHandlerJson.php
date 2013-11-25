<?php

/**
 * WiseLoop JSON RestOutputHandler class definition<br/>
 * This class implements an output handler that will render the data as in JSON format.<br/>
 * This output handler is registered by default when creating a new REST Service.
 * @see wlRestOutputHandler
 */
class wlRestOutputHandlerJson extends wlRestOutputHandler {
    
    /**
     * Renders the data in JSON format.
     * @param mixed $data
     * @return mixed the processed data to be sent to the requester
     * @see wlRestService
     */
    public function render($data) {
        return json_encode(wlRestUtils::encodeToArray($data));
    }
 
    /**
     * Returns '<i>application/json</i>' string as the content mime type header that will be sent to the requester.
     * @return string 
     */
    public function getContentType() {
        return 'application/json';
    }
    
}

?>
