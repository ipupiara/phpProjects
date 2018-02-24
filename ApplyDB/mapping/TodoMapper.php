<?php


namespace TodoList\Mapping;

use \DateTime;
use \TodoList\Model\Todo;

/**
 * Mapper for {@link \TodoList\Model\Todo} from array.
 * @see \TodoList\Validation\TodoValidator
 */
final class TodoMapper {

    private function __construct() {
    }

    /**
     * Maps array to the given {@link Todo}.
     * <p>
     * Expected properties are:
     * <ul>
     *   <li>id</li>
     *   <li>priority</li>
     *   <li>created_on</li>
     *   <li>due_on</li>
     *   <li>last_modified_on</li>
     *   <li>title</li>
     *   <li>description</li>
     *   <li>comment</li>
     *   <li>status</li>
     *   <li>deleted</li>
     * </ul>
     * @param Todo $todo
     * @param array $properties
     */
    public static function map(Todo $todo, array $properties) {
        if (array_key_exists('id', $properties)) {
            $todo->setId($properties['id']);
        }      
        if (array_key_exists('pre_name', $properties)) {
            $todo->setPre_name($properties['pre_name']);
        }
        if (array_key_exists('name', $properties)) {
            $todo->setName($properties['name']);
        }        
        if (array_key_exists('title', $properties)) {
            $todo->setTitle(trim($properties['title']));
        }
        if (array_key_exists('company', $properties)) {
            $todo->setCompany(trim($properties['company']));
        }        
        if (array_key_exists('address', $properties)) {
            $todo->setAddress(trim($properties['address']));
        }        
        if (array_key_exists('zip_city', $properties)) {
            $todo->setZip_city(trim($properties['zip_city']));
        }        
        if (array_key_exists('greeting_line', $properties)) {
            $todo->setGreeting_line(trim($properties['greeting_line']));
        }
        if (array_key_exists('business', $properties)) {
            $todo->setBusiness(trim($properties['business']));
        }
        if (array_key_exists('email', $properties)) {
            $todo->setEmail(trim($properties['email']));
        }        
        if (array_key_exists('status', $properties)) {
            $todo->setStatus($properties['status']);
        }        
        if (array_key_exists('homepage', $properties)) {
            $todo->setHomepage($properties['homepage']);
        }        
        if (array_key_exists('priority', $properties)) {
            $todo->setPriority($properties['priority']);
        }       
        if (array_key_exists('comment', $properties)) {
            $todo->setComment(trim($properties['comment']));
        }
        if (array_key_exists('dateAdded', $properties)) {
            $dateAdded = self::createDateTime($properties['dateAdded']);
            if ($dateAdded) {
                $todo->setDateAdded($dateAdded);
            }
        }   
        if (array_key_exists('deleted', $properties)) {
            $todo->setDeleted($properties['deleted']);
        }
    }
    
    public static function phpArrayFromTodo (Todo $todo)
    {
        /*
        $res = [
            'id' => $todo->getId(),
            'pre_name' => $todo->getPre_name(),
            'name' => $todo->getName(),
            'title' => $todo->getTitle(),
            'company' => $todo->getCompany(),
            'address' => $todo->getAddress(),
            'zip_city' => $todo->getZip_city(),
            'greeting_line' => $todo->getGreeting_line(),
            'business' => $todo->getBusiness(),
            'email' => $todo->getEmail(),
            'status' => $todo->getStatus(),       
            'homepage' => $todo->getHomepage(),
            'priority' => $todo->getPriority(),
            'comment' => $todo->getComment(),
            'dateAdded' => self::formatDateTime($todo->getDateAdded()),
            'deleted' => self::formatBoolean($todo->getDeleted()),
        ];     
         * 
         */
            
        $res = array(
            array(  'id','pre_name','name','title','company','address','zip_city','greeting_line',
                    'business','email','status','homepage','priority','comment', 'dateAdded','deleted'),
            array($todo->getId(),$todo->getPre_name(), $todo->getName(),$todo->getTitle(), $todo->getCompany(), $todo->getAddress(),
                $todo->getZip_city(), $todo->getGreeting_line(), $todo->getBusiness(), $todo->getEmail(), $todo->getStatus(),  
                $todo->getHomepage(), $todo->getPriority(), $todo->getComment(), self::formatDateTime($todo->getDateAdded()), self::formatBoolean($todo->getDeleted()))
        );
        return res;
    }

    private static function createDateTime($input) {
        return DateTime::createFromFormat('Y-n-j H:i:s', $input);
    }

}
