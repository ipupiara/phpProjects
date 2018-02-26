<?php


namespace TodoList;

use \DateTime;
use \TodoList\Dao\TodoDao;
use \TodoList\Flash\Flash;
use \TodoList\Mapping\TodoMapper;
use \TodoList\Model\Todo;
use \TodoList\Util\Utils;
use \TodoList\Validation\TodoValidator;

$errors = [];
$todo = null;
$edit = array_key_exists('id', $_GET);
if ($edit) {
    $todo = Utils::getTodoByGetId();
} else {
    // set defaults / PN 25. feb 2018 but in constructor and never in UI code  (rules of OO-development!)
    // to avoid any redundancy  
    $todo = new Todo();
}

if (array_key_exists('cancel', $_POST)) {
    // redirect
    Utils::redirect('detail', ['id' => $todo->getId()]);
} elseif (array_key_exists('save', $_POST)) {
    // for security reasons, do not map the whole $_POST['todo']
    $data = [
        'title' => $_POST['todo']['title'],
        'pre_name' => $_POST['todo']['pre_name'],
        'name' => $_POST['todo']['name'],
        'company' => $_POST['todo']['company'],
        'address' => $_POST['todo']['address'],
        'zip_city' => $_POST['todo']['zip_city'],
        'greeting_line' => $_POST['todo']['greeting_line'],
        'email' => $_POST['todo']['email'],
        'homepage' => $_POST['todo']['homepage'],
        'business' => $_POST['todo']['business'],
        'priority' => $_POST['todo']['priority'],
        'comment' => $_POST['todo']['comment'],
        'dateAdded' => $_POST['todo']['dateAdded_date'] . ' ' . $_POST['todo']['dateAdded_hour'] . ':' . $_POST['todo']['dateAdded_minute'] . ':00',
    ];
        ;
    // map
    TodoMapper::map($todo, $data);
    // validate
    $errors = TodoValidator::validate($todo);
    // validate
    if (empty($errors)) {
        // save
        $dao = new TodoDao();
        $todo = $dao->save($todo);
        Flash::addFlash('TODO saved successfully.');
        // redirect
        Utils::redirect('detail', ['id' => $todo->getId()]);
    }
}
