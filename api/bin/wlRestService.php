<?php

require_once 'wlRestUtils.php';
require_once 'wlRestController.php';
require_once 'wlRestException.php';
require_once 'wlRestLogger.php';
require_once 'wlRestOutputHandler.php';
require_once 'wlRestRequest.php';
require_once 'wlRestSettings.php';
require_once 'wlRestTrigger.php';
require_once 'wlRestCache.php';
require_once 'output-handlers/wlRestOutputHandlerArray.php';
require_once 'output-handlers/wlRestOutputHandlerJson.php';
require_once 'output-handlers/wlRestOutputHandlerPhp.php';
require_once 'output-handlers/wlRestOutputHandlerPlain.php';
require_once 'output-handlers/wlRestOutputHandlerXml.php';

/**
 * WiseLoop RestService class definition<br/>
 * This class represents the actual service, it is responsible for parsing the request and serving the data back to the requester.<br/>
 * In order to obtain a RESTful service the developer must instantiate this class, registers some controllers and call the wlRestService::run method.
 * @code
$service = new wlRestService();
$service->registerController(new sampleHelloRestControllerV1());
$service->run();
 * @endcode
 * wlRestService class instantiation has to be done inside the endpoint PHP scriptfile without other outputs generated.<br/>
 * @author WiseLoop
 */
class wlRestService {
    
    /**
     * @var array of wlRestController associative array of registered service controllers having as key the name and version of the controller
     */
    private $_registeredControllers;

    /**
     * @var array of wlRestTrigger list of registered triggers
     */
    private $_registeredTriggers;

    /**
     * @var array of wlRestOutputHandler associative array of registered output handlers having as key the extension of the handler
     */
    private $_registeredOutputHandlers;
    
    /**
     * @var string the extension of the default ouput handler
     */
    private $_defaultOutputHandlerExtension;

    /**
     * @var wlRestRequest the current request object
     */
    private $_request;
    
    /**
     * @var mixed data returned by the service
     */
    private $_data;

    /**
     * @var wlSettings service settings<br/>
     * File <b>rest-settings.ini</b> is loaded by default when creating the service.
     */
    private $_settings;
    
    /**
     * @var wlRestLogger current logger; if not null all the exceptions will be logged
     */
    private $_logger;

    /**
     * @var wlRestCache cache handler; it uses file system to manage cache for various data returned by GET in an associative structure pair (key, data).
     */
    private $_cache;

    /**
     * Constructor.<br/>
     * Creates a wlRestService object, loads <b>rest-settings.ini</b> settings file and registers by default JSON, XML, PHP, ARR and PLAIN output handlers.<br/>
     */
    public function __construct() {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        $this->loadSettings(dirname(__FILE__) . '/rest-settings.ini');
        $this->registerOutputHandler(new wlRestOutputHandlerJson(wlRestUtils::RESPONSE_TYPE_JSON));
        $this->registerOutputHandler(new wlRestOutputHandlerXml(wlRestUtils::RESPONSE_TYPE_XML));
        $this->registerOutputHandler(new wlRestOutputHandlerPhp(wlRestUtils::RESPONSE_TYPE_PHP));
        $this->registerOutputHandler(new wlRestOutputHandlerArray(wlRestUtils::RESPONSE_TYPE_ARR));
        $this->registerOutputHandler(new wlRestOutputHandlerPlain(wlRestUtils::RESPONSE_TYPE_PLAIN));
    }
         
    /**
     * Returns the current request.
     * @return wlRestRequest
     */
    public function getRequest() {
        return $this->_request;
    }

    /**
     * Returns the service data as it was computed by the corresponding service controller.<br/>
     * This method can be used by triggers registered with wlRestTrigger::ON_DATA_EVENT in order to do additional general processing of data before sending.
     * @return mixed
     */
    public function getData() {
        return $this->_data;
    }
    
    /**
     * Injects data into the service to be served.<br/>
     * This method can be used by triggers registered with wlRestTrigger::ON_DATA_EVENT in order to inject back the processed data before sending.
     * @param mixed $data
     */
    public function setData($data) {
        $this->_data = $data;
    }
    
    /**
     * Registers a service controller.
     * @param wlRestController $restController
     */
    public function registerController($restController) {
        if (!is_subclass_of($restController, 'wlRestController')) {
            throw new wlRestException('Invalid REST controller class submitted.');
        }
        $restController->setService($this);
        $this->_registeredControllers[$restController->getName() . '/' . $restController->getVersion()] = $restController;
    }
    
