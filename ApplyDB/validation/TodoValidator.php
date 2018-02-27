<?php


namespace TodoList\Validation;

use \TodoList\Exception\NotFoundException;
use \TodoList\Model\Todo;
use \TodoList\Dao\TodoDao;

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
        if (!$todo->getDateAdded()) {
            $errors[] = new \TodoList\Validation\ValidationError('dateAdded', 'Empty or invalid date added.',ValidationError::INVALID_DATEADDED);
        }
        if (!trim($todo->getPriority())) {
            $errors[] = new \TodoList\Validation\ValidationError('priority', 'Priority cannot be empty.',ValidationError::EMPTY_PRIORITY);
        } elseif (!self::isValidPriority($todo->getPriority())) {
            $errors[] = new \TodoList\Validation\ValidationError('priority', 'Invalid Priority set.', ValidationError::INVALID_PRIORITY);
        }
        if (!trim($todo->getStatus())) {
            $errors[] = new \TodoList\Validation\ValidationError('status', 'Status cannot be empty.', ValidationError::EMPTY_STATUS);
        } elseif (!self::isValidStatus($todo->getStatus())) {
            $errors[] = new \TodoList\Validation\ValidationError('status', 'Invalid Status set.', ValidationError::INVALID_STATUS);
        }
        if (!trim($todo->getCompany())) {
            $errors[] = new \TodoList\Validation\ValidationError('company', 'Company cannot be empty.');
        } else {
            // check when more than one company with the almost same name
            $amt = 0;
            if ($todo->getId()) { $amt = 1; }
            $cName = substr(trim($todo->getCompany()),0,5);
            $pos=strpos($cName," ");
            if ($pos !== false) {
               $cName = substr($cName,0,$pos);
            }
            if (TodoDao::checkMoreThanAmtCompanyWithNameLike($cName,$amt)) {
                $err = new \TodoList\Validation\ValidationError('company', 'More than one Company wit name like '.$cName, ValidationError::SIMILAR_COMPANY_CONFLICT, true);
                
                if (($_POST[$err->getErrorCheckboxName()] ) && ($_POST[$err->getErrorCheckboxName()])) {
     //               $errors[] = $err;
                }
                $errors[] = $err;
            }
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
