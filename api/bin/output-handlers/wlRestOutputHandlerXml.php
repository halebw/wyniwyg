<?php

/**
 * WiseLoop XML RestOutputHandler class definition<br/>
 * This class implements an output handler that will render the data in XML format.<br/>
 * This output handler is registered by default when creating a new REST Service.
 * @see wlRestOutputHandler
 */
class wlRestOutputHandlerXml extends wlRestOutputHandler {
    
    /**
     * Renders the data in XML format.
     * @param mixed $data
     * @return mixed the processed data to be sent to the requester
     * @see wlRestService
     */
    public function render($data) {
        return wlRestUtils::arrayToXml(wlRestUtils::encodeToArray($data, true));
    }
    
    /**
     * Returns '<i>text/xml</i>' string as the content mime type header that will be sent to the requester.
     * @return string
     */
    public function getContentType() {
        return 'text/xml';
    }  
    
}

?>
