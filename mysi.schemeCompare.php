<?php
    /* ************************************************************************************************************************************************************
     * ************************************************************************************************************************************************************
     Mysi Scheme Compare Tool 1.0 
     Created by Pablo E. Fernández Casado
     Licence MIT.
     Visite http://www.islavisual.com
     ************************************************************************************************************************************************************
     ************************************************************************************************************************************************************
     * EXAMPLE:
     * mysiSC::$_GET_VARIABLES = false;
     * mysiSC::compareDump('database1_name', 'database2_name');
    */

    include "mysi.php";

    class mysiSC {
        public static $_EXECUTE_ALTER_TABLE      = false;
        public static $_EXECUTE_CREATE_FUNCTION  = false;
        public static $_EXECUTE_CREATE_INDEX     = false;
        public static $_EXECUTE_CREATE_PROCEDURE = false;
        public static $_EXECUTE_CREATE_TABLE     = false;
        public static $_EXECUTE_CREATE_TRIGGER   = false;
        public static $_EXECUTE_CREATE_VIEW      = false;
        public static $_GET_VARIABLES            = false;
        public static $_DATATYPES                = "TABLE,VIEW,TRIGGER,FUNCTION,PROCEDURE,INDEX";

        public static function compareDump($db_ori, $db_dst){
            $mysql = new MySQL();
            $mysql->connect();
            $mysql2 = new MySQL();
            $mysql2->connect();
            $mysql->_ENABLED_LOG = false;
            $mysql2->_ENABLED_LOG = false;

            error_reporting(E_ALL^E_WARNING^E_NOTICE);

            // ****************************************************************************************************
            // RECOVER ORIGIN DATABASE INFORMATION --> $db_ori
            // ****************************************************************************************************

            // Get Estructure Database 1
            $mysql->query("SHOW TABLES FROM `$db_ori`");
            while($row = $mysql->fetchArray(MYSQL_NUM)){
                $namesTablesDB1[] = $row[0];

                // Guardamos los campos de la tabla
                $mysql2->query("SHOW COLUMNS FROM `$db_ori`.$row[0]");
                while($row2 = $mysql2->fetchArray(MYSQL_ASSOC)){
                    $arrFieldsDB1[$row[0]][] = $row2;
                }

                // Guardamos los índices de la tabla
                $mysql2->query("SHOW INDEX FROM `$db_ori`.$row[0]");
                while($row2 = $mysql2->fetchArray(MYSQL_ASSOC)){
                    $arrIndexDB1[$row[0]][] = $row2;
                }
            }

            // Guardamos las vistas de la la base de datos origen
            $mysql2->query("SELECT * FROM INFORMATION_SCHEMA.tables WHERE table_schema LIKE '".$db_ori."' AND LCASE(table_type)='view'");
            while($row2 = $mysql2->fetchArray(MYSQL_ASSOC)){
                $arrViewsDB1[] = $row2;
            }

            // Guardamos las funciones y procedimientos de la la base de datos origen
            $mysql2->query("SELECT * FROM INFORMATION_SCHEMA.ROUTINES WHERE routine_schema LIKE '".$db_ori."'");
            while($row2 = $mysql2->fetchArray(MYSQL_ASSOC)){
                $arrRoutinesDB1[] = $row2;
            }

            // Guardamos las triggers de la la base de datos origen
            $mysql2->query("SHOW TRIGGERS FROM `$db_ori`");
            while($row2 = $mysql2->fetchArray(MYSQL_ASSOC)){
                $arrTriggersDB1[] = $row2;
            }

            // *******************************************************************************************************
            // RECOVER DESTINATION DATABASE INFORMATION --> $db_dst
            // *******************************************************************************************************

            // Get Estructure Database 2
            $mysql->query("SHOW TABLES FROM `$db_dst`");
            while($row = $mysql->fetchArray(MYSQL_NUM)){
                $namesTablesDB2[] = $row[0];

                // Guardamos los campos de la tabla
                $mysql2->query("SHOW COLUMNS FROM `$db_dst`.$row[0]");
                while($row2 = $mysql2->fetchArray(MYSQL_ASSOC)){
                    $arrFieldsDB2[$row[0]][] = $row2;
                }

                // Guardamos los índices de la tabla
                $mysql2->query("SHOW INDEX FROM `$db_dst`.$row[0]");
                while($row2 = $mysql2->fetchArray(MYSQL_ASSOC)){
                    $arrIndexDB2[$row[0]][] = $row2;
                }
            }

            // Guardamos las vistas de la la base de datos destino
            $mysql2->query("SELECT * FROM INFORMATION_SCHEMA.tables WHERE table_schema LIKE '".$db_dst."' AND LCASE(table_type)='view'");
            while($row2 = $mysql2->fetchArray(MYSQL_ASSOC)){
                $arrViewsDB2[] = $row2;
            }

            // Guardamos las funciones y procedimientos de la la base de datos destino
            $mysql2->query("SELECT * FROM INFORMATION_SCHEMA.ROUTINES WHERE routine_schema LIKE '".$db_dst."'");
            while($row2 = $mysql2->fetchArray(MYSQL_ASSOC)){
                $arrRoutinesDB2[] = $row2;
            }

            // Guardamos las triggers de la la base de datos destino
            $mysql2->query("SHOW TRIGGERS FROM `$db_dst`");
            while($row2 = $mysql2->fetchArray(MYSQL_ASSOC)){
                $arrTriggersDB2[] = $row2;
            }

            // *******************************************************************************************************
            // COMPARE PROCESS
            // *******************************************************************************************************

            $diff = array();

            // Save the system variables.
            // --------------------------------------------------------------------------------------------------------

            if(self::$_GET_VARIABLES){
                $mysql2->query("SHOW VARIABLES;");
                while($row2 = $mysql2->fetchArray(MYSQL_ASSOC)){
                    $diff['variables'][$row2['Variable_name']] = $row2['Value'];
                }
            }

            // Look if the fields number is different
            // --------------------------------------------------------------------------------------------------------

            if(count($arrFieldsDB1) != count($arrFieldsDB2)) $diff['number_tables'] = "distinct";

            // If dont exists a table in destination database, write create instruction.
            // --------------------------------------------------------------------------------------------------------

            if(stripos(self::$_DATATYPES, "table") !== false){
                $createdTables = "";
                $arr_namesTablesDB2 = ";".implode(";", $namesTablesDB2).";";
                foreach($namesTablesDB1 as $table1){
                    if(stripos($arr_namesTablesDB2, ";".$table1.";") === false){
                         $def = $mysql->getValue("SHOW CREATE TABLE `$table1`", 1);

                         $query = "USE $db_dst; $def; ";
                         $diff['sql'][]= $query;
                         $createdTables .= ";".$table1.";";
                         if(self::$_EXECUTE_CREATE_TABLE) $mysql->query($query);
                    }
                }
            }

            // If a field dont exists inside table existing, write alter instruction and refill the array.
            // --------------------------------------------------------------------------------------------------------

            foreach($namesTablesDB1 as $table){
                for($x = 0; $x < count($arrFieldsDB1[$table]); $x++){
                    if(stripos(self::$_DATATYPES, "table") !== false){
                        foreach($arrFieldsDB1[$table][$x] as $key => $value){
                            $a1 = $arrFieldsDB1[$table][$x][$key];
                            $a2 = $arrFieldsDB2[$table][$x][$key];

                            if( $a1 != "" && $a2 != "" && $a1 != $a2 || ((!isset($a2) || $a2 == "") && $a1 != "" && strpos($createdTables, $table) === false)){
                                $col = $arrFieldsDB1[$table][$x]['Column_name'];
                                $diff['columns'][$table][$col][$key] = ($a1==NULL?"NULL":$a1)." != ".($a2==NULL?"NULL":$a2);

                                if($arrFieldsDB1[$table][$x]['Null'] == "NO") $null = "NOT NULL"; else $null = "NULL";
                                if($arrFieldsDB1[$table][$x]['Default'] == "NULL") $default = ""; else $default = "DEFAULT ".$arrFieldsDB1[$table][$x]['Default'];
                                if($arrFieldsDB1[$table][$x]['Extra'] == "AUTO_INCREMENT") $extra = "AUTO_INCREMENT"; else $extra = " ";

                                $diff['sql'][$table] = "ALTER TABLE `$db_dst`.`$table` CHANGE `".$arrFieldsDB1[$table][$x]['Field']."` `".$arrFieldsDB2[$table][$x]['Field']."` ".$arrFieldsDB1[$table][$x]['Type']." CHARACTER SET utf8 COLLATE utf8_general_ci $null $default $extra;";
                                if(self::$_EXECUTE_ALTER_TABLE) $mysql->query($diff['sql'][$table]);
                            }
                        }
                    }

                    if(stripos(self::$_DATATYPES, "index") !== false){
                        // The indexes only is showed the differences.
                        foreach($arrIndexDB1[$table][$x] as $key => $value){
                            $a1 = $arrIndexDB1[$table][$x][$key];
                            $a2 = $arrIndexDB2[$table][$x][$key];

                            if( $a1 != "" && $a2 != "" && $a1 != $a2){
                                $col = $arrIndexDB1[$table][$x]['Column_name'];
                                $diff['indexes'][$table][$col][$key] = ($a1==NULL?"NULL":$a1)." != ".($a2==NULL?"NULL":$a2);
                            }
                        }
                    }
                }
                //echo "<br><br>";
            }

            // If a procedure or function dont exists inside destination database, write create instruction and/or refill the array.
            // --------------------------------------------------------------------------------------------------------

            if(stripos(self::$_DATATYPES, "function") !== false || stripos(self::$_DATATYPES, "procedure") !== false){
                for($x = 0; $x < count($arrRoutinesDB1); $x++){
                    $inserted = false;
                    foreach($arrRoutinesDB1[$x] as $key => $value){
                        $a1 = $arrRoutinesDB1[$x][$key];
                        $a2 = $arrRoutinesDB2[$x][$key];
                        $name = $arrRoutinesDB1[$x]['ROUTINE_NAME'];
                        $type = $arrRoutinesDB1[$x]['ROUTINE_TYPE'];

                        if(($a1 != "" && $a2 != "" && $a1 != $a2) || ((!isset($a2) || $a2 == "") && $a1 != "")){
                            if((stripos(self::$_DATATYPES, "function") !== false && strtolower($type) == "function") ||
                               (stripos(self::$_DATATYPES, "procedure") !== false && strtolower($type) == "procedure")){
                                $diff['routines'][$name][$key] = ($a1==NULL?"NULL":$a1)." != ".($a2==NULL?"NULL":$a2);
                            }
                        }

                        if((!isset($a2) || $a2 == "") && $a1 != "" && !$inserted){
                            $def = $mysql->getValue("SHOW CREATE ".$type." ".$name, 2);

                            $query = "USE $db_dst; $def; ";
                            $diff['sql'][]= $query;
                            if    (self::$_EXECUTE_CREATE_FUNCTION  && strtolower($type) == "function" && stripos(self::$_DATATYPES, "function") !== false) $mysql->query($query);
                            elseif(self::$_EXECUTE_CREATE_PROCEDURE && strtolower($type) == "procedure" && stripos(self::$_DATATYPES, "procedure") !== false) $mysql->query($query);
                            $inserted = true;
                        }

                    }
                }
            } 

            // If a trigger dont exists inside destination database, write create instruction and/or refill the array.
            // --------------------------------------------------------------------------------------------------------

            if(stripos(self::$_DATATYPES, "trigger") !== false){
                for($x = 0; $x < count($arrTriggersDB1); $x++){
                    $inserted = false;

                    foreach($arrTriggersDB1[$x] as $key => $value){
                        $a1 = $arrTriggersDB1[$x][$key];
                        $a2 = $arrTriggersDB2[$x][$key];

                        if(($a1 != "" && $a2 != "" && $a1 != $a2) || ((!isset($a2) || $a2 == "") && $a1 != "")){
                            $col = $arrTriggersDB1[$x]['Trigger'];
                            $diff['triggers'][$col][$key] = ($a1==NULL?"NULL":$a1)." != ".($a2==NULL?"NULL":$a2);
                        }

                        if((!isset($a2) || $a2 == "") && $a1 != "" && !$inserted){
                            $name       = $arrTriggersDB1[$x]['Trigger'];
                            $statements = $arrTriggersDB1[$x]['Statement'];
                            $timing     = $arrTriggersDB1[$x]['Timing'];
                            $event      = $arrTriggersDB1[$x]['Event'];
                            $table      = $arrTriggersDB1[$x]['Table'];

                            $query = "USE $db_dst; CREATE TRIGGER $name $timing $event ON $table FOR EACH ROW $statements ;";
                            $diff['sql'][]= $query;
                            if(self::$_EXECUTE_CREATE_TRIGGER) $mysql->query($query);
                            $inserted = true;
                        }

                    }
                }
            }

            // If a view dont exists inside destination database, write create instruction and/or refill the array.
            // --------------------------------------------------------------------------------------------------------

            if(stripos(self::$_DATATYPES, "view") !== false){
                for($x = 0; $x < count($arrViewsDB1); $x++){
                    $inserted = false;

                    foreach($arrViewsDB1[$x] as $key => $value){
                        $a1 = $arrViewsDB1[$x][$key];
                        $a2 = $arrViewsDB2[$x][$key];

                        if(($a1 != "" && $a2 != "" && $a1 != $a2) || ((!isset($a2) || $a2 == "") && $a1 != "")){
                            $col = $arrViewsDB1[$x]['TABLE_NAME'];
                            $diff['views'][$col][$key] = ($a1==NULL?"NULL":$a1)." != ".($a2==NULL?"NULL":$a2);
                        }

                        if((!isset($a2) || $a2 == "") && $a1 != "" && !$inserted){
                            $name       = $arrViewsDB1[$x]['TABLE_NAME'];

                            $def = $mysql->getValue("SHOW CREATE VIEW ".$name, 1);

                            $query = "USE $db_dst; $def;";
                            $diff['sql'][]= $query;
                            if(self::$_EXECUTE_CREATE_VIEW) $mysql->query($query);
                            $inserted = true;
                        }

                    }
                }
            }

            return $diff;
        }
    }
?>
