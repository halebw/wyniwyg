<?php

/**
 * WiseLoop RestRequest class definition<br/>
 * This class is responsible to parse and hold the request information submited by a data consumer through the URL.<br/>
 * The request object determines and holds the following information:
 * - the request method: can be GET, POST, PUT or DELETE;
 * - the request extension: is a string preceded by dot and it will be used to determine the output handler for rendering the data;
 * - service controller name: retrieved from the URL, it will be used to instantiate a registered controller in order to call the required service action;
 * - service controller version: retrieved from the URL, it represents the service controller version to be used when calling an action;
 * - service controller action name: retrieved from the URL, it represents the actual controller method name that will be called to provide the requested data;
 * - action parameters: these parameters are retrieved from PUT and POST submited data;
 * - an array of query (GET) parameters: this piece of information is retrieved from the URL and will consist of some (key/value) pairs according to the URL format;<br/>
 * The URL route format provided in the constructor represents the keys and it is used to parse and compute the actual GET parameters values given in the URL.<br/>
 * For example, if we have a route like <b>key1/key2/key3</b>, after submitting <b>http ://rest-service-url-endpoint/abc/some-value/99</b>, the query array will have: <b>('key1'=>'abc', 'key2'=>'some-value', 'key3'=>'99')</b>.<br/>
 * A more realistic example is the default route format given in the default <b>rest-settings.ini</b> settings file: <b>route=controller/version/action</b>.
 * In this case, after submitting <b>http ://rest-service-url-endpoint/math/v1/add/1/2/3/4</b>, the query array will have: <b>('controller'=>'math', 'version'='v1', 'action'=>'add')</b>.
 * The values to be added (1, 2, 3, 4) are available when calling wlRestRequest::getQueryParams method.
 * @author WiseLoop
 */
class wlRestRequest {
    
    const REQUEST_METHOD_GET = 'GET';
    const REQUEST_METHOD_POST = 'POST';
    const REQUEST_METHOD_PUT = 'PUT';
    const REQUEST_METHOD_DELETE = 'DELETE';
    
    /**
     * @var string the URL extension - will be used to determine the output handler for rendering the data
     */
    private $_extension = null;

    /**
     * @var array the full query (GET) parameters (key/value pairs) according to the route format
     */
    private $_params = null;

    /**
     * @var string the request method (GET, POST, PUT, DELETE)
     * @see wlRestRequest::REQUEST_METHOD_GET, wlRestRequest::REQUEST_METHOD_POST, wlRestRequest::REQUEST_METHOD_PUT, wlRestRequest::REQUEST_METHOD_DELETE
     */
    private $_method = null;
    
    /**
     * @var mixed data submited through POST or PUT requests
     */
    private $_actionParams = null;

    /**
     * @var array the query (GET) parameters except the ones specified in the URL route format;
     * these are the parameters that can be accesed when calling a service controller action
     */
    private $_queryParams = null;

    /**
     * @var mixed data submited through PUT requests
     */
    private $_cachedPutInfo = null;
    
    /**
     * Constructor.<br/>
     * Creates a wlRestRequest object.
     * @param string $urlRouteFormat
     */
    public function __construct($urlRouteFormat) {
        $this->parse($urlRouteFormat);
    }
    
    /**
     * Parses the given URL route format against the actual URL.
     * @param array $urlRouteFormat
     */
    public function parse($urlRouteFormat) {
        if(!$urlRouteFormat) {
            throw new wlRestException('URL route format is invalid', 500);
        }
        $pathInfo = $this->computePathInfo();

        $this->_extension = $this->computeExtension($pathInfo);
        if($this->_extension) {
            $pathInfo = substr($pathInfo, 0, strlen($pathInfo) - strlen($this->_extension) - 1);
        }
        
        $urlFormatCount = count($urlRouteFormat) + 1;
        $urlParamsValues = wlRestUtils::completeArray(explode('/', $pathInfo, $urlFormatCount), $urlFormatCount);
        $this->_params = array();
        foreach($urlRouteFormat as $k => $paramName) {
            $this->_params[$paramName] = $urlParamsValues[$k];
        }
        
        $this->_queryParams = array();
        $getString = $urlParamsValues[$urlFormatCount - 1];
        if($getString) {
            $this->_queryParams = explode('/', $getString);
        }
        
        $this->_method = $this->computeRequestMethod();
        $this->_actionParams = $this->computeActionParams($this->_method);
        
        if(!$this->_actionParams) {
            $this->_actionParams = $this->_queryParams;
        }
    }    
    
    /**
     * Returns the query (GET) parameters except the ones specified in the URL route format.<br/>
     * These are the parameters that can be accesed when calling a service controller action.
     * @return array|null
     */
    public function getQueryParams() {
        return $this->_queryParams;
    }
    
    /**
     * Returns the data submited through POST or PUT requests.
     * In the case of GET and DELETE request types this will return same as wlRestRequest::getQueryParams.
     * @return mixed|null
     */
    public function getActionParams() {
        return $this->_actionParams;
    }
    
    /**
     * Returns the URL extension; this will be used to determine the output handler for rendering the data.
     * @return string
     */
    public function getExtension() {
        return $this->_extension;
    }
    
    /**
     * Returns the request method (GET, POST, PUT or DELETE)
     * @return string
     */
    public function getMethod() {
        return $this->_method;
    }
    
