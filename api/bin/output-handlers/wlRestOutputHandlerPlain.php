<?php

/**
 * WiseLoop Plain RestOutputHandler class definition<br/>
 * This class implements an output handler that will render the data as it is without any formatting; if data is an array, then PHP print_r function is used to generate the output.<br/>
 * This output handler is registered by default when creating a new REST Service.
 * @see wlRestOutputHandler
 */
class wlRestOutputHandlerPlain extends wlRestOutputHandler {

    /**
     * Renders the data in standard plain format without any formatting; if data is an array, then PHP print_r function is used to generate the output.
     * @param mixed $data
     * @return mixed the processed data to be sent to the requester
     * @see wlRestService
     */
    public function render($data) {
        if(is_array($data)) {
            return print_r(wlRestUtils::encodeToArray($data), true);
        }
        return $data;
    }

}

?>
