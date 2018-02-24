<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TodoList;

use \TodoList\Dao\TodoDao;
use \TodoList\Flash\Flash;
use \TodoList\Util\Utils;
use \TodoList\Mapping\TodoMapper;

$todo = Utils::getTodoByGetId();
$todoPhpArray = TodoMapper::phpArrayFromTodo($todo);

header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=test.csv;");

$fp = fopen('php://output', 'w');
foreach ($todoPhpArray as $fields) {
    fputcsv($fp, $fields);
}
// fclose($fp);
fseek($fp, 0);
fpassthru($fp);

Flash::addFlash('TODO csv successfully.');
Utils::redirect('detail', ['id' => $todo->getId()]);