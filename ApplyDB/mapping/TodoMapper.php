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
        if (array_key_exists('uid', $properties)) {
            $todo->setId($properties['uid']);
        }
        if (array_key_exists('pre_name', $properties)) {
            $todo->setPriority($properties['pre_name']);
        }
        if (array_key_exists('dateAdded', $properties)) {
            $createdOn = self::createDateTime($properties['dateAdded']);
            if ($createdOn) {
                $todo->setCreatedOn($createdOn);
            }
        }
 
   
        
        
        
        if (array_key_exists('title', $properties)) {
            $todo->setTitle(trim($properties['title']));
        }
        if (array_key_exists('description', $properties)) {
            $todo->setDescription(trim($properties['description']));
        }
        if (array_key_exists('comment', $properties)) {
            $todo->setComment(trim($properties['comment']));
        }
        if (array_key_exists('status', $properties)) {
            $todo->setStatus($properties['status']);
        }
        if (array_key_exists('deleted', $properties)) {
            $todo->setDeleted($properties['deleted']);
        }
    }

    private static function createDateTime($input) {
        return DateTime::createFromFormat('Y-n-j H:i:s', $input);
    }

}
