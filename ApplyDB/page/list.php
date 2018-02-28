<?php


namespace TodoList;

use \TodoList\Dao\TodoDao;
use \TodoList\Dao\TodoSearchCriteria;
use \TodoList\Util\Utils;
use \TodoList\Validation\TodoValidator;


final class listClass    
{
    private $dateAddedArrow;
    private $companyArrow;
    private $dateAddedButtonClass;
    private $companyButtonClass;
        
    public function __construct() {
        $this->dateAddedArrow='';
        $this->companyArrow = '';
    }
    
    private function setSortingUi()
    {
        if (TodoDao::getSortingField() == 'dateAdded')  {
           $this->companyArrow ='';
           $this->companyButtonClass = 'submitClear';
           $this->dateAddedButtonClass = 'submit';   
           if (TodoDao::getSortingDirection() == 'asc') {
               $this->dateAddedArrow = 'arrow_upward';
           } else {
               $this->dateAddedArrow = 'arrow_downward';
           } 
        }  else {
           $this->dateAddedArrow ='';
           $this->companyButtonClass = 'submit';
           $this->dateAddedButtonClass = 'submitClear';   
           if (TodoDao::getSortingDirection() == 'asc') {
               $this->companyArrow ='arrow_upward';
           } else {
               $this->companyArrow ='arrow_downward'; 
           }            
        }
    }
    
    public function getCompanyArrow () 
    {
        return $this->companyArrow;
    }
    
    public function getDateAddedArrow () 
    {
        return $this->dateAddedArrow;
    }
    
    public function getDateAddedButtonClass()
    {
        return $this->dateAddedButtonClass;
    }
    
    public function getCompanyButtonClass()
    {
        return $this->companyButtonClass;
    }

    public function handleSortingInput()
    {
        $sorting = NULL;
        if (array_key_exists('companySortButton', $_POST)) {
            $sorting = 'company';
        }
        if (array_key_exists('dateAddedSortButton', $_POST)) {
            $sorting= 'dateAdded';
        }
        if ($sorting != NULL) {
            TodoDao::setSorting($sorting);
        }
        $this->setSortingUi();
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
