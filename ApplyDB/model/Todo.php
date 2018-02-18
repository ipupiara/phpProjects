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
    const STATUS_VOIDED = "VOIDED";

    /** @var int */
    private $uid;
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
    private $closedNeg;
    /** @var int */
    private $homepage;
    /** @var string */
     private $comments;
    /** @var string */
    private $dateAdded;
    /** @var timestamp */
         

    /**
     * Create new {@link Todo} with default properties set.
     */
    public function __construct() {
        $now = new DateTime();
        $this->setDateAdded($now);
    }

    public static function allStatuses() {
        return [
            self::STATUS_PENDING,
            self::STATUS_DONE,
            self::STATUS_VOIDED,
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
    public function getUid() {
        return $this->uid;
    }

    public function setId($uid) {
        if ($this->uid !== null
                && $this->uid != $uid) {
            throw new Exception('Cannot change identifier to ' . $uid . ', already set to ' . $this->uid);
        }
        if ($uid === null) {
            $this->uid = null;
        } else {
            $this->uid = (int) $uid;
        }
    }

   
    public function getPreName() {
        return $this->pre_name;
    }

    public function setPrename($prename) {
        $this->pre_name = (int) $prename;
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
        $this->company = $company;
    }
    
    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
    }
    public function getZip_city() {
        return $this->zip_city;
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
        return $this->business;
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
    
    public function getClosedNeg() {
        return $this->closedNeg;
    }

    public function setClosedNeg($cN) {
        $this->closedNeg = $cN;
    }
        
   public function getHomepage() {
        return $this->homepage;
    }

    public function setHomepage($hp) {
        $this->homepage = $hp;
    }
 
   public function getComments() {
        return $this->comments;
    }

    public function setComments($hp) {
        $this->comments = $hp;
    }   
    
    public function getDateAdded() {
        return $this->dateAdded;
    }

    public function setDateAdded(DateTime $hp) {
        $this->dateAdded = $hp;
    }    
    
}
