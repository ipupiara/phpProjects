<?php


namespace TodoList\Dao;

use \DateTime;
use \Exception;
use \PDO;
use \PDOStatement;
use \TodoList\Config\Config;
use \TodoList\Exception\NotFoundException;
use \TodoList\Mapping\TodoMapper;
use \TodoList\Model\Todo;
use \TodoList\Dao\TodoSearchCriteria;

/**
 * DAO for {@link \TodoList\Model\Todo}.
 * <p>
 * It is also a service, ideally, this class should be divided into DAO and Service.
 */
final class TodoDao {

    const SIMILARTEXT_MIN_CHARS = 3;
    
    /** @var PDO */
    private $db = null;
    private static $orderField = 'dateAdded';
    private static $orderDirection = 'desc';
    
    
    private static function checkSortingVariables()
    {
        if ( ! isset($_SESSION['orderField'])) { 
            $_SESSION['orderField'] = TodoDao::$orderField;
        }  else  {
            TodoDao::$orderField = $_SESSION['orderField'];
        }
       if ( ! isset($_SESSION['orderDirection'])) { 
            $_SESSION['orderDirection'] = TodoDao::$orderDirection;
       } else {
           TodoDao::$orderDirection =  $_SESSION['orderDirection'];
       }
    }
    
    private static function saveSortingVariables()
    {
        $_SESSION['orderField'] = TodoDao::$orderField;
        $_SESSION['orderDirection'] = TodoDao::$orderDirection;
    }

    public function __destruct() {
        // close db connection
        $this->db = null;
    }
    
    public static function credentialsValid($uname,$pwd)
    {
        $result = false;
        $config = Config::getConfig('db');
        try {
            $dbx = new PDO($config['dsn'], $uname, $pwd);
            $result = true;
        } catch (Exception $ex) {
 //           throw new Exception('DB connection error: ' . $ex->getMessage());
        }
        return $result;
    }
    
    public static function checkMoreThanAmtCompanyWithNameLike($cName,$amt, &$todos)
    {
        try {
            $dao = new TodoDao();
            $sCrit = new TodoSearchCriteria();
            $search = $sCrit->setCompanyNamePart($cName);
            $todos = $dao->find($search);
            $amtTodos = count($todos);
            if ($amtTodos > $amt) {
                $result = true;
            }
        } catch (Exception $ex) {
            throw new Exception('error: ' . $ex->getMessage());
        }       
        return $result;
    }
    
    private static function todoNeedsInsert(Todo $todo)
    {
        $res = false;
        if ($todo->getId() === null) { 
            $res = true; 
        } else {
            $res = false;
        }
        return $res;
    }

    /**
     * Find all {@link Todo}s by search criteria.
     * @return array array of {@link Todo}s
     */
    public function find(TodoSearchCriteria $search = null) {
        $result = [];
        foreach ($this->query($this->getFindSql($search)) as $row) {
            $todo = new Todo();
            TodoMapper::map($todo, $row);
            $result[$todo->getId()] = $todo;
        }
        return $result;
    }

    /**
     * Find {@link Todo} by identifier.
     * @return Todo Todo or <i>null</i> if not found
     */
    public function findById($id) {
        $row = $this->query('SELECT * FROM applycompanies WHERE id = ' . (int) $id)->fetch();
        if (!$row) {
            return null;
        }
        $todo = new Todo();
        TodoMapper::map($todo, $row);
        return $todo;
    }

    /**
     * Save {@link Todo}.
     * @param Todo $todo {@link Todo} to be saved
     * @return Todo saved {@link Todo} instance
     */
    public function save(Todo $todo) {
        if (TodoDao::todoNeedsInsert($todo)) {
            return $this->insert($todo);
        }
        return $this->update($todo);
    }

    /**
     * @return PDO
     */
    private function getDb() {
        if ($this->db !== null) {
            return $this->db;
        }
        $config = Config::getConfig('db');
        try {
            $this->db = new PDO($config['dsn'], $_SESSION['username'], $_SESSION['password']);
        } catch (Exception $ex) {
            throw new Exception('DB connection error: ' . $ex->getMessage());
        }
        return $this->db;
    }
    
    private static function conditionLinkPrefix($first)
    {
        $res = '';
        if ($first) {
            $res.=' where ';                    
//            $first = false;    there are no ref parameters but value given on stack as copy, at least for booleans
            // so first must be set by the caller   :-(
        } else  {
            $res.=' and ';
        }
        return $res;
    }

    private function getFindSql(TodoSearchCriteria $search = null) {
        $first = true;
        $sql = 'SELECT * FROM applycompanies ';
        $orderBy=NULL;
        if ($search !== null) {
            if ($search->getStatus() !== null) {   
                $condPrefix = TodoDao::conditionLinkPrefix($first);
                $first = false;
                $sql .= $condPrefix. ' status = ' . $this->getDb()->quote($search->getStatus());
            }
            if ($search->getCompanyNamePart() !== null) {
                $condPrefix = TodoDao::conditionLinkPrefix($first);
                $first = false;
                $stringToMatch =  $this->getDb()->quote('%'.$search->getCompanyNamePart().'%');
                $sql .= $condPrefix.' company like ' . $stringToMatch;
            }     
        }
        $orderBy = TodoDao::$orderField.' '.TodoDao::$orderDirection;
        if ($orderBy != NULL )  {
            $sql .= ' ORDER BY ' . $orderBy;
        }
        return $sql;
    }

