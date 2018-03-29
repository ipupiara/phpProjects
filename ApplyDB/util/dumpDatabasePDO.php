<?php

function backupDB()
{
    $db_config = getDbConfigFromWordPress();
    if ($db_config === false) {
        unset($db_config);
        logMessage('Unable to get database configuration from WordPress', true, 'red');
        return false;
    }

    $new_backup_file = __DIR__ . DIRECTORY_SEPARATOR . 'newbackup_xxx_date.sql';
    if (is_file($new_backup_file) && is_writable($new_backup_file)) {
        @unlink($new_backup_file);
    } elseif (is_file($new_backup_file) && !is_writable($new_backup_file)) {
        logMessage('Unable to remove new backup SQL file. This is necessary to create backup SQL file.', true, 'red');
        return false;
    }
    unset($new_backup_file);

    $dbh = new \PDO('mysql:dbname=' . $db_config['dbname'] . ';host=' . $db_config['dbhost'] . ';charset=' . $db_config['dbcharset'], $db_config['dbuser'], $db_config['dbpassword']);
    $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
    $dbh->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);

    $sth = $dbh->prepare('SHOW TABLES');
    $sth->execute();
    $result = $sth->fetchAll(\PDO::FETCH_COLUMN);
    $tables = [];
    if (is_array($result) && !empty($result)) {
        foreach ($result as $row) {
            if (is_string($row) && stristr($row, $db_config['tableprefix']) !== false) {
                $tables[] = $row;
            } elseif (is_array($row) && array_key_exists(0, $row) && stristr($row[0], $db_config['tableprefix']) !== false) {
                $tables[] = $row[0];
            }
        }// endforeach;
        natcasesort($tables);
    }
    $sth->closeCursor();
    unset($result, $row, $sth);

    // begins export string header.
    $export_sql = '-- Manual backup SQL Dump'."\n\n";
    $export_sql .= 'SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";'."\n\n\n";
    $export_sql .= '--'."\n";
    $export_sql .= '-- Database: `' . $db_config['dbname'] . '`'."\n";
    $export_sql .= '--'."\n\n";
    unset($db_config);
    writeDownBackupDB($export_sql);
    unset($export_sql);

    // starting to loop thru tables.
    if (isset($tables) && is_array($tables)) {
        foreach ($tables as $table) {
            $export_sql = '-- --------------------------------------------------------'."\n\n";
            $export_sql .= '--'."\n";
            $export_sql .= '-- Table structure for table `' . $table . '`'."\n";
            $export_sql .= '--'."\n\n";
            $export_sql .= 'DROP TABLE IF EXISTS `' . $table . '`;'."\n";
            $sth = $dbh->prepare('SHOW CREATE TABLE `' . $table . '`');
            $sth->execute();
            $row = $sth->fetch(\PDO::FETCH_NUM);
            if (isset($row[1])) {
                $create_sql_string = $row[1];
                $create_sql_string = str_replace(['CREATE TABLE `'], ['CREATE TABLE IF NOT EXISTS `'], $create_sql_string);
                if (substr($create_sql_string, -1) != ';') {
                    $create_sql_string .= ' ;';
                }
            } else {
                $create_sql_string = '';
            }
            unset($row);
            $export_sql .= $create_sql_string."\n\n";
            $sth->closeCursor();
            unset($sth);
            writeDownBackupDB($export_sql);
            unset($export_sql);

            $export_sql = '--'."\n";
            $export_sql .= '-- Dumping data for table `' . $table . '`'."\n";
            $export_sql .= '--'."\n\n";
            writeDownBackupDB($export_sql);
            unset($export_sql);

            // get fields
            $sth = $dbh->prepare('SELECT * FROM `' . $table . '` LIMIT 1');
            $sth->execute();
            $result = $sth->fetch(\PDO::FETCH_ASSOC);
            if (is_array($result)) {
                $fields = array_keys($result);
            } else {
                $fields = [];
            }
            $sth->closeCursor();
            unset($result, $sth);

            // get fields type
            $sth = $dbh->prepare('DESCRIBE `' . $table . '`');
            $sth->execute();
            $table_columns = $sth->fetchAll();
            $columns = [];
            if (is_array($table_columns)) {
                foreach ($table_columns as $column) {
                    $columns[$column->Field] = [
                        'field' => $column->Field,
                        'type' => $column->Type,
                        'null' => $column->Null,
                        'default' => $column->Default,
                    ];
                }// endforeach;
                unset($column);
            }
            $sth->closeCursor();
            unset($sth, $table_columns);

            if (isset($fields) && is_array($fields) && !empty($fields)) {
                $select_string = 'SELECT ';
                $i_count_field = 1;
                foreach ($fields as $field) {
                    $select_string .= 'IF (`' . $field . '` IS NULL, \'FIELD_VALUE_NULL\', `' . $field . '`) AS `' . $field . '`';
                    if ($i_count_field < count($fields)) {
                        $select_string .= ', ';
                    }
                    $i_count_field++;
                }// endforeach;
                unset($i_count_field, $field);
                $select_string .= ' FROM `' . $table . '`';
                $sth = $dbh->prepare($select_string);
                unset($select_string);
                $sth->execute();
                $result = $sth->fetchAll();
                $export_sql = '';
                if (is_array($result) && !empty($result)) {
                    // generate INSERT INTO `table_name` string.
                    $export_sql .= 'INSERT INTO `' . $table . '` (';
                    $i_count = 1;
                    foreach ($fields as $field) {
                        $export_sql .= '`' . $field . '`';
                        if ($i_count < count($fields)) {
                            $export_sql .= ', ';
                        }
                        $i_count++;
                    }// endforeach;
                    unset($field, $i_count);
                    $export_sql .= ') VALUES'."\n";
                    writeDownBackupDB($export_sql);
                    unset($export_sql);

                    // generate VALUES of INSERT INTO.
                    if (is_array($result)) {
                        $i_count = 1;
                        $i_count_break = 1;
                        foreach ($result as $row) {
                            $export_sql = '(';
                            $i_count_fields = 1;
                            foreach ($fields as $field) {
                                $field_value = $row->{$field};
                                // escape slash
                                $field_value = str_replace('\\', '\\\\', $field_value);
                                // sanitize new line
                                $field_value = str_replace(["\r\n", "\r", "\n"], ['\r\n', '\r', '\n'], $field_value);
                                // escape single quote
                                $field_value = str_replace('\'', '\'\'', $field_value);
                                // change value to NULL if it is NULL.
                                if ($field_value === 'FIELD_VALUE_NULL') {
                                    $field_value = 'NULL';
                                }

                                // detect field value type and cloak with single quote.
                                if (isset($columns[$field]['type']) && 
                                    (
                                        stristr($columns[$field]['type'], 'tinyint(') !== false ||
                                        stristr($columns[$field]['type'], 'smallint(') !== false ||
                                        stristr($columns[$field]['type'], 'mediumint(') !== false ||
                                        stristr($columns[$field]['type'], 'int(') !== false ||
                                        stristr($columns[$field]['type'], 'bigint(') !== false
                                    )
                                ) {
                                    // this field column type is int
                                    if (!is_numeric($field_value) && $field_value !== 'NULL') {
                                        $field_value = '\'' . $field_value . '\'';
                                    }
                                } else {
                                    if ($field_value !== 'NULL') {
                                        $field_value = '\'' . $field_value . '\'';
                                    }
                                }

                                $export_sql .= $field_value;
                                unset($field_value);

                                if ($i_count_fields < count($fields)) {
                                    $export_sql .= ', ';
                                }
                                $i_count_fields++;
                            }// endforeach;
                            unset($field, $i_count_fields);
                            $export_sql .= ')';

                            if ($i_count < count($result)) {
                                if ($i_count_break >= 30) {
                                    $export_sql .= ';'."\n";
                                    writeDownBackupDB($export_sql);
                                    unset($export_sql);
                                    $i_count_break = 0;

                                    $export_sql = 'INSERT INTO `' . $table . '` (';
                                    $i_count_fields = 1;
                                    foreach ($fields as $field) {
                                        $export_sql .= '`' . $field . '`';
                                        if ($i_count_fields < count($fields)) {
                                            $export_sql .= ', ';
                                        }
                                        $i_count_fields++;
                                    }// endforeach;
                                    unset($field, $i_count_fields);
                                    $export_sql .= ') VALUES'."\n";
                                    writeDownBackupDB($export_sql);
                                    unset($export_sql);
                                    $export_sql = '';
                                } else {
                                    $export_sql .= ','."\n";
                                }
                            } else {
                                $export_sql .= ';'."\n\n";
                            }
                            $i_count++;
                            $i_count_break++;
                            writeDownBackupDB($export_sql);
                            unset($export_sql);
                        }// endforeach;
                        unset($i_count, $i_count_break, $result, $row);
                    }
                } else {
                    $export_sql .= "\n";
                    writeDownBackupDB($export_sql);
                    unset($export_sql);
                }
                unset($fields);
                $sth->closeCursor();
                unset($result, $sth);
            } else {
                $export_sql = "\n";
                writeDownBackupDB($export_sql);
                unset($export_sql);
            }
            unset($export_sql);
        }// endforeach;
        unset($table);
    }
    unset($tables);

    unset($dbh);
    logMessage('Backup DB completed. Max memory usage is ' . formatBytes(memory_get_peak_usage(true)) . '.', true, 'green');
    return true;
}// backupDB


/**
 * Write content to backup SQL file by append.
 * 
 * @param string $content
 */
function writeDownBackupDB($content)
{
    $new_backup_file = __DIR__ . DIRECTORY_SEPARATOR . 'newbackup_xxx_date.sql';
    $handle = fopen($new_backup_file, 'a+');
    fwrite($handle, $content);
    fclose($handle);
    unset($handle, $new_backup_file);
}// writeDownBackupDB


logMessage('Beginning backup DB.', true, 'light_gray');
backupDB();