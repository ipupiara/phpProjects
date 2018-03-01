<?php


namespace TodoList;

use \TodoList\Dao\TodoDao;
use \TodoList\Model\Todo;
use \TodoList\Dao\TodoSearchCriteria;
use \TodoList\Util\Utils;
use \TodoList\Validation\TodoValidator;
use \TodoList\Flash\Flash;

final class listClass    
{
    private $dateAddedArrow;
    private $companyArrow;
    private $similarTextIcon;
    private $dateAddedButtonClass;
    private $companyButtonClass;
    private $similarTextButtonClass;
        
    public function __construct() {
        $this->dateAddedArrow='';
        $this->companyArrow = '';
        $this->similarTextIcon = '';
    }
    
    private function setSortingUi()
    {
        if (TodoDao::getSortingField() == 'dateAdded')  {
           $this->companyArrow ='';
           $this->similarTextIcon = '';
           $this->companyButtonClass = 'submitClear';
           $this->similarTextButtonClass= 'submitClear'; 
           $this->dateAddedButtonClass = 'submit';   
           if (TodoDao::getSortingDirection() == 'asc') {
               $this->dateAddedArrow = 'arrow_upward';
           } else {
               $this->dateAddedArrow = 'arrow_downward';
           } 
        } elseif (TodoDao::getSortingField() == 'company')  {
           $this->dateAddedArrow ='';
           $this->similarTextIcon = '';
           $this->companyButtonClass = 'submit';
           $this->dateAddedButtonClass = 'submitClear'; 
           $this->similarTextButtonClass= 'submitClear'; 
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

    public function getSimilarTextButtonClass()
    {
        return $this->similarTextButtonClass;
    }
    
    public function getSimilarTextIcon()
    {
        return $this->similarTextIcon;
    }
 /*
  * 
  * <!---                               <td class= aligncenter> <i class="material-icons multi"><?php echo $listInstance->getSimilarTextIcon()    ?></i>  </td> -->
  *  <td><input type="text" name="similarText" value= "<?php echo $similarText ?>"    /></td>
  * 
  */   
    public function handleSortingInput()
    {
        $sorting = NULL;
        if (array_key_exists('companySortButton', $_POST)) {
            $sorting = 'company';
        } elseif (array_key_exists('dateAddedSortButton', $_POST)) {
            $sorting= 'dateAdded';
        } elseif (array_key_exists('similarTextSortButton', $_POST))  {
            if (array_key_exists('similarTextSortButton', $_POST)) {
                Flash::addFlash("text was added");
            }  else {
                Flash::addFlash("no text");
            }
        }
        if ($sorting != NULL) {
            TodoDao::setSorting($sorting);
        }   
        $this->setSortingUi();
        
 //       $this->setSortingUi();
    }        

}


$listInstance = new listClass();
$listInstance->handleSortingInput();
$similarText = "";

 $status = Utils::getUrlParam('status');
 TodoValidator::validateStatus($status);
if ($status == TODO::STATUS_ALL) {
    $search = NULL;
} else {
    $search = (new TodoSearchCriteria())
        ->setStatus($status);
}
$dao = new TodoDao();
// data for template
$title = Utils::capitalize($status) . ' Apply Companies';
$todos = $dao->find($search);
$amtTodos = count($todos);
