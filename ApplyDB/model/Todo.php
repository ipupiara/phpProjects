<?php


namespace TodoList\Model;

use \DateTime;
use \Exception;
use \TodoList\Validation\TodoValidator;

/**
 * Model class representing one TODO item.
 */
final class Todo {

    // priority
    const PRIORITY_HIGH = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_LOW = 3;
    // status
    const STATUS_PENDING = "PENDING";
    const STATUS_DONE = "DONE";
    const STATUS_OLD_ARCHIVE = "OLDARCHIVE";
    
    
    // not actually a valid status for a todo, only used for the links on the menu
   // const STATUS_ALL   = "ALL";
    const STATUS_ALL   = "ALL";
    
    
    
    /** @var int */
    private $id;
    /** @var string */
    private $priority;
    /** @var int */
    private $pre_name;
    /** @var string */
    private $name;
    /** @var string */
    private $title;
    /** @var string */
    private $company;
    /** @var string */
    private $address;
    /** @var string */
    private $zip_city;
    /** @var string */
    private $greeting_line;
    /** @var string */
    private $business;
    /** @var string */
    private $email;
    /** @var string */
    private $status; 
    /** @var string */
    private $homepage;
    /** @var string */
     private $comment;
    /** @var string */
    private $dateAdded;
    /** @var timestamp */
    private $tempSortFloat;
    
    /**
     * Create new {@link Todo} with default properties set.
     */
    public function __construct() {
        $now = new DateTime();
        $this->setDateAdded($now);
        $this->setPriority(Todo::PRIORITY_MEDIUM);
        $this->setStatus(Todo::STATUS_PENDING);
        $this->tempSortFloat = 0.0;
    }

    public static function allStatuses() {
        return [
            self::STATUS_PENDING,
            self::STATUS_DONE,
            self::STATUS_OLD_ARCHIVE,
        ];
    }

    public static function allPriorities() {
        return [
            self::PRIORITY_HIGH,
            self::PRIORITY_MEDIUM,
            self::PRIORITY_LOW,
        ];
    }

    //~ Getters & setters

    /**
     * @return int <i>null</i> if not persistent
     */
    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        if ($this->id !== null
                && $this->id != $id) {
            throw new Exception('Cannot change identifier to ' . $id . ', already set to ' . $this->id);
        }
        if ($id === null) {
            $this->id = null;
        } else {
            $this->id = (int) $id;
        }
    }
   
    public function getPre_name() {
        return $this->pre_name;
    }

    public function setPre_name($prename) {
        $this->pre_name =  $prename;
    }

  
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }


    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    
    public function getCompany() {
        return $this->company;
    }

    public function setCompany($company) {
        $this->company = utf8_encode($company);
    }
    
    public function getAddress() {
        return utf8_encode($this->address);
    }

    public function setAddress($address) {
        $this->address = $address;
    }
    public function getZip_city() {
        return utf8_encode($this->zip_city);
    }

    public function setZip_city($zC) {
        $this->zip_city = $zC;
    }
    public function getGreeting_line() {
        return $this->greeting_line;
    }

    public function setGreeting_line($title) {
        $this->greeting_line = $title;
    }
    public function getBusiness() {
        return utf8_encode($this->business);
    }

    public function setBusiness($title) {
        $this->business = $title;
    }
    public function getEmail() {
        return $this->email;
    }

    public function setEmail($title) {
        $this->email = $title;
    }
        
   public function getHomepage() {
        return $this->homepage;
    }

    public function setHomepage($hp) {
        $this->homepage = $hp;
    }
 
   public function getComment() {
        return $this->comment;
    }

    public function setComment($hp) {
        $this->comment = $hp;
    }   
    
    public function getDateAdded() {
        return $this->dateAdded;
    }

    public function setDateAdded(DateTime $hp) {
        $this->dateAdded = $hp;
    }    
    
    public function getPriority() {
        return $this->priority;
    }
    public function setPriority($priority) {
        TodoValidator::validatePriority($priority);
        $this->priority = (int) $priority;
    }
    
     public function getStatus() {
        return utf8_encode($this->status);
    }
    public function setStatus($status) {
        TodoValidator::validateStatus($status);
        $this->status = $status;
    }
    
    public function getTempSortFloat() {
        return $this->tempSortFloat;
    }

    public function setTempSortFloat($flt) {
        $this->tempSortFloat = $flt;
    }  
    
    public function getNameString()
    {
        $nameString = "";
        if( $this->getTitle()){$nameString .= $this->getTitle() ; $nameString .= " ";}
        if( $this->getPre_name()){$nameString .= $this->getPre_name()  ; $nameString .= " ";}
        if( $this->getName()){$nameString .= $this->getName()  ;}
        return utf8_encode($nameString);
    }
    
    public function nameStringNotEmpty()
    {
        $str = $this->getNameString();
        $len = strlen(trim($str));
        return ($len > 0);
    }
}
 