    /**
     * Returns the URL parameter for a specified key as defined in the route format.
     * @param string $key
     * @return string
     */
    public function getParam($key) {
        if(isset($this->_params[$key])) {
            return $this->_params[$key];
        }
        return null;
    }

    /**
     * Returns the service controller name.
     * It will be used to instantiate a registered controller in order to call the required service action.<br/>
     * The service controller name is identified by the <b>controller</b> key as defined by the default route format in <b>rest-settings.ini</b> file.
     * The developer should not give a different meaning to this key by overwriting (using) it in another settings file.
     * @return string
     * @see wlRestUtils::ROUTE_CONTROLLER
     */
    public function getControllerName() {
        return $this->getParam(wlRestUtils::ROUTE_CONTROLLER);
    }
    
    /**
     * Returns the service controller version.
     * It represents the service controller version to be used when calling an action.<br/>
     * The service controller version is identified by the <b>version</b> key as defined by the default route format in <b>rest-settings.ini</b> file.
     * The developer should not give a different meaning to this key by overwriting (using) it in another settings file.
     * @see wlRestUtils::ROUTE_VERSION
     * @return string
     */
    public function getControllerVersion() {
        return $this->getParam(wlRestUtils::ROUTE_VERSION);
    }
    
    /**
     * Returns the service controller action.
     * It represents the actual controller method name that will be called to provide the requested data.<br/>
     * The service controller action is identified by the <b>action</b> key as defined by the default route format in <b>rest-settings.ini</b> file.
     * The developer should not give a different meaning to this key by overwriting (using) it in another settings file.
     * @see wlRestUtils::ROUTE_ACTION
     * @return string
     */
    public function getControllerAction() {
        return $this->getParam(wlRestUtils::ROUTE_ACTION);
    }
    
    /**
     * Returns the IP address of the requester.
     * @return string
     */
    public function getRemoteAddress() {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    }

    /**
     * Returns the hostname of the requester or null if not available.
     * @return string|null
     */
    public function getRemoteHost()
    {
        return isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : null;
    }
    
    /**
     * Returns the user agent of the requester or null if not available.
     * @return string|null
     */
    public function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
    }
    
    /**
     * Returns the request full URL.
     * @return string
     */
    public function getUri() {
        return $_SERVER['REQUEST_URI'];
    }
    
    /**
     * Returns a parsed pathinfo from the request full URL containing only the relevant information for determining the service controller name, version, action and other query (GET) parameters.
     * @return string
     */
    private function computePathInfo() {
        $path = $this->getUri();
        if (array_key_exists('PATH_INFO', $_SERVER) === true) {
            $path = $_SERVER['PATH_INFO'];
        }else {
            $root = dirname(str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
            if($root == '.') {
                $root = '';
            }else {
                $root .= '/';
            }

            $path = str_replace($_SERVER['SCRIPT_NAME'], '', $path);
            if ($path[strlen($path) - 1] == '/') {
                $path = substr($path, 0, -1);
            }
            if ($root) {
                $path = str_replace($root, '', $path);
            }
        }
        if(substr($path, 0, 1) == '/') {
            $path = substr($path, 1);
        }
        return $path;
    }

    /**
     * Returns the URL extension used to instantiate the proper output handler.
     * @param string $pathInfo the parsed pathinfo as returned by wlRestRequest::computePathInfo
     * @return string
     * @see wlRestRequest::computePathInfo
     */
    private function computeExtension($pathInfo) {
        $k = strrpos($pathInfo, '.');
        if($k !== false) {
            return strtolower(str_replace('.', '', substr($pathInfo, $k, 999)));
        }
        return null;
    }
    
    /**
     * Returns the request method.
     * @return string
     */
    private function computeRequestMethod() {
        $requestMethod = strtoupper($_SERVER['REQUEST_METHOD']);
        $override = strtoupper(isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) ? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : (isset($_GET['method']) ? $_GET['method'] : ''));
        if ($requestMethod == wlRestRequest::REQUEST_METHOD_POST && $override == wlRestRequest::REQUEST_METHOD_PUT) {
            $requestMethod = wlRestRequest::REQUEST_METHOD_PUT;
        } elseif ($requestMethod == wlRestRequest::REQUEST_METHOD_POST && $override == wlRestRequest::REQUEST_METHOD_DELETE) {
            $requestMethod = wlRestRequest::REQUEST_METHOD_DELETE;
        }
        return $requestMethod;
    }

    /**
     * Returns the passed data through POST or PUT requests.
     * @param string $requestMethod the request method
     * @return array
     */
    private function computeActionParams($requestMethod) {
        $actionParams = null;

        if($requestMethod == wlRestRequest::REQUEST_METHOD_PUT) {
            $actionParams = $this->getPutInfo();
        }elseif($requestMethod == wlRestRequest::REQUEST_METHOD_POST) {
            $actionParams = $_POST;
        }
        return $actionParams;
    }
    
    /**
     * Returns tha passed data through PUT request.
     * It needs to be cached as it is read from a stream and it will become unavailable after the first read.
     * @return mixed
     */
    private function getPutInfo() {
        if($this->_cachedPutInfo == null) {
            $this->_cachedPutInfo = file_get_contents('php://input');
        }
        return $this->_cachedPutInfo;
    }
}

?>