    /**
     * @return Todo
     * @throws Exception
     */
    private function insert(Todo $todo) {
        $now = new DateTime();
        $todo->setId(null);
        $todo->setDateAdded($now);
        $todo->setStatus(Todo::STATUS_PENDING);
          $sql = '
            INSERT INTO applycompanies (id,pre_name,name,title,company,address,zip_city,greeting_line,business,email,status,homepage,priority,comment,dateAdded,tempSortFloat)
                VALUES (:id,:pre_name,:name,:title,:company,:address,:zip_city,:greeting_line,:business,:email,:status,:homepage,:priority,:comment,:dateAdded,:tempSortFloat)';
 
        return $this->execute($sql, $todo);
    }

    /**
     * @return Todo
     * @throws Exception
     */
    private function update(Todo $todo) {
        $sql = '
            UPDATE applycompanies SET
                pre_name = :pre_name,
                name = :name,
                title = :title,
                company = :company,
                address = :address,
                zip_city = :zip_city,
                greeting_line = :greeting_line,
                business = :business,
                email = :email,
                status = :status,
                homepage = :homepage,      
                priority = :priority,
                comment = :comment,
                dateAdded = :dateAdded,
                tempSortFloat = :tempSortFloat
            WHERE
                id = :id';
        return $this->execute($sql, $todo);
    }

    /**
     * @return Todo
     * @throws Exception
     */
    private function execute($sql, Todo $todo) {
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, $this->getParams($todo));
        if (TodoDao::todoNeedsInsert($todo)) {
            return $this->findById($this->getDb()->lastInsertId());
        }   
//        if (!$statement->rowCount()) 
//                PN 16 feb 1018: above rowCount obviousely does not work all times (maybe would need to set a param on mysql ...?)
        if (!$this->findById($todo->getId())) {
            throw new NotFoundException('applycompany with ID "' . $todo->getId() . '" does not exist.');
        }
        return $todo;
    }

    private function getParams(Todo $todo) {
        $params = [
            ':id' => $todo->getId(),
            ':pre_name' => $todo->getPre_name(),
            ':name' => $todo->getName(),
            ':title' => $todo->getTitle(),
            ':company' => $todo->getCompany(),
            ':address' => $todo->getAddress(),
            ':zip_city' => $todo->getZip_city(),
            ':greeting_line' => $todo->getGreeting_line(),
            ':business' => $todo->getBusiness(),
            ':email' => $todo->getEmail(),
            ':status' => $todo->getStatus(),       
            ':homepage' => $todo->getHomepage(),
            ':priority' => $todo->getPriority(),
            ':comment' => $todo->getComment(),
            ':dateAdded' => self::formatDateTime($todo->getDateAdded()),
            ':tempSortFloat' => $todo->getTempSortFloat(),
        ];
        return $params;
    }

    private function executeStatement(PDOStatement $statement, array $params) {
        // XXX
        //echo str_replace(array_keys($params), $params, $statement->queryString) . PHP_EOL;
        if ($statement->execute($params) === false) {
            self::throwDbError($this->getDb()->errorInfo());
        }
    }

    /**
     * @return PDOStatement
     */
    private function query($sql) {
        $statement = $this->getDb()->query($sql, PDO::FETCH_ASSOC);
        if ($statement === false) {
            self::throwDbError($this->getDb()->errorInfo());
        }
        return $statement;
    }

    private static function throwDbError(array $errorInfo) {
        // TODO log error, send email, etc.
        throw new Exception('DB error [' . $errorInfo[0] . ', ' . $errorInfo[1] . ']: ' . $errorInfo[2]);
    }

    public static function formatDateTime(DateTime $date) {
        return $date->format('Y-m-d H:i:s');
    }

    public static function formatBoolean($bool) {
        return $bool ? 1 : 0;
    }
    
    public static function setSorting($sort,$likeString = NULL)
    {
        TodoDao::checkSortingVariables();
        if ($sort == 'dateAdded')  {
            if (TodoDao::$orderField == 'dateAdded') {
                if (TodoDao::$orderDirection == 'asc') {
                    TodoDao::$orderDirection = 'desc';
                } else {
                    TodoDao::$orderDirection = 'asc';
                }
            } else {
                TodoDao::$orderField = 'dateAdded';
                TodoDao::$orderDirection = 'desc';
            }
        }    
        if ($sort == 'company')  {
            if (TodoDao::$orderField == 'company') {
                if (TodoDao::$orderDirection == 'asc') {
                    TodoDao::$orderDirection = 'desc';
                } else {
                    TodoDao::$orderDirection = 'asc';
                }
            } else {
                TodoDao::$orderField = 'company';
                TodoDao::$orderDirection = 'asc';
            }            
        }
        if ($sort == 'similarTextSort')  {
            TodoDao::$orderField = 'tempSortFloat';
            TodoDao::$orderDirection = 'desc';
            $dao = new TodoDao();
            $dao->establishSimilarTextSort($likeString);
        }
        TodoDao::saveSortingVariables();
    }
    
    private function establishSimilarTextSort( $likeString)
    {
        $allApplies = $this->find();
        foreach ($allApplies as $apply) {
            $pctVal = 0.0;
            if (strlen($likeString) >= TodoDao::SIMILARTEXT_MIN_CHARS)  {
                similar_text ( strtolower ($likeString) , strtolower ($apply->getCompany()) ,$pctVal  );
            }
            $apply->setTempSortFloat($pctVal);
            $this->save($apply);
        }
        
    }
    
    public static function getSortingField()
    {       
        return TodoDao::$orderField;
        
    }
    
    
    public static function getSortingDirection()
    { 
        return TodoDao::$orderDirection;
    }

}
