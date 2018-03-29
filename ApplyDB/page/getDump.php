<?php


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

$cmd = "mysqldump -u ".$DBUSER ." --password=".$DBPASSWD. " " .$DATABASE ; 

//$cmd = "set";  

passthru( $cmd );



/*
 * 
 

backup_tables('localhost','817636','maenam22','817636');

function backup_tables($host,$user,$pass,$dbName,$tables = '*')
{
	
	$link = mysql_connect($host,$user,$pass);
	mysql_select_db($dbName,$link);
	
	//get all of the tables
	if($tables == '*')
	{
		$tables = array();
		$result = mysql_query('SHOW TABLES');
		while($row = mysql_fetch_row($result))
		{
			$tables[] = $row[0];
		}
	}
	else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}
	
	//cycle through
	foreach($tables as $table)
	{
		$result = mysql_query('SELECT * FROM '.$table);
		$num_fields = mysql_num_fields($result);
		
		$return.= 'DROP TABLE '.$table.';';
		$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) 
		{
			while($row = mysql_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j < $num_fields; $j++) 
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j < ($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}
	
	//save file
        
        $filename = "backup-" . date("d-m-Y") . ".txt";
        header('Content-Encoding: iso-8859-1');
        header('Content-Type: text/plain; charset= iso-8859-1');
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

        $handle = fopen('php://output', 'w');
//	$handle = fopen('db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql','w+');
	fwrite($handle,$return);
//	fclose($handle);
}
 * 
 */