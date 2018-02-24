<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TodoList;

use \TodoList\Flash\Flash;
use \TodoList\Util\Utils;
use \TodoList\Mapping\TodoMapper;

$todo = Utils::getTodoByGetId();
$todoPhpArray = TodoMapper::phpArrayFromTodo($todo);

 
header('Content-Encoding: iso-8859-1');
header('Content-Type: application/csv; charset=iso-8859-1');
header('Content-Disposition: attachement; filename="test.csv";');

$fp = fopen('php://output', 'w');
foreach ($todoPhpArray as $fields) {
    fputcsv($fp, $fields);
}



/*Flash::addFlash('TODO csv successfully.');
Utils::redirect('detail', ['id' => $todo->getId()]); */