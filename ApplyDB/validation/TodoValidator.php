<?php


namespace TodoList\Validation;

use \TodoList\Exception\NotFoundException;
use \TodoList\Model\Todo;

/**
 * Validator for {@link \TodoList\Model\Todo}.
 * @see \TodoList\Mapping\TodoMapper
 */
final class TodoValidator {

    private function __construct() {
    }

    /**
     * Validate the given {@link Todo} instance.
     * @param Todo $todo {@link Todo} instance to be validated
     * @return array array of {@link Error} s
     */
    public static function validate(Todo $todo) {
        $errors = [];
        if (!$todo->getCreatedOn()) {
            $errors[] = new \TodoList\Validation\ValidationError('createdOn', 'Empty or invalid Created On.');
        }
        if (!trim($todo->getPriority())) {
            $errors[] = new \TodoList\Validation\ValidationError('priority', 'Priority cannot be empty.');
        } elseif (!self::isValidPriority($todo->getPriority())) {
            $errors[] = new \TodoList\Validation\ValidationError('priority', 'Invalid Priority set.');
        }
        if (!trim($todo->getStatus())) {
            $errors[] = new \TodoList\Validation\ValidationError('status', 'Status cannot be empty.');
        } elseif (!self::isValidStatus($todo->getStatus())) {
            $errors[] = new \TodoList\Validation\ValidationError('status', 'Invalid Status set.');
        }
        if (!trim($todo->getCompany())) {
            $errors[] = new \TodoList\Validation\ValidationError('company', 'Company cannot be empty.');
        }
        return $errors;
    }

    /**
     * Validate the given status.
     * @param string $status status to be validated
     * @throws NotFoundException if the status is not known
     */
    public static function validateStatus($status) {
        if (!self::isValidStatus($status)) {
            throw new NotFoundException('Unknown status: ' . $status);
        }
    }

    /**
     * Validate the given priority.
     * @param int $priority priority to be validated
     * @throws NotFoundException if the priority is not known
     */
    public static function validatePriority($priority) {
        if (!self::isValidPriority($priority)) {
            throw new NotFoundException('Unknown priority: ' . $priority);
        }
    }

    private static function isValidStatus($status) {
        return in_array($status, Todo::allStatuses());
    }

    private static function isValidPriority($priority) {
        return in_array($priority, Todo::allPriorities());
    }

}
