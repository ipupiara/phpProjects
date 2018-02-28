<?php


namespace TodoList;

use \TodoList\Dao\TodoDao;
use \TodoList\Dao\TodoSearchCriteria;
use \TodoList\Util\Utils;
use \TodoList\Validation\TodoValidator;


final class listClass    
{
    private $sortingField;
    private $sortingDirection;
    
        
    public function __construct() {
        $sortingField='';
        $sortingDirection='';
    }
    
    private function setSortingUi()
    {
        
    }

    public function handleSortingInput()
    {
        $sorting = NULL;
        if (array_key_exists('companySortButton', $_POST)) {
            $sorting = 'companySort';
        }
        if (array_key_exists('dateAddedSortButton', $_POST)) {
            $sorting= 'dateAddedSort';
        }
        if ($sorting != NULL) {
            TodoDao::setSorting($sorting);
            $this->setSortingUi();
        }
    }        

}


$listInstance = new listClass();
$listInstance->handleSortingInput();


$status = Utils::getUrlParam('status');
TodoValidator::validateStatus($status);

$dao = new TodoDao();
$search = (new TodoSearchCriteria())
        ->setStatus($status);

// data for template
$title = Utils::capitalize($status) . ' Apply Companies';
$todos = $dao->find($search);
$amtTodos = count($todos);
