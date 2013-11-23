<?php

/**
 * WiseLoop RestOutputHandler class definition<br/>
 * This class provides the mechanism to output the service returned data.<br/>
 * An output handler is responsible to transform and render the data according to a desired format and it should also provide the content mime type format that will be sent to the requester.
 * A REST service can register more output handlers to work with, the one used to render the data will be choosen by matching the extension of the request URL with the extensions provided at registering time; 
 * so, the extension acts like a key to identify the proper output handler.<br/>
 * For example the URL http ://rest-service-url-endpoint/resource/99.json will return the data in JSON format (if an output handler with .json extension is registered into the service)<br/>
 * To build a custom output handler the developer must extend wlRestOutputHandler class and overwrite two methods: wlRestOutputHandler::render and wlRestOutputHandler::getContentType.
 * @code
class csvSampleOutputHandler extends wlRestOutputHandler {
    public function render($data) {
        //...
    }
    public function getContentType() {
        //...
    }
 }
 * @endcode
 * To make it available for a REST service, the custom output handler must be registered along with the extensions using wlRestService::registerOutputHandler.
 * @code
 $service->registerOutputHandler(new csvSampleOutputHandler(array('csv', 'sv')));
 * @endcode
 * @author WiseLoop
 * @see wlRestService::registerOutputHandler
 */
class wlRestOutputHandler {
    
    /**
     * @var array the extensions associated with this output handler
     */
    private $_extensions;
    
    
    /**
     * Constructor.<br/>
     * Creates a wlRestOutputHandler object.<br/>
     * In order to associate more than one extension to the output handler, an array of strings representing the extensions can be provided to the constructor.
     * @param string|array $extensions the extensions associated with this output handler
     */
    public function __construct($extensions) {
        if(!is_array($extensions)) {
            $extensions = array($extensions);
        }
        foreach($extensions as $extension) {
            if(is_string($extension)) {
                $this->_extensions[] = $extension;
            }
        }
    }

    /**
     * Returns the associated extensions with this output handler.
     * @return array the output handler associated extensions
     */
    public function getExtensions() {
        return $this->_extensions;
    }

    /**
     * This method is responsible to process the passed data and format it as desired before the REST service will send it to the requester.<br/>
     * When creating custom output handlers, this method needs to be ovewritten in the derived class.
     * @param mixed $data
     * @return mixed the processed data to be sent to the requester
     */
    public function render($data) {
        return $data;
    }
    
    /**
     * This method provides the content mime type information that will be sent in the header along with the data.<br/>
     * It should consist of a single line of code returning the content type string.<br/>
     * The default value is 'text/html', so for a custom output handler this method should be overwritten in the derived class if the content type is different from 'text/html'.
     * @return string the content mime type header that will be sent to the requester
     */
    public function getContentType() {
        return 'text/html';
    }
    
}

?>