    /**
     * Registers a trigger.
     * @param wlRestTrigger $restTrigger
     * @param int $event
     */
    public function registerTrigger($restTrigger, $event) {
        if (!is_subclass_of($restTrigger, 'wlRestTrigger')) {
            throw new wlRestException('Invalid REST trigger class submitted.');
        }
        $restTrigger->setService($this);
        $restTrigger->setEvent($event);
        $this->_registeredTriggers[] = $restTrigger;
    }

    /**
     * Register an outupt handler.
     * @param wlRestOutputHandler $restOutputHandler
     */
    public function registerOutputHandler($restOutputHandler) {
        if (!is_subclass_of($restOutputHandler, 'wlRestOutputHandler')) {
            throw new wlRestException('Invalid REST output handler class submitted.');
        }
        $extensions = $restOutputHandler->getExtensions();
        foreach($extensions as $extension) {
            $this->_registeredOutputHandlers[$extension] = $restOutputHandler;
        }
    }
    
    /**
     * Sets the extension that will be used to get the default ouput handler in case if no extension is provided to the request URL.
     * @param string $extension
     */
    public function setDefaultOutputHandlerByExtension($extension) {
        $this->_defaultOutputHandlerExtension = $extension;
    }

    /**
     * Parses and loads an .ini settings file.
     * The .ini file must store the settings as key/value pairs <i>(key=value)</i> just like <i>php.ini</i> file does.<br/>
     * The new loaded data is merged into the existing settings data, the new keys will overwrite the pre-existing ones.
     * @param string $iniSettingsFilePath
     */
    public function loadSettings($iniSettingsFilePath) {
        if($this->_settings == null) {
            $this->_settings = new wlSettings();
        }
        try {
            if(!file_exists($iniSettingsFilePath)) {
                throw new wlRestException('Settings file ' . $iniSettingsFilePath . ' not found.', 500);
            }
            $this->_settings->loadSettingsFile($iniSettingsFilePath);
            $this->applySettings();
        }catch(wlRestException $ex) {
            $this->serveError($ex);
        }
    }
    
    /**
     * Activates the logger and sets its directory.
     * @param string $dir the full directory path where log will be generated
     */
    public function setLogDir($dir) {
        $this->_logger = new wlRestLogger($dir);
    }

    /**
     * Activates the cache feature.
     * @param string $dir the full directory path where the cache will be stored
     * @param int $cacheTime caching time expressed in seconds
     */
    public function setCacheDir($dir, $cacheTime) {
        $this->_cache = new wlRestCache($dir, $cacheTime);
    }

    /**
     * Logs a message.
     * @param string|array $message
     */
    private function log($message) {
        if($this->_logger == null) {
            return;
        }
        $this->_logger->write(array(
            $this->_request->getUri(),
            $message
        ));
    }
    
    /*
     * Applies new loaded settings.
     */
    private function applySettings() {
        try {
            $route = $this->_settings->getValue(wlRestUtils::SETTING_ROUTE);
            $this->setRoute($route);
        }catch(wlRestException $ex) {
            $this->serveError($ex);
        }
    }
    
    /**
     * Sets the URL route format.<br/>
     * This method is used by wlRestService::applySettings to parse the route specified in the settings file.
     * @param array|string $route the route format
     */
    private function setRoute($route) {
        if(is_string($route)) {
            $route = explode('/', $route);
        }
        if(!$this->_request) {
            $this->_request = new wlRestRequest($route);
        }else {
            $this->_request->parse($route);
        }
    }
    
    /**
     * Returns the service settings.
     * @return wlSettings
     */
    public function getSettings() {
        return $this->_settings;
    }

    /**
     * Runs all the registered triggers linked with the specified event.
     * @param string $eventType
     * @return array
     * @see wlRestTrigger, wlRestTrigger::ON_REQUEST_EVENT, wlRestTrigger::ON_DATA_EVENT, wlRestTrigger::ON_RENDER_EVENT, wlRestTrigger::ON_ERROR_EVENT 
     */
    private function runTriggers($eventType) {
        if($this->_registeredTriggers == null) {
            return array();
        }
        $ret = array();
        foreach($this->_registeredTriggers as $trigger) {
            if($eventType == $trigger->getEvent()) {
                $ret[] = $trigger->run();
            }
        }
        return $ret;
    }

    /**
     * Serves the data returned by the service controller by setting the proper headers and echoing it out.
     * @param mixed $data the data to be served
     * @param int $statusCode heder status code
     * @param string $contentType header content type
     */
    private function serve($data, $statusCode = 200, $contentType = 'text/html') {
        $codeMessage = $statusCode . ' ' . wlRestUtils::getStatusCodeMessage($statusCode);
        header('HTTP/1.1 ' . $codeMessage);
        header('Content-type: ' . $contentType);
        echo $data;
    }
    
