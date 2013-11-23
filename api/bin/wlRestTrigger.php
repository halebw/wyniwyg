<?php

/**
 * WiseLoop RestTrigger class definition<br/>
 * This class provides means to execute some user actions when specific events happens.<br/>
 * To build a trigger the developer must extend wlRestTrigger class and overwrite the wlRestTrigger::run method.
 * @code
class wlRestTriggerServiceKey extends wlRestTrigger {
    public function run() {
        //...
    }
}
 * @endcode
 * To hook it up to a REST service, the trigger must be registered along with an event using wlRestService::registerTrigger method.
 * @code
$service->registerTrigger(new wlRestTriggerServiceKey(), wlRestTrigger::ON_REQUEST_EVENT);
 * @endcode
 * @author WiseLoop
 */
class wlRestTrigger {

    /**
     * ON_REQUEST_EVENT: occurs just before calling the service method, when the request is parsed.<br/>
     * This event can be used to hook triggers that can do various authentication procedures like session authentication or to implement an API service key validation.
     */
    const ON_REQUEST_EVENT = 1;
    
    /**
     * ON_DATA_EVENT: occurs after the raw data is computed and retrieved, just before rendering and sendig it to the requester.<br/>
     * This event can be used to hook triggers that can do additional general processing of data before sending.
     * A good example is the wlRestTriggerAddTimestamp trigger that simply adds a time stamp information to the data.
     */
    const ON_DATA_EVENT = 2;

    /**
     * ON_RENDER_EVENT: occurs after data is rendered with the corresponding output handler.<br/>
     * This event can be used to hook triggers that can do additional general formatting of data before sending.
     * This event is quite seldom used as the main rendering of data is and should be done by the output handler.
     */
    const ON_RENDER_EVENT = 3;
    
    /**
     * ON_ERROR_EVENT: occurs when an exception is raised.<br/>
     * This event is used to hook triggers that formats the error message before sending it to the requester.
     * Of course, the trigger can be used to mute any errors raised during the service call but this practice is not recommended.
     * A good example (and it is highly recommended to be used) is wlRestTriggerError trigger that formats nicely the error message along with the error code and displays also the stack trace.
     */
    const ON_ERROR_EVENT = 4;
    
    /**
     * @var wlRestService the REST service that has registered the current trigger
     */
    private $_restService = null;

    /**
     * @var int the event identifier to which the current trigger is hooked
     */
    private $_event = null;
    
    public function __construct() {
    }
    
    /**
     * Sets the REST service that has registered the current trigger.
     * @param wlRestService $restService
     * @return void
     */
    public function setService($restService) {
        if($restService) {
            $this->_restService = $restService;
        }
    }
    
    /**
     * Returns the REST service that has registered the current trigger.
     * @return wlRestService
     */
    public function getService() {
        return $this->_restService;
    }

    /**
     * Sets the event identifier to which the current trigger is hooked.
     * @param int $event
     */
    public function setEvent($event) {
        $this->_event = $event;
    }
    
    /**
     * Returns the event identifier to which the current trigger is hooked.
     * @return int
     */
    public function getEvent() {
        return $this->_event;
    }
    
    /**
     * Runs the curent trigger.<br/>
     * This method should be overwritten in the derived class.
     */
    public function run() {
    }
}

?>
