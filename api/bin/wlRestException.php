<?php

/**
 * WiseLoop RestException class definition<br/>
 * This provides a custom exception class to help in error handling across the other REST services classes.
 * @author WiseLoop
 */
class wlRestException extends Exception {
    
    /**
     * Constructor.<br/>
     * Creates a wlRestException object.
     * @param string $message
     * @param string|int $code
     */
    public function __construct($message, $code = null) {
        parent::__construct($message, $code);
    }
    
    /**
     * Returns the string representation of the current wlRestException object.
     * @return string
     */
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}";
    }
    
}

?>
