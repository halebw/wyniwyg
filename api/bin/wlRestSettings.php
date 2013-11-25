<?php

require_once 'lib/wlSettings.php';

/**
 * WiseLoop RestSettings class definition<br/>
 * This is an extension of the standard wlSettings class to be used by PHP REST Services package.<br/>
 * Settings file <b>rest-settings.ini</b> is loaded by default when creating new REST services.
 * To overwrite or merge the default settings with some custom settings, load them using wlRestService::loadSettings:
 * @code
$service->loadSettings(__DIR__ . '/custom-rest-settings.ini');
 * @endcode
 * @author WiseLoop
 * @see wlRestService::loadSettings
 */
class wlRestSettings extends wlSettings {
    
}

?>
