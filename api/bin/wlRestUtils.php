<?php

/**
 * WiseLoop RestUtils class definition<br/>
 * This class provides some useful static methods and constants for the whole WiseLoop PHP Rest Services package.
 * @author WiseLoop
 */
class wlRestUtils {
    const RESPONSE_TYPE_JSON = 'json';
    const RESPONSE_TYPE_XML = 'xml';
    const RESPONSE_TYPE_PHP = 'php';
    const RESPONSE_TYPE_ARR = 'array';
    const RESPONSE_TYPE_PLAIN = 'plain';

    /**
     * Hardcoded key for the service controller name as defined by the default route format in <b>rest-settings.ini</b> file.<br/>
     * The developer should not give a different meaning to this key by overwriting (using) it in another settings file.
     */
    const ROUTE_CONTROLLER = 'controller';

    /**
     * Hardcoded key for the service controller version as defined by the default route format in <b>rest-settings.ini</b> file.<br/>
     * The developer should not give a different meaning to this key by overwriting (using) it in another settings file.
     */
    const ROUTE_VERSION = 'version';

    /**
     * Hardcoded key for the service controller action name as defined by the default route format in <b>rest-settings.ini</b> file.<br/>
     * The developer should not give a different meaning to this key by overwriting (using) it in another settings file.
     */
    const ROUTE_ACTION = 'action';

    /**
     * Hardcoded key for specifying the url route format (the default is defined in <b>rest-settings.ini</b> file).<br/>
     * The developer can specify another route format in another settings file (ex. <b>route=apikey/controller/version/action</b>).
     * The new settings file must be registerd to the main service using wlRestService::loadSettings method.
     */
    const SETTING_ROUTE = 'route';
    
    /**
     * Returns the text representation for the given http status code.
     * @param int $statusCode the http status code
     * @return string
     */
    public static function getStatusCodeMessage($statusCode) {
        $codes = Array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );

        return (isset($codes[$statusCode])) ? $codes[$statusCode] : '';
    }

    /**
     * Returns the accepted request method type for a givem full service controller action name.<br/>
     * Action names methods are following a convention that they must end with the accepted request method.
     * Ex. For <b>personGet</b>, GET is the request method and <b>personGet</b> method is called if in the request URL the action parameter is be set to <b>person</b> and the request is sent using GET.
     * @param string $actionName full service controller action name including the request method (Get, Post, Put, Delete)
     * @return string the accepted request method (GET, POST, PUT, DELETE)
     */
    public static function getRequestMethodForAction($actionName) {
        $actionName = strtoupper($actionName);
        if(substr($actionName, -1 * strlen(wlRestRequest::REQUEST_METHOD_DELETE)) == wlRestRequest::REQUEST_METHOD_DELETE) {
            return wlRestRequest::REQUEST_METHOD_DELETE;
        }
        if(substr($actionName, -1 * strlen(wlRestRequest::REQUEST_METHOD_GET)) == wlRestRequest::REQUEST_METHOD_GET) {
            return wlRestRequest::REQUEST_METHOD_GET;
        }
        if(substr($actionName, -1 * strlen(wlRestRequest::REQUEST_METHOD_POST)) == wlRestRequest::REQUEST_METHOD_POST) {
            return wlRestRequest::REQUEST_METHOD_POST;
        }
        if(substr($actionName, -1 * strlen(wlRestRequest::REQUEST_METHOD_PUT)) == wlRestRequest::REQUEST_METHOD_PUT) {
            return wlRestRequest::REQUEST_METHOD_PUT;
        }
        return null;
    }
    
    /**
     * Completes an array with elements having a specified value until it has a certain number of elements.
     * @param array $array
     * @param int $count the number of elements
     * @param null $value the value to be added to array
     * @return array
     */
    public static function completeArray($array, $count, $value = null) {
        if(!is_array($array)) {
            $array = array($array);
        }
        for($i=count($array); $i<$count; $i++) {
            $array[] = $value;
        }
        return $array;
    }    
    
    /**
     * Encodes the given data to an array and normalizes the keys to make sure they are unique.
     * @param mixed $data
     * @param bool $forXml if the result will be used to render XML
     * @return array
     */
    public static function normalizeArray($data, $forXml = false) {
        if(!is_array($data)) {
            return self::normalizeArray(array($data), $forXml);
        }
        $ret = array();
        foreach($data as $key => $value) {
            if(is_numeric($key) && $forXml == true) {
                $key = "_$key";
            }
            if(is_array($value)) {
                $value = self::normalizeArray($value, $forXml);
            }
            $ret[$key] = $value;
        }
        return $ret;
    }
    
    /**
     * Returns the given data as an array.
     * @param mixed $data
     * @param bool $forXml if the result will be used to render XML
     * @return array the normalized array
     */
    public static function encodeToArray($data, $forXml = false) {
        if(is_array($data)) {
            return self::normalizeArray($data, $forXml);
        }
        return self::encodeToArray(array($data), $forXml);
    }    

    /**
     * Converts an array to XML.
     * @param array $array
     * @param string $rootElement
     * @param mixed $xml
     * @return mixed
     */
    public static function arrayToXml($array, $rootElement = null, $xml = null) {
        $_xml = $xml;

        if ($_xml === null) {
            $_xml = new SimpleXMLElement($rootElement !== null ? $rootElement : '<response/>');
        }

        foreach ($array as $k => $v) {
            if (is_array($v)) {
                self::arrayToXml($v, $k, $_xml->addChild($k));
            } else {
                $_xml->addChild($k, $v);
            }
        }

        return $_xml->asXML();
    }    
}
?>