<?php


namespace TodoList\Validation;

/**
 * Validation error.
 */
final class ValidationError {

      
    const INVALID_DATEADDED = '101';
    const EMPTY_PRIORITY = '102';
    const INVALID_PRIORITY = '103';
    const EMPTY_STATUS = '104';
    const INVALID_STATUS = '105';
    const EMPTY_COMPANY = '106';
    const SIMILAR_COMPANY_CONFLICT = '201';
  
    
    
    private $source;
    private $message;
    private $ignorable;
    private $errorId;
    private $infoArray;

    /**
     * Create new validation error.
     * @param mixed $source source of the error
     * @param string $message error message
     */
    function __construct($source, $message, $errId, $ignore = false, $arr = null) {
        $this->source = $source;
        $this->message = $message;
        $this->ignorable = $ignore;
        $this->errorId = $errId;
        $this->infoArray = $arr;
    }

    /**
     * Get source of the error.
     * @return mixed source of the error
     */
    public function getSource() {
        return $this->source;
    }

    public function getInfoArray() {
        return $this->infoArray;
    }
    
    /**
     * Get error message.
     * @return string error message
     */
    public function getMessage() {
        return $this->message;
    }
    
    public function getIgnorable() {
        return $this->ignorable;
    }
    
    public function getErrorTitle() {
        return $this->source .'-' .$this->errorId;
    }
    
    public static function  getErrorSourceFromTitle(String $title) 
    {
        $res = '';
        $pos = strpos($title,'-');        
        $res = substr($title,0,$pos);
        return $res;
    }

    public static function getErrorIdFromTitle(String $title)
    {
        $res ='';
        $pos = strpos($title,'-');        
        $res = substr($title,$pos+1);    
        return res;
    }
            
    public function getErrorCheckboxName()
    {
        $res = $this->getErrorTitle().'_IgnoreCheckbox';
        return $res;
    } 
    
    public function postContainsResolvement() 
    {
        $res = false;
         if ((array_key_exists($this->getErrorCheckboxName(), $_POST) ) &&  ($_POST[$this->getErrorCheckboxName()] == 'on')) {
                    $res = true;
         }
        return $res; 
    }
}
