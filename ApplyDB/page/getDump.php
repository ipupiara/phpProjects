<?php

namespace TodoList;


use TodoList\Util\Mysqldump;



 // To change this license header, choose License Headers in Project Properties.
 // To change this template file, choose Tools | Templates
 // and open the template in the editor.
 

$DBUSER="817636";
$DBPASSWD="maenam22";
//$DBUSER="root";
//$DBPASSWD="14u24me";
$DATABASE="817636";


$filename = "backup-" . date("d-m-Y") . ".txt";
header('Content-Encoding: iso-8859-1');
header('Content-Type: text/plain; charset= iso-8859-1');
header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

 $handle = fopen('php://output', 'w');
 
 fwrite($handle,"hello from backup");

$dumpSettings = array(
    'exclude-tables' => array(),
    'compress' => Mysqldump::NONE,
    'no-data' => false,
    'add-drop-table' => true,
    'single-transaction' => true,
    'lock-tables' => true,
    'add-locks' => true,
    'extended-insert' => false,
    'disable-keys' => true,
    'skip-triggers' => false,
    'add-drop-trigger' => true,
    'routines' => true,
    'databases' => false,
    'add-drop-database' => false,
    'hex-blob' => true,
    'no-create-info' => false,
    'where' => ''
    );

$dump = new Mysqldump(
    "mysql:host=localhost;dbname=".$DATABASE,
    $DBUSER,
    $DBPASSWD,
    $dumpSettings);
$dump->start();