    /**
     * Serves an exception by setting the proper headers and echoing it out.<br/>
     * The service data is set to the exception and if there are any wlRestTrigger::ON_ERROR_EVENT registered triggers, they will be used to format the exception;
     * otherwise a default exception formatting will be provided.
     * @param wlRestException $ex
     */
    private function serveError($ex) {
        $statusCode = $ex->getCode();
        if(!$statusCode) {
            $statusCode = 500;
        }
        $codeMessage = $statusCode . ' ' . wlRestUtils::getStatusCodeMessage($statusCode);
        $this->log($codeMessage . ': ' . $ex->getMessage());
        $this->_data = $ex;
        header('HTTP/1.1 ' . $codeMessage);
        header('Content-type: text/html');
        $errs = $this->runTriggers(wlRestTrigger::ON_ERROR_EVENT);
        $ret = '';
        foreach($errs as $err) {
            $ret .= $err;
        }
        if(!$ret) {
            $ret = $codeMessage . ': ' . $ex->getMessage();
        }
        echo $ret;
    }

    /**
     * Lookups for the proper output handler considering the request extension and the registered output handlers.
     * @return wlRestOutputHandler|null
     */
    private function computeOutputHandler() {
        $outputHandler = null;
        $extension = $this->_request->getExtension();
        if(array_key_exists($extension, $this->_registeredOutputHandlers)) {
            $outputHandler = $this->_registeredOutputHandlers[$extension];
        }
        if($outputHandler == null) {
            if(array_key_exists($this->_defaultOutputHandlerExtension, $this->_registeredOutputHandlers)) {
                $outputHandler = $this->_registeredOutputHandlers[$this->_defaultOutputHandlerExtension];
            }else {
                $outputHandler = $this->_registeredOutputHandlers[wlRestUtils::RESPONSE_TYPE_PLAIN];
            }
        }
        return $outputHandler;
    }
    
    /**
     * Runs the service.<br/>
     * This is the most important method and each service should call it inside the endpoint PHP script.
     * @return void
     */
    public function run() {
        try {
            $this->runTriggers(wlRestTrigger::ON_REQUEST_EVENT);
        }catch(wlRestException $ex) {
            $this->serveError($ex);
            return;
        }

        $controllerName = $this->_request->getControllerName();
        $controllerVersion = $this->_request->getControllerVersion();
        
        if(!$controllerName) {
            $info = '';
            foreach($this->_registeredControllers as $controller) {
                $info.=$controller->getHelp();
            }
            $this->serve($info);
            return;
        }
        
        $key = $controllerName . '/' . $controllerVersion;
        
        if(!array_key_exists($key, $this->_registeredControllers)) {
            $this->serveError(new wlRestException('Service controller [' . $this->_request->getControllerName() . '] version [' . $this->_request->getControllerVersion() . '] not found', 404));
            return;
        }
        
        $this->_data = null;
        $rsh = $this->_registeredControllers[$key];
        
        $useCache = ($this->_cache !== null && $this->_request->getMethod() == wlRestRequest::REQUEST_METHOD_GET);
        try {           
            if($useCache) {
                $cacheKey = array($this->_request->getUri(), $this->_request->getActionParams());
                if ($this->_cache->isCacheUpdated($cacheKey)) {
                    $this->_data = $this->_cache->loadCache($cacheKey);
                }else {
                    $rsh->initialize();
                    $this->_data = $rsh->call($this->_request->getControllerAction());
                    $this->_cache->saveCache($cacheKey, $this->_data);
                }
            }else {
                $rsh->initialize();
                $this->_data = $rsh->call($this->_request->getControllerAction());               
            }
        }catch(wlRestException $ex) {
            try {
                $rsh->handleException($ex);
            }catch(wlRestException $ex) {
                $this->serveError($ex);
                return;
            }
        }

        try {
            $this->runTriggers(wlRestTrigger::ON_DATA_EVENT);
        }catch(wlRestException $ex) {
            $this->serveError($ex);
            return;
        }
        
        $contentType = 'text/html';
        if(isset($this->_data)) {
            $outputHandler = $this->computeOutputHandler();
            $this->_data = $outputHandler->render($this->_data);
            $contentType = $outputHandler->getContentType();
        }

        $this->serve($this->_data, 200, $contentType);
    }    
    
}
?>