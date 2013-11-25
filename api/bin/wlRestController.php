<?php

/**
 * WiseLoop RestController class definition<br/>
 * This generic class provides a global way to create service actions.<br/>
 * A service controller consists of actual actions responding to requests.<br/>
 * To create a service controller, the developer must extend wlRestController class and define some actions that will process the requests and provide data to be served back to the requester.
 * The actions are implemented by class methods (declared as protected or public) following the convention: <b>actionName<i>RequestMethod</i></b>.
 * Request method must be Get, Post, Put or Delete.<br/>
 * Ex.: Method <b>add<i>Get</i></b> implements action <b>add</b> that accepts only GET requests.<br/>
 * @code
 class sampleHelloRestControllerV1 extends wlRestController {
    protected function helloGet() {
        $params = $this->getService()->getRequest()->getQueryParams();
        $name = $params[0];
        return "Hello $name (from GET)";
    }
 }
 * @endcode
 * The service controller instance must then be registered to a wlRestService object.
 * @code
$service = new wlRestService();
$service->registerController(new sampleHelloRestControllerV1('hello'));
$service->run();
 * @endcode
 * @author WiseLoop
 */
abstract class wlRestController {
    
    const DEFAULT_ACTION_NAME = 'defaultAction';

    /**
     * @var wlRestService the REST services that has registered the current controller
     */
    private $_restService = null;

    /**
     * @var string service controller name given at construction time
     */
    private $_name;

    /**
     * @var array query GET parameters as returned by the current request wlRestRequest::getQueryParams method
     */
    protected $queryParams;

    /**
     * @var mixed action parameters as returned by the current request wlRestRequest::getActionParams method
     */
    protected $actionParams;

    /**
     * Constructor.<br/>
     * Creates a wlRestController object.
     * @param string $name the service controller name<br/>
     * The name is important because it is used to identify the actual controller instance by finding a match between the name provided in the request URL and the wlRestService registered controllers.
     * If no name is provided in the constructor, the real derived controller class must reimplement the wlRestController::getDefaultName method.
     * @see wlRestRequest, wlRestService::registerController
     */
    public function __construct($name = null) {
        $this->_name = $name;
    }

    /**
     * Sets the REST service that has registered the current controller
     * @param wlRestService $restService
     * @return void
     */
    public function setService($restService) {
        if($restService) {
            $this->_restService = $restService;
        }
    }
    
    /**
     * Returns the REST service that has registered the current controller
     * @return wlRestService
     */
    public function getService() {
        return $this->_restService;
    }

    /**
     * Returns the name of the current service controller.<br/>
     * The name is used to identify the actual controller instance by finding a match between the name provided in the request URL and the registered controllers.
     * @return string
     */
    public function getName() {
        if($this->_name) {
            return $this->_name;
        }
        return $this->getDefaultName();
    }
    
    /**
     * Calls an action (method) of the service controller.<br/>
     * The given action name is provided by the parsed request URL against the URL route format specified in settings file.<br/>
     * The actual method that will be called is computed as a concatenation between the given $actionName and the request method (wlRestRequest::getMethod).
     * This is the mechanism for linking an action to a request method type: the action name must be followed by the allowed request methods: Get, Post, Put, Delete.<br/>
     * Ex.: if in the route format (given in the default <b>rest-settings.ini</b> settings file or another .ini file) is <b>route=controller/version/action</b>
     * and the request URL (submitted by GET request method) is <b>http ://rest-service-url-endpoint/math/v1/add/1/2/3/4</b>,
     * the sevice controller named <b>math</b>, version <b>v1</b> must have a method <b>addGet</b>.
     * The <b>addGet</b> method has to be declared as <b>protected</b> or <b>public</b>.
     * If method <b>addGet</b> does exists, an exception will be thrown.
     * @param string $actionName the action name (taken previously from the URL)
     * @return mixed|string
     * @see wlRestRequest, wlRestService::setRoute
     */
    public function call($actionName) {
        if($this->_restService) {
            $this->queryParams = $this->_restService->getRequest()->getQueryParams();
            $this->actionParams = $this->_restService->getRequest()->getActionParams();
        }

        $fullActionName = strtolower(self::DEFAULT_ACTION_NAME);
        if($actionName) {
            $fullActionName = strtolower($actionName . $this->_restService->getRequest()->getMethod());
        }

        if(is_callable(array($this, $fullActionName))) {
            try {
                return $this->$fullActionName();
            }catch(wlRestException $ex) {
                throw $ex;
            }
        }
        $exception = 'Service handler (' . $this->_restService->getRequest()->getControllerName() . ') does not have a registered action (' . $fullActionName . ').';
        throw(new wlRestException($exception, 405));
    }


