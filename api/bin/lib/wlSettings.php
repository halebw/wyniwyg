<?php

/**
 * WiseLoop Settings class definition<br/>
 * This class provides settings accessibility from a standard <i>.ini</i> file.<br/>
 * The actual settings needs to be stored as key/value pairs <i>key=value</i> just like <i>php.ini</i> file does.<br/>
 * Basically, objects of this type holds an associative array and data is loaded into the array using the wlSettings::loadSettingsFile method.
 * The new loaded data is merged into the existing settings data, the new keys will overwrite the pre-existing ones.
 * To empty the settings data, wlRestSettings::reset method mus be called.<br/>
 * To pull a setting value, use wlRestSettings::getValue method and provide the key as parameter.
 * @author WiseLoop
 */
class wlSettings {
    
    /**
     * @var array the settings data (as key/value pair)
     */
    private $_settingsData;
    
    /**
     * Constructor.<br/>
     * Creates a wlSettings object.
     */
    public function __construct() {
        $this->_settingsData = array();
    }

    /**
     * Empties the settigns array.
     */
    public function reset() {
        $this->_settingsData = array();
    }
    
    /**
     * Parses and loads an .ini settings file.
     * The .ini file must store the settings as key/value pairs <i>(key=value)</i> just like <i>php.ini</i> file does.
     * @param array $iniSettingsFilePath the settings file full path
     */
    public function loadSettingsFile($iniSettingsFilePath) {
         $data = @parse_ini_file($iniSettingsFilePath);
         if(is_array($data)) {
             $this->_settingsData = array_merge($this->_settingsData, $data);
         }
    }

    /**
     * Returns a setting value for the specified key or null if the key does not exists.
     * @param string $key
     * @return mixed|null
     */
    public function getValue($key) {
        if(isset($this->_settingsData[$key])) {
            return $this->_settingsData[$key];
        }
        return null;
    }
    
}

?>
