<?php

require_once 'lib/wlLogger.php';

/**
 * WiseLoop RestLogger class definition<br/>
 * If provided to the REST service, this class will log all the errors and warnings occured.
 * @code
//...
$service = new wlRestService();
//...
$service->setLogDir(__DIR__);
//...
 * 
 * @endcode
 * @author WiseLoop
 * @see wlLogger
 */
class wlRestLogger extends wlLogger {
    
    /**
     * Constructor.<br/>
     * Creates a wlRestLogger object.
     * @param type $dir the the directory path where the log file will be stored
     */
    public function __construct($dir) {
        parent::__construct('rest.log', 4096, wlLogger::TYPE_TEXT, wlLogger::DIRECTION_QUEUE, $dir);
    }
    
}

?>
