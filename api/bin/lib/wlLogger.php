<?php

/**
 * WiseLoop Logger class definition<br/>
 * This is a generic logger class.
 * The log can be generated in various formats (HTML, XML, TXT) and the messages can be logged in classical queue direction or stacked so the last message will be the first when viewing the log.
 * Also a maximum byte size can be specified for the log size; when the maximum size is reached the log file will be trimmed to keep only the newest messages fitting the specified size.
 * @author WiseLoop
 */

class wlLogger
{
    const TYPE_HTML ='html';
    const TYPE_XML ='xml';
    const TYPE_TEXT = 'txt';

    const DIRECTION_STACK = -1;
    const DIRECTION_QUEUE = 1;

    /**
     * @var string the log name - will be used to compute the log file name
     */
    private $_name;
    
    /**
     * @var string the log output type (html, xml, txt)
     */
    private $_type;
    
    /**
     * @var string the directory path where the log file will be stored
     */
    private $_path;
    
    /**
     * @var int log direction
     * @see DIRECTION_QUEUE, DIRECTION_STACK
     */
    private $_direction;
    
    /**
     * @var int maximum log file size
     */
    private $_kbSize;

    /**
     * Constructor.<br/>
     * Creates a wlLogger object.
     * @param string $name name of the log - will be used to compute the log file name
     * @param int $kbSize maximum log file size
     * @param string $type the log output type (html, xml, txt)
     * @param int $direction log direction
     * @param string $path the directory path where the log file will be stored
     */
    public function __construct($name, $kbSize = 0, $type = self::TYPE_TEXT, $direction = self::DIRECTION_QUEUE, $path = '')
    {
        $this->_path = $path;
        $this->_name = $name;
        $this->_type = $type;
        $this->_direction = $direction;
        $this->_kbSize = $kbSize;
    }

    /**
     * Computes the log filename.
     * @return string
     */
    public function getLogFileName() {
        return $this->_name . '.' . $this->_type;
    }
    
    /**
     * Returns the full log file path.
     * @return string
     */
    public function getLogFilePath()
    {
        if($this->_path === '') {
            return $this->getLogFileName();
        }
        return $this->_path . '/' . $this->getLogFileName();
    }

    /**
     * Logs the provided information.
     * @param string|array $info data to be logged
     */
    public function write($info)
    {
        $head = '';
        $item = '';

        if($this->_type == self::TYPE_HTML)
        {
            $head = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\n<head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/></head>\n";
            $item.= "<p style='margin: 5px 0 5px 0;'>\n";
            $item.= "<p style='font:bold 12px Courier New,sans-serif; background-color:#e0e0e0; border:1px solid #a0a0a0; margin:0; padding:2px;'>".date("Y-m-d H:i:s")."</p>\n";
            if (is_array($info)) {
                foreach ($info as $inf) {
                    $item.= "<p style='font:bold 12px Courier New,sans-serif; border:1px solid #a0a0a0; margin:0; padding:2px'>".nl2br(htmlentities($inf))."</p>\n";
                }
            }
            else {
                $item.= "<p style='font:bold 12px Courier New,sans-serif; border:1px solid #a0a0a0; margin:0; padding:2px'>".nl2br(htmlentities($info))."</p>\n";
            }
            $item.="</p>\n";
        }elseif($this->_type == self::TYPE_XML) {
            $head = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        }else {
            $head = "";
            $item.= "--- ".date("Y-m-d H:i:s")." ---\n";
            if (is_array($info)) {
                foreach ($info as $inf) {
                    $item.= $inf."\n";
                }
            }
            else {
                $item.= $info."\n";
            }
            $item.="\n";
        }

        $file_path = $this->getLogFilePath();
        $fh = @fopen($file_path, 'a+');
        if($fh)
        {
            $this->lock($fh);
            $log = "";
            if($this->_kbSize != 0 || $this->_direction == self::DIRECTION_STACK)
            {
                $fsize = filesize($file_path);
                if ($fsize != 0) {
                    $log = fread($fh, $fsize);
                }
            }
            if($this->_direction == self::DIRECTION_STACK) {
                $log = $head.$item.$log;
            }
            else
            {
                if(file_exists($file_path)) {
                    $log = $log.$item;
                }
                else $log = $head.$log.$item;
            }
            if($this->_kbSize != 0)
            {
                $log = substr($log, 0, $this->_kbSize * 1024);
                ftruncate($fh, 0);
            }
            fwrite($fh, $log);
            fclose($fh);
        }
    }

    /**
     * Applies a lock to the provided file handle to be safely used in multi user environments.
     * @param type $fileHandle
     */
    private function lock($fileHandle)
    {
        if(!$fileHandle) {
            return;
        }
        $time = microtime();
        do
        {
            $canWrite = flock($fileHandle, LOCK_EX);
            if(!$canWrite) {
                usleep(round(rand(0, 100) * 1000));
            }
        }while ((!$canWrite) && ((microtime() - $time) < 1000));
    }
}
?>