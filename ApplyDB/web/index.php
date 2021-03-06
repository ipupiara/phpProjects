<?php

namespace TodoList;

use \TodoList\Exception\NotFoundException;
use \TodoList\Flash\Flash;
use \TodoList\Dao\TodoDao;

/**
 * Main application class.
 */
final class Index {

    const DEFAULT_PAGE = 'home';
    const PAGE_DIR = '../page/';
    const LAYOUT_DIR = '../layout/';

    private static $CLASSES = [
        'TodoList\Config\Config' => '/../config/Config.php',
        'TodoList\Flash\Flash' => '/../flash/Flash.php',
        'TodoList\Exception\NotFoundException' => '/../exception/NotFoundException.php',
        'TodoList\Dao\TodoDao' => '/../dao/TodoDao.php',
        'TodoList\Dao\TodoSearchCriteria' => '/../dao/TodoSearchCriteria.php',
        'TodoList\Mapping\TodoMapper' => '/../mapping/TodoMapper.php',
        'TodoList\Model\Todo' => '/../model/Todo.php',
        'TodoList\Validation\TodoValidator' => '/../validation/TodoValidator.php',
        'TodoList\Validation\ValidationError' => '/../validation/ValidationError.php',
        'TodoList\Util\Utils' => '/../util/Utils.php',
        'TodoList\Util\Mysqldump' => '/../util/Mysqldump.php',
    ];
    /**
     * System config.
     */
    public function init() {
        // error reporting - all errors for development (ensure you have display_errors = On in your php.ini file)
        error_reporting(E_ALL | E_STRICT);
        mb_internal_encoding('UTF-8');
        set_exception_handler([$this, 'handleException']);
        spl_autoload_register([$this, 'loadClass']);
        // session
        session_start();
         if (isset($_SESSION['credentialsValid'])) {
            
        } else {
            $_SESSION['credentialsValid'] = false;
            $_SESSION['username']="817636";
            $_SESSION['password']="";
        }
    }

    /**
     * Run the application!
     */
    public function run() {
        $this->runPage($this->getPage());
    }

    /**
     * Exception handler.
     */
    public function handleException($ex) {
        $extra = ['message' => $ex->getMessage()];
        if ($ex instanceof NotFoundException) {
            header('HTTP/1.0 404 Not Found');
            $this->runPage('404', $extra);
        } else {
            // TODO log exception
            header('HTTP/1.1 500 Internal Server Error');
            $this->runPage('500', $extra);
        }
    }

    /**
     * Class loader.
     */
    public function loadClass($name) {
        if (!array_key_exists($name, self::$CLASSES)) {
            die('Class "' . $name . '" not found.');
        }
        require_once __DIR__ . self::$CLASSES[$name];
    }
    
    

    private function getPage() {
        $page = self::DEFAULT_PAGE;
        /*
         * decide here if login credentials in $session are valid
         * if no 
         *      check if $get contains user/pwd values (as below for page value in $_GET) 
         *      update  user in $session
         *      and check if credentials are valid now, 
         *      update $session->credentialsValid
         *          update in $session pwd if pwd is valid,
         * 
         * if credentials are still no valid set $page to default_page (with user/password dialog)
         * else allow the user-desired page to be loaded
         *          
         */
        if (array_key_exists('page', $_GET) && ($_GET['page'] == "homeLogout")) {
                 $_SESSION['password'] = "";   
                 $_SESSION['credentialsValid'] = false;  
        } else {
            if (array_key_exists('username', $_POST)) {
                $uname = $_POST['username'];
                $pwd = $_POST['password'];
                $_SESSION['username'] = $uname;
                if (TodoDao::credentialsValid($uname,$pwd)) {
                    $_SESSION['password'] = $pwd;
                    $_SESSION['credentialsValid'] = true;
                } else {
                    $_SESSION['password'] = "";
                    $_SESSION['credentialsValid'] = false;    
                    Flash::addFlash("credentials not valid");
                }
            }
            if ($_SESSION['credentialsValid']) {
                if (array_key_exists('page', $_GET)) {
                     $page = $_GET['page'];
                }
            }
        }
        return $this->checkPage($page);
    }

    private function checkPage($page) {
        if (!preg_match('/^[a-z0-9-]+$/i', $page)) {
            // TODO log attempt, redirect attacker, ...
            throw new NotFoundException('Unsafe page "' . $page . '" requested');
        }
        if (!$this->hasScript($page)
                && !$this->hasTemplate($page)) {
            // TODO log attempt, redirect attacker, ...
            throw new NotFoundException('Page "' . $page . '" not found');
        }
        return $page;
    }

    private function runPage($page, array $extra = []) {
        $run = false;
        if ($this->hasScript($page)) {
            $run = true;
            require $this->getScript($page);
        }
        if ($this->hasTemplate($page)) {
            $run = true;
            // data for main template
            $template = $this->getTemplate($page);
            $flashes = null;
            if (Flash::hasFlashes()) {
                $flashes = Flash::getFlashes();
            }

            // main template (layout)
            require self::LAYOUT_DIR . 'index.phtml';
        }
        if (!$run) {
            die('Page "' . $page . '" has neither script nor template!');
        }
    }

    private function getScript($page) {
        return self::PAGE_DIR . $page . '.php';
    }

    private function getTemplate($page) {
        return self::PAGE_DIR . $page . '.phtml';
    }

    private function hasScript($page) {
        return file_exists($this->getScript($page));
    }

    private function hasTemplate($page) {
        return file_exists($this->getTemplate($page));
    }
     
}

$index = new \TodoList\Index();
$index->init();
// run application!
$index->run();
