<?php

require_once 'lib/wlCacheByFile.php';

/**
 * WiseLoop RestCache class definition<br/>
 * The RestCache object uses file system to manage cache for various data returned by GET request method in an associative structure pair (key, data).
 * An existing directory and the cache time (expressed in seconds) must be specified when constructing the rest cache object.
 * @code
$restCache = new wlRestCache($dir, $cacheTime);
 * @endcode
 * @author WiseLoop
 * @see wlCacheByFile
 */
class wlRestCache extends wlCacheByFile {

    /**
     * Constructor.<br/>
     * Creates a wlRestCache object.
     * @param type $dir the cache directory
     * @param type $cacheTime the cache time expressed in seconds
     */
    public function __construct($dir, $cacheTime = 0) {
        parent::__construct($dir, 'rest.cache', $cacheTime);
    }
    
}

?>
