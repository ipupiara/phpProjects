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

$todo = Utils::getTodoByGetId();


Flash::addFlash('TODO csv successfully.');

Utils::redirect('detail', ['id' => $todo->getId()]);