    /**
     * Returns the default controller name if no name is provided at construction time.<br/>
     * This method returns null by default, but it must be reimplemented in the actual derived controller if no name is provided in the constructor.
     * @return string|null
     */
    public function getDefaultName() {
        //can be overwritten
        return null;
    }
    
    /**
     * Returns the default version of the current service controller.<br/>
     * This method returns <b>v1</b> by default, but it can be reimplemented in the actual derived controller classes.
     * @return string
     */
    public function getVersion() {
        //can be overwritten
        return 'v1';
    }
    
    /**
     * Performs some general initializations before calling the action.<br/>
     * This method does nothing by default, but it can be reimplemented in the actual derived controller classes.
     * @return void
     */
    public function initialize() {
        //can be overwritten
    }

    /**
     * Calls a default action if not explicit action name is provided in the URL.<br/>
     * This method returns by default what wlRestController::getHelp returns, but it can be reimplemented in the actual derived controller classes.
     * @return string|null
     * @see wlRestController::getHelp
     */
    protected function defaultAction() {
        //can be overwritten
        return $this->getHelp();
    }
    
    /**
     * Returns a formatted list with action names and the corresponding acceptable requests methods.<br/>
     * Results of this method will be served by default if no explicit action is provided in the request URL.
     * @return string
     */
    public function getHelp() {
        $serviceDescription = $this->getControllerDescription();
        $ret = '<h3>' . $this->getName() . ' version ' . $this->getVersion() . '</h3>';
        $ret.='<ul>';
        foreach($serviceDescription as $actionItem) {
            $actionName = $actionItem['action'];
            $actionMethods = '';
            foreach($actionItem['methods'] as $method) {
                $actionMethods.="$method ";
            }
            $ret.="<li>Action: <b>$actionName</b>, methods: <b>$actionMethods</b></li>";
        }
        $ret.='</ul>';
        return $ret;
    }
    
    /**
     * This method is used internally by wlRestController::getHelp to get a list with action names and the corresponding acceptable requests methods.
     * @return array
     */
    private function getControllerDescription() {
        $ret = array();
        $classInfo = new ReflectionClass($this);
        $classMethods = $classInfo->getMethods(ReflectionMethod::IS_PROTECTED);
        foreach($classMethods as $classMethod) {
            $className = $classMethod->class;
            if($className !== __CLASS__) {
                $requestMethod = wlRestUtils::getRequestMethodForAction($classMethod->name);
                $actionName = str_ireplace($requestMethod, '', $classMethod->name);

                if(!isset($ret[$actionName])) {
                    $ret[$actionName] = array(
                        'action' => $actionName,
                        'description' => '',
                        'methods' => array()
                    );
                }
                $ret[$actionName]['methods'][] = $requestMethod;
            }
        }
        return $ret;
    }
   
    /**
     * Handles any exception during an action call.<br/>
     * By default this method just rethrows the expection, but it can be reimplemented in the actual derived controller classes to take more custom actions upon the exception.
     * Anyway, regardless of the custom actions taken, finally the exception should be re-thrown in order to be catch by the global wlRestService exception handler and formatted and served properly to the requester.
     * @param wlRestException $ex
     */
    public function handleException($ex) {
        //can be overwritten
        throw($ex);
    }
}
?>