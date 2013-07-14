<?php
    /* ************************************************************************************************************************************************************
     * ************************************************************************************************************************************************************
     Mysi MySQL Class 1.0 
     Created by Pablo E. Fernández Casado
     Licence MIT.
     Visite http://www.islavisual.com
     ************************************************************************************************************************************************************
     ************************************************************************************************************************************************************
    */

    // Clase Mysql
    include "mysi.getInfo.php";
    
    class mysi{
        public    $resource;
        private   $total_queries = 0;
        
        private   $_DATABASE_NAME_DEVELOPMENT = '';
        private   $_USER_DEVELOPMENT          = '';
        private   $_PASS_DEVELOPMENT          = '';
        
        private   $_DATABASE_NAME_PRODUCTION  = '';
        private   $_USER_PRODUCTION           = '';
        private   $_PASS_PRODUCTION           = '';
        
        const     _TOKEN_KEY                  = 'date("Y-m-d H:i:s", $_SERVER["REQUEST_TIME"]);';
        var       $_ENCODED_TOKEN             = "";            //  Contains the token generated.
        
        var       $_IGNORE_ERRORS             = '1062';        // Is a list of Numbers of errors comma separated MySQL that will be managed in a special way. If you want to know what and how errors manage, you can to see the http://dev.mysql.com/doc/refman/5.0/es/error-handling.html URL.
        var       $_WARNING_COLOR             = 'orange';      // Color to show the WARNING messages.
        var       $_ERROR_COLOR               = 'red';         // Color to show the ERROR messages.
        var       $_SHOW_WARNING_ERROR        = true;          // If this variable is set to TRUE WARNING messages are displayed.
        var       $_SHOW_IGNORED_ERRORS       = false;         // If this variable is set to FALSE IGNORED messages aren't displayed. Otherwise, the messages, will be displayed as the others.
        var       $_SHOW_CONTROL_MESSAGES     = true;          // If this variable is set to TRUE ERROR messages are displayed.
        var       $_STOP_WARNING_ERROR        = false;         // If a warning error is found and this variable is seted to TRUE, the application execution  is force to die().
        
        const     _SEPARADOR_SQL              = ";\n";         // Separator for executing multiple statements. At runtime, are separated by this value and then executed one by one.
        var       $_FORMAT_DATETIME_DB        = "Y-m-d H:i:s"; // Datetime format that has set in the database. By default set to AMERICAN FORMAT 1970-01-01 1:00:00.
        var       $_FORMAT_DATE_DB            = "Y-m-d";       // Date format that has set in the database. By default set to AMERICAN FORMAT 1970-01-01.
        var       $_FORMAT_DATETIME_FRMWRK    = "d-m-Y H:i:s"; // Datetime format you want to use in the Framework. By default set to FORMAT 31-12-1970 00:00:00.
        var       $_FORMAT_DATE_FRMWRK        = "d-m-Y";       // Datetime format you want to use in the Framework. By default set to FORMAT 31-12-1970.
        
        public    $_EMPTY_FIELD_BY_DEFAULT    = "";            // If the query returns no results will return the value set for this variable. Usually will be NULL or "".
        public    $_UTF8_ENCODE               = false;         // The results extracted from MySQL will become default to UTF-8 if is set to TRUE. If it does nothing.
        public    $_CHARSET_PREFERRED         = "utf8";        // Charset set by default.
        
        var       $selected_rows              = 0;             // Variable that contains number of selected rows for last executed SELECT sentence.
        var       $affected_rows              = 0;             // Variable that contains number of affected rows for last executed UPDATE, INSERT or DELETE sentence.
        var       $last_insert_id             = 0;             // Variable that contains the last ID inserted
        var       $last_query                 = "";            // Variable that contains the last query executed
        var       $last_error_id              = 0;             // Variable that contains ERROR NUMBER that provoked the last query executed
        var       $last_error_msg             = "";            // Variable that contains ERROR MESSAGE that provoked the last query executed
        
        protected $execStartTime              = 0;             // Is used to save the initial time of a query. Never change this property
        protected $execEndTime                = 0;             // Is used to save the ended time of a query. Never change this property
        var       $completedIn                = 0;             // Is used to save the transcurred time of a query. Never change this property
        
        // -------------------------------
        // VARIABLES DEL LOG DE EVENTOS
        // -------------------------------
        public    $_ENABLED_LOG               = false;         // If this variable is set to TRUE is saves in the database a log entry every page that is accessed. If FALSE, it does nothing.
        private   $_LOG_TABLE_CREATE_AUTO     = true;          // Indicates whether to create the table automatically if the database is not created at the time of the call or execution.
        private   $_LOG_TABLE_NAME            = "dblog";       // Is the name of the table that has the configuration of LOG's table.
        var       $_SIZE_LOG_IN_DAYS          = 30;            // LOG size in days. The default is 30 days. If set to zero means you do not want to delete any entry in the log.
        var       $_SAVE_QUERIES_IN_LOG       = false;         // If this variable is set to TRUE, all queries are saved automatically in MySQL. Otherwise, only events that request it will be saved if is doing directly through insertEntryLog function (...)
        private   $_LOG_TABLE_DEF             = "DROP TABLE IF EXISTS `<table_log>`; CREATE TABLE IF NOT EXISTS `<table_log>` ( `Id` bigint(20) NOT NULL auto_increment, `fecha` datetime NOT NULL default '0000-00-00 00:00:00', `evento` longtext collate utf8_bin NOT NULL, `pagina` varchar(255) collate utf8_bin default NULL, `ip` varchar(15) collate utf8_bin default NULL, `so` varchar(50) collate utf8_bin default NULL, `browser` varchar(255) collate utf8_bin default NULL, `host` varchar(255) collate utf8_bin default NULL, PRIMARY KEY  (`Id`) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=15 ;";
        
        var       $_NAMES_MONTH = array('JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER',
                    'ENERO', 'FEBBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE',
                    'JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC', 
                    'ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC', 
                  );
                     
        function __construct(){
            if($_SERVER['HTTP_HOST'] == "localhost"){
                $resource=(mysql_connect($_SERVER['HTTP_HOST'], $this->_USER_DEVELOPMENT, $this->_PASS_DEVELOPMENT)) or die(mysql_error());
                mysql_select_db($this->_DATABASE_NAME_DEVELOPMENT, $resource) or die(mysql_error());
            } else {
                $resource=(mysql_connect("localhost", $this->_USER_PRODUCTION, $this->_PASS_PRODUCTION)) or die(mysql_error());
                mysql_select_db($this->_DATABASE_NAME_PRODUCTION, $resource) or die(mysql_error());
            }
            
            mysql_query("SET NAMES '".$this->_CHARSET_PREFERRED."'",$resource);
            mysql_query("SET CHARACTER SET ".$this->_CHARSET_PREFERRED, $resource);
            if($this->_CHARSET_PREFERRED == "utf8") 
                mysql_query("SET SESSION collation_connection = '".$this->_CHARSET_PREFERRED."_unicode_ci'", $resource);
            else {
                mysql_query("SET SESSION collation_connection = '".$this->_CHARSET_PREFERRED."'", $resource);
            }
            
            if($this->_ENABLED_LOG){
                $result = mysql_query("SHOW TABLES LIKE '".$this->_LOG_TABLE_NAME."';");
                $this->last_query = "SHOW TABLES LIKE '".$this->_LOG_TABLE_NAME."';";
                $row = mysql_fetch_row($result);
                $row = $row[0];
    
                if($row == $this->_EMPTY_FIELD_BY_DEFAULT){
                    $resultCreate = $this->createTableLog();
                }
                
                // Eliminamos los registros sobrantes.
                if($this->_SIZE_LOG_IN_DAYS != 0){
                    $result = mysql_query("DELETE FROM ".$this->_LOG_TABLE_NAME." WHERE fecha < DATE_SUB(NOW(),INTERVAL ".$this->_SIZE_LOG_IN_DAYS." DAY)");
                    $this->last_query = "DELETE FROM ".$this->_LOG_TABLE_NAME." WHERE fecha < DATE_SUB(NOW(),INTERVAL ".$this->_SIZE_LOG_IN_DAYS." DAY)";
                }    
            }
        }
        
        // ************************************************************************************************************************************************************
        // ************************************************************************************************************************************************************
        // FUNCIONES PARA EL CONTROL DE TRANSFERENCIA DE DATOS.
        // SE USARÁ PARA CONTROLAR QUE NO SE ENVÍE A LA BBDD 2 VECES LA MISMA SENTENCIA SQL.
        // TAMBIÉN SE PUEDE USAR PARA EL GESTIONAR LA SEGURIDAD DE LAS TRANSFERENCIAS ENTRE EL SERVIDOR Y MYSQL.
        // ************************************************************************************************************************************************************
        // ************************************************************************************************************************************************************
        
        // ------------------------------------------------------------------------------------------------------------
        // FUNCIÓN PARA CODIFICAR O CREAR TOKENS. SE BASA EL LA CONSTAMTE _TOKEN_KEY PARA CODIFICAR LA CADENA ENVIADA.
        // ------------------------------------------------------------------------------------------------------------
        
        function encodeToken($string) {
            eval("\$auxToken = ".mySQL::_TOKEN_KEY);
            eval("\$token_key = '".$auxToken."';");
            
            $token = '';
            for($i=0; $i<strlen($string); $i++) {
                $char = substr($string, $i, 1);
                $keychar = substr($token_key, ($i % strlen($token_key))-1, 1);
                $char = chr(ord($char)+ord($keychar));
                $token.=$char;
            }
            $this->_ENCODED_TOKEN = $token;
            return base64_encode($token);
        }
        
        // ------------------------------------------------------------------------------------------------------------
        // FUNCIÓN PARA DECODIFICAR TOKENS.
        // ------------------------------------------------------------------------------------------------------------
        
        function decodeToken($token) {
            eval("\$auxToken = ".mySQL::_TOKEN_KEY);
            eval("\$token_key = '".$auxToken."';");
            
            $token = base64_decode($token);
            $string = '';
            for($i=0; $i<strlen($token); $i++) {
                $char = substr($token, $i, 1);
                $keychar = substr($token_key, ($i % strlen($token_key))-1, 1);
                $char = chr(ord($char)-ord($keychar));
                $string.=$char;
            }
            return $string;
        }
        
        // ------------------------------------------------------------------------------------------------------------
        // FUNCIÓN QUE COMPARA UN TOKEN ENVIADO POR $token CON EL TOKEN CREADO A PARTIR DE $string. 
        // SE BASA EL LA CONSTAMTE _TOKEN_KEY PARA CODIFICAR LA CADENA ENVIADA.
        // SI $token ES VACÍO TOMA COMO TOKEN PARA COMPARAR EL DEVUELTO POR LA VARIABLE _ENCODED_TOKEN.
        // ------------------------------------------------------------------------------------------------------------
        // Devuelve TRUE si son iguales. En cualquier otro caso devuelve FALSE.
        // ------------------------------------------------------------------------------------------------------------
        
        function checkToken($string, $token=""){
            $current_token = $token;
            if($current_token == "") $current_token = $this->_ENCODED_TOKEN;
            
            $aux = $this->createToken($string);
            if($aux == $current_token) return true;
            
            return false;
        }
        
        // ------------------------------------------------------------------------------------------------------------
        // FUNCIÓN QUE COMPARA 2 TOKENS 
        // SI $token2 ES VACÍO TOMA COMO TOKEN PARA COMPARAR EL DEVUELTO POR LA VARIABLE _ENCODED_TOKEN.
        // ------------------------------------------------------------------------------------------------------------
        // Devuelve TRUE si son iguales. En cualquier otro caso devuelve FALSE.
        // ------------------------------------------------------------------------------------------------------------
        
        function compareTokens($token1, $token2=""){
            if($token2 == "") $token2 = $this->_ENCODED_TOKEN;
            
            if($token1 == $token2) return true;
            
            return false;
        }
        
        // ------------------------------------------------------------------------------------------------------------
        // FUNCIÓN COMPRUEBA QUE EL TOKEN INTRODUCIDO NO ESTÁ EN LA TABLA REFERENCIADA POR _LOG_TABLE_NAME DE LA BBDD.
        // ------------------------------------------------------------------------------------------------------------
        // Devuelve TRUE si son iguales. En cualquier otro caso devuelve FALSE.
        // ------------------------------------------------------------------------------------------------------------
        
        function existsToken($token=""){
            $current_token = $token;
            if($current_token == "") $current_token = $this->_ENCODED_TOKEN;
            
            $result = mysql_query("SELECT id FROM ".$this->_LOG_TABLE_NAME." WHERE token = '".$current_token."'");
            $this->last_query = "SELECT id FROM ".$this->_LOG_TABLE_NAME." WHERE token = '".$current_token."'";
            $row = mysql_fetch_row($result);
            $row = $row[0];
            
            if($row != $this->_EMPTY_FIELD_BY_DEFAULT){
                return true;
            }
            
            return false;
        }
        
        // ************************************************************************************************************************************************************
        // ************************************************************************************************************************************************************
        // FUNCIONES PARA LA GESTIÓN DEL LOG
        // SE UTILIZA PARA EL CONTROL DE EVENTOS EN EL SISTEMA Y GUARDAR EL HISTORIAL DE CONSULTAS QUE SE REALIZARON EN LOS
        // ÚLTIMOS '_SIZE_LOG_IN_DAYS' DÍAS, SIEMPRE Y CUANDO _SAVE_QUERIES_IN_LOG ESTÉ true. POR DEFECTO SON 30 DÍAS.
          // ************************************************************************************************************************************************************
        // ************************************************************************************************************************************************************
        
        // -----------------------------------------------------------------------------------------------------------------
        // FUNCIÓN QUE DEVUELVE EL BROWSER, VERSION, IP Y SO DEL CLIENTE QUE ACCEDE A LA PÁGINA. 
        // -----------------------------------------------------------------------------------------------------------------
        // Necesita de la clase "getInfo" para alimentarse. Por ello si se usa es necesario incluir la línea de código:
        // EJ.: include "getInfo.class.php";
        // -----------------------------------------------------------------------------------------------------------------
        
        public function getInfo(){
               $mysiGI = new mysiGI;
            $aux = $mysiGI->getInfo();
            return array(
                'browser'          => $aux['browser'],
                'version'          => $aux['version'],
                'so'              => $aux['so'],
                'ip'            => $aux['ip'],
                'page'             => $_SERVER['REQUEST_URI'],
                'host'            => $_SERVER['SERVER_SOFTWARE'].", ".$_SERVER['SERVER_PROTOCOL'].", ".$_SERVER['SERVER_NAME'],
                'added_info'    => ''
            );
        }
        
        // -----------------------------------------------------------------------------------------------------------------
        // FUNCIÓN QUE CREA LA TABLA DE LOG's. 
        // -----------------------------------------------------------------------------------------------------------------
        
        public function createTableLog(){
            $query = $this->_LOG_TABLE_DEF;
            $query = @ereg_replace('<table_log>', $this->_LOG_TABLE_NAME, $query);
            $result = mysql_query($query);
            $this->last_query = $query;
            
            return $result;
        }
        
                private static function classAllowed($rights, $class, $join_plus){
                    $class = strtoupper($class);
                    if(strlen($class) > 4) $class = substr($class, 0, 4);
                    $posi = stripos($rights, $class); 

                    if($posi !== false){
                        $posi--;
                        if(substr($rights, $posi, 1) == $join_plus ){
                           return true;
                        } else {
                           return false;
                        }
                    } else {
                        if(substr($rights, 0, 4) != "NONE") 
                            return true;
                        else
                            return false;
                    }
                }
                
        // -----------------------------------------------------------------------------------------------------------------
        // FUNCIÓN QUE INSERTA EVENTOS EN LA TABLA DE LOG's. 
        // -----------------------------------------------------------------------------------------------------------------
        
        public function insertEntryLog($event=''){
            $call = debug_backtrace();
            
            if($event == ''){
                                for($xCount = 2; $xCount < 10; $xCount++){
                                    if( substr($call[$xCount]['function'], 0, 7) != "include") break;
                                }
                
                //if(substr($call[$xCount]['function'], 0, 7) == "include") $xCount++;

                                $_CLASS_ALLOWED = self::classAllowed($GLOBALS['_USER_LOG_MODE'], $call[$xCount]['class'], $GLOBALS['_DEBUG_JOIN_PLUS']);
                                
                                if($_CLASS_ALLOWED || $this->last_error_id != ""){
//                                    echo $call[$xCount]['class']." -> ".$call[$xCount]['function']."\n";
                                    $result = mysql_query("INSERT INTO ".$this->_LOG_TABLE_NAME." (user_id, fecha, pagina, linea, funcion, params, evento, conexion) VALUES ('".$GLOBALS['_USER_ID']."', '".date("Y-m-d H:i:s", $_SERVER["REQUEST_TIME"])."', '".mysql_real_escape_string($call[$xCount]['class'])."', '".$call[$xCount-1]['line']."', '".$call[$xCount]['function']."', '".urldecode(http_build_query($call[$xCount]['args']))."', 'Call Control', '".$GLOBALS['_CONNECTION_TYPE']."');\n");
                                }
                
            } else {
                for($xCount = 1; $xCount < 10; $xCount++){
                    if($call[$xCount]['function'] != "query" 
                                        && $call[$xCount]['function'] != "showError" 
                    && $call[$xCount]['function'] != "getValue" 
                    && $call[$xCount]['function'] != "getListValues"
                    && substr($call[$xCount]['function'], 0, 7) != "include") break;
                }
                                
                                $_CLASS_ALLOWED = self::classAllowed($GLOBALS['_USER_LOG_MODE'], $call[$xCount]['class'], $GLOBALS['_DEBUG_JOIN_PLUS']);
                                
                                if($_CLASS_ALLOWED || $this->last_error_id != ""){
//                                    echo $this->last_error_id." ".$call[$xCount]['class']." -> ".$call[$xCount]['function']."\n";
                                    $result = mysql_query("INSERT INTO ".$this->_LOG_TABLE_NAME." (user_id, fecha, pagina, linea, funcion, params, evento, conexion) VALUES ('".$GLOBALS['_USER_ID']."', '".date("Y-m-d H:i:s", $_SERVER["REQUEST_TIME"])."', '".mysql_real_escape_string($call[$xCount]['class'])."', '".$call[$xCount-1]['line']."', '".$call[$xCount]['function']."', '".urldecode(http_build_query($call[$xCount]['args']))."', '".mysql_real_escape_string($event)."', '".$GLOBALS['_CONNECTION_TYPE']."');\n");
                                }
            }
        }
       
        // -------------------------------------------------------------------------------------------
        // FUNCIÓN PARA TENER ACCESO DESDE FUERA DE LA CLASE A DETERMINADAS VARIABLES DE USO PRIVADO. 
        // -------------------------------------------------------------------------------------------
        // PARA PERIMITIR QUE SE LEA UNA VARIABLE DESDE FUERA DE LA CLASE DEBE DE ESTAR EL LA LISTA
        // $alowed_vars. SI NO ESTÁ, NO SE TENDRÁ ACCESO Y SACARÁ UN MENSAJE DE ERROR.
        // -------------------------------------------------------------------------------------------
        
          public function __get($name){
            $alowed_vars = "completedIn, total_queries, last_insert_id, affected_rows, selected_rows";
            if(strpos($alowed_vars, $name) !== false){
                 return $this->$name;
            } else {
                eval("\$exists_reference = isset(\$this->".$name.");");
                if($exists_reference){
                    if($this->_ENABLED_LOG) $this->insertEntryLog('ERROR: No se permite el acceso a la variable o función '.$name);
                    echo '<b style="color:'.$this->_ERROR_COLOR.'">ERROR FATAL</b>:<br>No se permite el acceso a la variable o función '.$name.'.<br />'."\n";
                } else {
                    if($this->_ENABLED_LOG) $this->insertEntryLog('ERROR: La variable o función '.$name.' NO EXISTE.');
                    echo '<b style="color:'.$this->_ERROR_COLOR.'">ERROR FATAL</b>:<br>La variable o función '.$name.' NO EXISTE.<br />'."\n";
                }
            } 
        }
        
        // -------------------------------------------------------------------------------------
        // FUNCIÓN QUE TOMA LA HORA ACTUAL DEL SISTEMA Y LA DEVUELVE EN MICROSEGUNDOS.
        // -------------------------------------------------------------------------------------
          
        private function uTime (){
            list ($msec, $sec) = explode(' ', microtime());
            $microtime = (float)$msec + (float)$sec;
            return $microtime;
        }
        
        // -------------------------------------------------------------------------------------
        // FUNCIÓN QUE DETECTA LA CODIFICACIÓN DEL TEXTO PARA DESPUÉS SER TRATADA
        // -------------------------------------------------------------------------------------
        
        private function detectCodeText($t){
            $c = 0;
            $ascii = true;
            for ($i = 0;$i<strlen($t);$i++) {
                $byte = ord($t[$i]);
                if ($c>0) {
                    if (($byte>>6) != 0x2) {
                        return ISO_8859_1;
                    } else {
                        $c--;
                    }
                } elseif ($byte&0x80) {
                    $ascii = false;
                    if (($byte>>5) == 0x6) {
                        $c = 1;
                    } elseif (($byte>>4) == 0xE) {
                        $c = 2;
                    } elseif (($byte>>3) == 0x1E) {
                        $c = 3;
                    } else {
                        return ISO_8859_1;
                    }
                }
            }
            return ($ascii) ? ASCII : UTF_8;
        }
        
        // -------------------------------------------------------------------------------------
        // FUNCIÓN CONVERTIR UN TEXTO EN FORMATO LEGIBLE.
        // -------------------------------------------------------------------------------------
        
        public function utf8($t){
            return ($this->detectCodeText($t)==ISO_8859_1) ? utf8_encode($t) : utf8_decode($t);
        }
        
        // -----------------------------------------------------------------------------------------------
        // CONVIERTE UNA CADENA DE TIPO FECHA, DEL FORMATO ENVIADO A UN ARRAY.
        // LOS PARÁMETROS DE $format SON LOS MISMOS QUE PARA LA IUNSTRUCCION DATEDE PHP.
        // -----------------------------------------------------------------------------------------------
        
        public function time2Array($date, $format) {
            $masks = array(
                'd' => '(?P<d>[0-9]{1,2})',
                'm' => '(?P<m>[0-9]{1,2})',
                'Y' => '(?P<Y>[0-9]{2,4})',
                'H' => '(?P<H>[0-9]{1,2})',
                'i' => '(?P<M>[0-9]{1,2})',
                's' => '(?P<S>[0-9]{1,2})',
            );
            $rexep = "#".strtr(preg_quote($format), $masks)."#";
            if(!preg_match($rexep, $date, $out)) return false;
            
            $ret = array(
                "tm_sec"  => (int) $out['S'],
                "tm_min"  => (int) $out['M'],
                "tm_hour" => (int) $out['H'],
                "tm_mday" => (int) $out['d'],
                "tm_mon"  => $out['m']?$out['m']-1:0,
                "tm_year" => $out['Y'] > 1900 ? $out['Y']-1900 : 0,
            );
            
            return $ret;
        } 
        
        // -----------------------------------------------------------------------------------------------
        // CONVIERTE UNA CADENA DE TIPO FECHA, DEL FORMATO ENVIADO A FORMATO UNIX.
        // LOS PARÁMETROS DE $format SON LOS MISMOS QUE PARA LA IUNSTRUCCION DATEDE PHP.
        // -----------------------------------------------------------------------------------------------
        // Los FORMATOS para manejo de las fechas son como la intrucción date() de PHP.
        // Los valores de cadena SÓLO NÚMEROS o SÓLO LETRAS causan ERROR por no ser considerados fechas.
        // Hay que tener cuidado ya que tiene comportamientos creados a conciencia. Por ejemplo:
        // Si la cadena es "2012/33" siendo 2012 el año y 33 el día, nos devuelve el 02-02-2012.
        // Si la cadena es "oct-01" o "01-oct" siendo oct el mes (Octubre) y 01 el día devuelve 01-10-2012.
        // Si la cadena es "2012-oct o "oct-2012" siendo 2012 el año y oct el mes (Octubre), devuelve 01-10-2012.
        
        public function mkTimeFormat($value, $format=""){
            $f = $format;
            if($f == "") $f = $this->_FORMAT_DATETIME_FRMWRK;
            
            for ($x = 0; $x < count($this->_NAMES_MONTH); $x++){
                $value = str_ireplace($this->_NAMES_MONTH[$x], ($x % 12)+1, $value);
            }
            
            $value = ereg_replace("/", "-", $value);
            $f = ereg_replace("/", "-", $f);
            @extract($this->time2Array($value,$f));
            
            if($tm_year == 0 && $tm_mon == 0 && $tm_mday == 0 && $tm_hour == 0 && $tm_min == 0 && $tm_sec == 0 && $format==""){
                $f = ereg_replace("/", "-", $this->_FORMAT_DATE_FRMWRK);
                @extract($this->time2Array($value,$f));
            }
            
            if($tm_year == "" && $tm_mday != "" && $tm_mon != "") $tm_year = date("Y")-1900;
            if($tm_mday == "") $tm_mday = "01";
            
            $mktime = mktime(
                intval($tm_hour),
                intval($tm_min),
                intval($tm_sec),
                intval($tm_mon)+1,
                intval($tm_mday),
                intval($tm_year+1900)
            );
            
            return $mktime;
        }
        
        // ------------------------------------------------------------------------------------------------------------------------------
        // CONVIERTE UNA CADENA DE TIPO FECHA, DEL FORMATO ENVIADO A FORMATO ENVIADO POR $format.
        // LOS PARÁMETROS DE $format Y $format_source SON LOS MISMOS QUE PARA LA IUNSTRUCCION DATEDE PHP.
        // SI $format_source = "" SE TOMA EL FORMATO DE FECHA DE LA CLASE, O BIEN DE _FORMAT_DATETIME_FRMWRK, O DE _FORMAT_DATE_FRMWRK.
        // SI $format = "" SE TOMA EL FORMATO DE FECHA DE LA CLASE DE _FORMAT_DATETIME_DB.
        // ------------------------------------------------------------------------------------------------------------------------------
        // Los FORMATOS para manejo de las fechas son como la intrucción date() de PHP.
        // Los valores de cadena SÓLO NÚMEROS o SÓLO LETRAS causan ERROR por no ser considerados fechas.
        // Hay que tener cuidado ya que tiene comportamientos creados a conciencia. Por ejemplo:
        // Si la cadena es "2012/33" siendo 2012 el año y 33 el día, nos devuelve el 02-02-2012.
        // Si la cadena es "oct-01" o "01-oct" siendo oct el mes (Octubre) y 01 el día devuelve 01-10-2012.
        // Si la cadena es "2012-oct o "oct-2012" siendo 2012 el año y oct el mes (Octubre), devuelve 01-10-2012.
        // EJ: $mysql->toDateFormat("2012-oct-10","Y-m-d", "d-m-Y");
        // ------------------------------------------------------------------------------------------------------------------------------
        
        public function toDateFormat($value, $format_source="", $format=""){
            
            if($this->isNumber(substr($value, 0,4))) $mktime = strtotime($value);
            
            if($mktime == 0){
                $f = $format_source;
                if($f == "") $f = $this->_FORMAT_DATETIME_FRMWRK;
                $mktime = $this->mkTimeFormat($value, $f);
                        
                if($mktime==0 && $format == ""){
                    $f = $this->_FORMAT_DATE_FRMWRK;
                    $mktime = $this->mkTimeFormat($value, $f);
                }
            }
            
            $f=$format;
            if($f == "") $f = $this->_FORMAT_DATETIME_DB;
            return date($format, $mktime);
        }
        
        // -----------------------------------------------------------------------------------------------
        // COMPRUEBA SI LA CADENA INTRODUCIDA ES UN TIPO FECHA. SE LE DEBE PASAR EL FORMATO PARA COMPARAR.
        // LOS PARÁMETROS DE $format SON LOS MISMOS QUE PARA LA IUNSTRUCCION DATEDE PHP.
        // -----------------------------------------------------------------------------------------------
        // EJ: $mysql->isDate("2012/33", "Y/d")     Devolvería 02-02-2012 y por eso, devuelve true
        // EJ: $mysql->isDate("oct-01", "m-d")         Devolvería 01-10-2012 y por eso, devuelve true
        // EJ: $mysql->isDate("oct", "m")            Devolvería error y por eso, devuelve false
        // EJ: $mysql->isDate("31", "d")            Devolvería error y por eso, devuelve false
        // EJ: $mysql->isDate("31/10/2012", '')        Devolvería 31-10-2012 y por eso, devuelve true
        
        public function isDate($value, $format=""){
            if($this->isNumber(substr($value, 0,4))) $mktime = strtotime($value);

            if($mktime == 0){
                $f = $format;
                if($f == "") $f = $this->_FORMAT_DATETIME_FRMWRK;
                
                if ($this->isNumber($value)) return false;
                
                for ($x = 0; $x < count($this->_NAMES_MONTH); $x++){
                    if(str_ireplace($this->_NAMES_MONTH[$x], '', $value) == "") return false;
                }
                
                $mktime = $this->mkTimeFormat($value, $f);
                if($mktime == 0 && $format == ""){
                    $f = $this->_FORMAT_DATE_FRMWRK;
                    $mktime = $this->mkTimeFormat($value, $f);
                    if($mktime == 0) return false;
                } 
            }
            return true; //date("d-m-Y H:i:s", $mktime);
        }
        
        // -----------------------------------------------------------------------------------------------
        // COMPRUEBA SI EL VALOR ENVIADO POR $value ES UN NÚMERO
        // -----------------------------------------------------------------------------------------------
        
        public function isNumber($value){
            if (is_numeric($value)) return true;
            return false;
        }
        
        // -----------------------------------------------------------------------------------------------
        // COMPRUEBA SI EL VALOR ENVIADO POR $value ES UNA CADENA
        // -----------------------------------------------------------------------------------------------
        
        public function isString($value){
            if ($this->isNumber($value) || $this->isDate($value) || is_array($value) || is_object($value)) return false;
            return true;
        }
        
        // ------------------------------------------------------------------------------------------------------------------
        // FUNCIÓN QUE RESTA 2 FECHAS Y NOS DEVUELVE EL NÚMERO DE DIAS, HORAS, MINUTOS Y SEGUNDOS QUE HAN PASADO ENTRE ELLAS 
        // ------------------------------------------------------------------------------------------------------------------
        // Devuelve un ARRAY con el formato array("d" => $d, "h" => $h, "m" => $m, "s" => $s) en dónde cada inicial de la 
        // clave del array indica la medida de tiempo
        // ------------------------------------------------------------------------------------------------------------------
        
        function elapsedTime($dInit, $dEnd, $dInit_format="", $dEnd_format=""){
            $f1 = $dInit_format;
            $f2 = $dEnd_format;
            if($f1 == "") $f1 = $this->_FORMAT_DATETIME_FRMWRK;
            if($f2 == "") $f2 = $this->_FORMAT_DATETIME_FRMWRK;
            
            // Probamos si está en formato legible por PHP
            if($this->isNumber(substr($dInit, 0,4))) $dInitAux = strtotime($dInit);
            // Si no, probamos con nuestra función y el formato pasado
            if($dInitAux == 0) $dInitAux = $this->mkTimeFormat($dInit, $f1);
            // Si no, probamos con nuestra función y el formato establecido por _FORMAT_DATE_FRMWRK
            if($dInitAux == 0 && $dInit_format == ""){
                $f1 = $this->_FORMAT_DATE_FRMWRK;
                $dInitAux = $this->mkTimeFormat($dInit, $f1);
            }
            
            // Probamos si está en formato legible por PHP
            if($this->isNumber(substr($dEnd, 0,4))) $dEndAux = strtotime($dEnd);
            // Si no, probamos con nuestra función y el formato pasado
            if($dEndAux == 0) $dEndAux  = $this->mkTimeFormat($dEnd,  $f2);
            // Si no, probamos con nuestra función y el formato establecido por _FORMAT_DATE_FRMWRK
            if($dEndAux == 0 && $dEnd_format == ""){
                $f2 = $this->_FORMAT_DATE_FRMWRK;
                $dEndAux = $this->mkTimeFormat($dEnd, $f2);
            }
            // Asignamos los auxiliares a los actuales ya convertidos
            $dInit = $dInitAux;
            $dEnd = $dEndAux;
            
            // Si la fecha inicial es mayor que la final, cambiamos.
            if($dInit > $dEnd){
                $aux = $dInit;
                $dInit = $dEnd;
                $dEnd = $aux;
            }
            echo date("d-m-Y", $dInit)." ".date("d-m-Y", $dEnd)."<br />";
            // Hacemos los cálculos
            $d=intval(($dEnd-$dInit)/86400);
            $h=intval((($dEnd-$dInit) - ($d*86400))/3600);
            $m=intval((($dEnd-$dInit) - ($d*86400)-($h*3600))/60);
            $s=intval((($dEnd-$dInit) - ($d*86400)-($h*3600)-($m*60)));
    
            return array("d" => $d, "h" => $h, "m" => $m, "s" => $s);
        }
        
        // -----------------------------------------------------------------------------------------------
        // REALIZA LA CONEXIÓN A LA BASE DE DATOS
        // -----------------------------------------------------------------------------------------------
        
        public function connect(){
            if(!isset($this->resource)){
                if($_SERVER['HTTP_HOST'] == "localhost"){
                    $this->resource=(mysql_connect($_SERVER['HTTP_HOST'], $this->_USER_DEVELOPMENT, $this->_PASS_DEVELOPMENT)) or die(mysql_error());
                    mysql_select_db($this->_DATABASE_NAME_DEVELOPMENT, $this->resource) or die(mysql_error());
                } else {
                    $this->resource=(mysql_connect("localhost", $this->_USER_PRODUCTION, $this->_PASS_PRODUCTION)) or die(mysql_error());
                    mysql_select_db($this->_DATABASE_NAME_PRODUCTION, $this->resource) or die(mysql_error());
                }
            }
        }
        
        // -----------------------------------------------------------------------------------------------
        // SELEECIONA UNA BASE DE DATOS
        // -----------------------------------------------------------------------------------------------
        
        public function usedb($db_name){
            if(!isset($this->resource)){
                $this->_DATABASE_NAME_DEVELOPMENT = $db_name;
                $this->_DATABASE_NAME_PRODUCTION  = $this->_DATABASE_NAME_DEVELOPMENT;
            }
        }
        
        // ------------------------------------------------------------------------------------------------------------------------------------------
        // REALIZA LA CONSULTA A LA BASE DE DATOS ENVIADA POR $consulta.
        // ------------------------------------------------------------------------------------------------------------------------------------------
        // Si _SHOW_CONTROL_MESSAGES está establecido a true, se muestran los mensajes de tipo ERROR en pantalla. Sólo para depuración de errores.
        // Si _SHOW_WARNING_ERROR está establecido a true, se muestran los mensajes de tipo WARNING en pantalla. Sólo para depuración de errores.
        // Si _STOP_WARNING_ERROR está establecido a true, se parará la ejecución como si un ERROR se tratase.  Sólo para depuración de errores.
        // ------------------------------------------------------------------------------------------------------------------------------------------
        
        public function query($query){
            $this->total_queries++;
            $this->selected_rows = 0;
            $this->affected_rows = 0;
            $this->execStartTime = $this->uTime();
            if (preg_match( '/^\s*(select) /i', $query) || preg_match( '/^\s*(show) /i', $query)){
                $this->resource = mysql_query($query);
                //$result = $this->resource;
                $this->last_query = $query;
                if(!$this->resource) $this->showError();
                $this->selected_rows = $this->num_rows();
                $this->execEndTime = $this->uTime(); 
                $this->completedIn = round($this->execEndTime - $this->execStartTime, 5) ;
                
                if($this->_ENABLED_LOG) $this->insertEntryLog($this->last_query);
                
                return $this->resource;
            } else {
                $query .= mySQL::_SEPARADOR_SQL;
                $query_array = explode(mySQL::_SEPARADOR_SQL, $query);
                foreach ($query_array as $sentence){
                    if (trim($sentence) != ""){
                        if($this->_UTF8_ENCODE){
                            $this->resource = mysql_query($sentence);
                        } else {
                            $this->resource = mysql_query($sentence);
                        }
                        $result = $this->resource;
                        $this->last_query = $sentence;
                        if (!$this->resource) {
                            $this->showError();
                        } else {
                            if (preg_match( '/^\s*(insert) /i', $sentence)){ $this->affected_rows += 1; $this->last_insert_id = mysql_insert_id(); }
                            else $this->affected_rows += $this->affected_rows();
                            $this->execEndTime = $this->uTime(); 
                            $this->completedIn = round($this->execEndTime - $this->execStartTime, 5) ;
                            
                            if($this->_ENABLED_LOG) $this->insertEntryLog($this->last_query);
                            
                        } // Fin if (!$this->resource)
                    } // Fin if (trim($sentence) != "")
                } // Fin foreach
            } 
            
            return $this->resource;
        }
        
        // -----------------------------------------------------------------------------------------------------------------
        // FUNCIÓN QUE RECUPERA EL NÚMERO DE TUPLAS O FILAS SELECCIONADAS. 
        // -----------------------------------------------------------------------------------------------------------------
        
        private function num_rows(){
            if($this->resource){
                $n = mysql_num_rows($this->resource);
                if (mysql_errno() != 0) $this->showError();
                if ($n == 0 || $n === false || $n == "" || $n == NULL) return 0;

                return $n;
            }
        }
        
        // -----------------------------------------------------------------------------------------------------------------
        // FUNCIÓN QUE RECUPERA EL NÚMERO DE TUPLAS O FILAS AFECTADAS EN UN UPDATE O DELETE. 
        // -----------------------------------------------------------------------------------------------------------------
        
        private function affected_rows(){
            $n = mysql_affected_rows();
            if (mysql_errno() != 0) $this->showError();
            if ($n == 0 || $n  == -1 || $n == "" || $n == NULL) return 0;
            
            return $n;
        }
        
        // -----------------------------------------------------------------------------------------------------------------
        // FUNCIÓN QUE ACTUALIZA LAS VARIABLES DE ERROR, GUARDANDO EL ÚLTIMO CÓDIGO Y MENSAJE DE ERROR.
        // ADEMÁS MUESTRA LOS MENSAJES DE ERROR SI PROCEDE. 
        // -----------------------------------------------------------------------------------------------------------------
        
        public function showError(){
                    //echo "E0.".$this->last_query."ERROR ".mysql_errno().": ".mysql_error().".<br /><br />";
                    $call = debug_backtrace();
                    for($xCount = 1; $xCount < 10; $xCount++){
                        if($call[$xCount]['function'] != "query" 
                        && $call[$xCount]['function'] != "showError" 
                        && $call[$xCount]['function'] != "getValue" 
                        && $call[$xCount]['function'] != "getListValues"
                        && substr($call[$xCount]['function'], 0, 7) != "include") break;
                    }
                    
                    $siteError = '<b style="color:'.$this->_ERROR_COLOR.'">CLASS</b>: '.mysql_real_escape_string($call[$xCount]['class']).'<br /><b style="color:'.$this->_ERROR_COLOR.'">LINE: </b>'.$call[$xCount-1]['line'].'<br /><b style="color:'.$this->_ERROR_COLOR.'">METHOD: </b>'.$call[$xCount]['function'];
                    
                    $this->last_error_id     =  mysql_errno();
                    $this->last_error_msg    =  mysql_error();
                    if (strpos($this->_IGNORE_ERRORS, (string) mysql_errno()) !== false){
                            if ($this->_SHOW_WARNING_ERROR){
                                    if ($this->_SHOW_CONTROL_MESSAGES && $this->_SHOW_IGNORED_ERRORS) echo '<b style="color:'.$this->_WARNING_COLOR.'">WARNING</b>:<br> Error: ' . mysql_errno() . ": " . mysql_error()."<br>".'<i>'.$sentence."</i><br>\n";
                                    if ($this->_STOP_WARNING_ERROR && $this->_SHOW_IGNORED_ERRORS) die();
                            } elseif($this->_SHOW_CONTROL_MESSAGES) {
                                    $this->insertEntryLog('ERROR '.$this->last_error_id . ': '.$this->last_error_msg);
                                    die('<b style="color:'.$this->_ERROR_COLOR.'">FATAL ERROR</b>:<br>Error: ' . $this->last_error_id . ": " . $this->last_error_msg . '<br /><b style="color:'.$this->_ERROR_COLOR.'">QUERY: </b>'.$this->last_query."<br />$siteError<br />");
                            }
                    } elseif($this->_SHOW_CONTROL_MESSAGES) {
                            $this->insertEntryLog('ERROR '.$this->last_error_id . ': '.$this->last_error_msg);
                            die('<b style="color:'.$this->_ERROR_COLOR.'">FATAL ERROR</b>:<br>Error: ' . $this->last_error_id . ": " . $this->last_error_msg . '<br /><b style="color:'.$this->_ERROR_COLOR.'">QUERY: </b>'.$this->last_query."<br />$siteError<br />");
                    }
                    //$this->last_error_id = "";
        }
                
        // -----------------------------------------------------------------------------------------------------------------------------------
        // REALIZA LA CONSULTA A LA BASE DE DATOS ENVIADA POR $sentence Y DEVUELVE EL VALOR DEL CAMPO SOLICITADO.
        // ES ÚNICAMENTE PARA CONSULTAS EN LAS QUE SE SOLICITA UN ÚNICO DATO Y UNA ÚNICA COINCIDENCIA.
        // -----------------------------------------------------------------------------------------------------------------------------------
        // EJ.: SELECT name FROM clientes WHERE id = 1;
        // Si no hay coincidencias devuelve el valor por defecto establecido por _EMPTY_FIELD_BY_DEFAULT
        // -----------------------------------------------------------------------------------------------------------------------------------
        
        public function getValue($sentence, $field_number = 0){
            $result = $this->query($sentence);

            if ($this->selected_rows == 0){
                return $this->_EMPTY_FIELD_BY_DEFAULT;
            } else {
                $row = mysql_fetch_row($result);
                return $row[$field_number];
            }
        }
        
        // -----------------------------------------------------------------------------------------------------------------------------------
        // REALIZA LA CONSULTA A LA BASE DE DATOS ENVIADA POR $sentence Y DEVUELVE UNA LISTA CON LOS VALORES DE LOS CAMPOS SOLICITADOS.
        // LA LISTA ES UN ARRAY QUE DESPUES PODEMOS RECUPERAR CON LA INSTRUCCIÓN list DE PHP O TRATARLO COMO UN array CUALQUIERA DE PHP.
        // -----------------------------------------------------------------------------------------------------------------------------------
        // EJ.: SELECT id, name FROM blog_tags WHERE id = 112;
        // LA FORMA MÁS FRECUENTE DE RECUPERAR LOS VALORES ES O SERÁ, EN EL EJEMPLO:
        // list($id, $name) = $this->getValues('SELECT id, name FROM blog_tags WHERE id = 112;');
        // PARA DESPUÉS RECUPERALO COMO:
        // echo "id = ".$id.", name = ".$name;
        // Si no hay coincidencias devuelve el valor por defecto establecido por _EMPTY_FIELD_BY_DEFAULT
        // -----------------------------------------------------------------------------------------------------------------------------------
        
        public function getListValues($sentence){
            $result = $this->query($sentence);
            
            $values = trim(substr($sentence, stripos($sentence, "SELECT")+7, stripos($sentence, "FROM",stripos($sentence, "SELECT"))-stripos($sentence, "SELECT")-7));
            $valuesArr = explode(",", $values);
            $valuesList = "";
                        $xCount = 0;
            foreach($valuesArr as $value){ 
                            $valuesList .= "\$item$xCount, "; 
                            $xCount++;
            }
            $valuesList = "list(".substr($valuesList, 0, strlen($valuesList)-2).")";
            
            if ($this->selected_rows == 0){
                return $this->_EMPTY_FIELD_BY_DEFAULT;
            } else {
                eval($valuesList." = mysql_fetch_row(\$result);");
                $list = array();
                                $xCount = 0;
                foreach($valuesArr as $value){
                                    eval("\$aux = \$item$xCount;");
                                    $list[] = $aux;
                                    $xCount++;
                }
                
                return $list;
            }
        }
        
        // -----------------------------------------------------------------------------------------------------------------------------------
        // ELIMINA TODAS LAS FILAS O TUPLAS DE LA TABLA $table QUE CUMPLAN LA CONDICIÓN $cond
        // -----------------------------------------------------------------------------------------------------------------------------------
        public function delete($table, $cond){
            $sentence = "DELETE FROM ".$table." WHERE ".$cond;
            $this->query($sentence);
        }
        
        // -----------------------------------------------------------------------------------------------------------------------------------
        // RECUPERA LOS RESULTADOS REFERENCIADOS POR $this->resource COMO UN ARRAY ASOCIATIVO DE NÚMEROS, NOMBRES ASOCIADOS O AMBOS
        // -----------------------------------------------------------------------------------------------------------------------------------
        // LA VARIABLE $type INDICA CÓMO SE HARÁ LA ASOCIACIÓN. SUS VALORES PUEDEN SER MYSQL_NUM, MYSQL_ASSOC O MYSQL_BOTH. 
        // POR DEFECTO ES MYSQL_BOTH.
        // -----------------------------------------------------------------------------------------------------------------------------------
        
        public function fetchArray($type=MYSQL_BOTH){
            $result = @mysql_fetch_array($this->resource, $type);
            if(mysql_errno() != 0){
                $this->showError();
            }
            
            return $result;
        }
        
        // -----------------------------------------------------------------------------------------------------------------------------------
        // RECUPERA LOS RESULTADOS REFERENCIADOS POR $this->resource COMO OBJETOS.
        // -----------------------------------------------------------------------------------------------------------------------------------
        // LA VARIABLE $class INDICA QUE EL OBJETO SE TRANSFERIRÁ A LA CLASE SUMINISTRADA.
        // -----------------------------------------------------------------------------------------------------------------------------------
        
        function fetchObject($class=""){
            if($class == "") $result = @mysql_fetch_object($this->resource);
            else  $result = @mysql_fetch_object($this->resource, $class);
            
            if(mysql_errno() != 0){
                $this->showError();
            }
            
            return $result;
        }
        
        // -----------------------------------------------------------------------------------------------------------------------------------
        // Obtiene el nombre del campo especificado de un resultado asociado a $this->resource.
        // -----------------------------------------------------------------------------------------------------------------------------------
        // LA VARIABLE $num INDICA EL NÚMERO DE CAMPO PARA EXTRAER EL NOMBRE.
        // -----------------------------------------------------------------------------------------------------------------------------------

        function field_name($num){
            return mysql_field_name($this->resource, $num);
        }
        
        // -------------------------------------------------------------------------------------------------------------------------------------
        // FUNCIÓN PARA EXPORTAR LA BBDD COMPLETA O TABLAS CONCRETAS.
        // -------------------------------------------------------------------------------------------------------------------------------------
        // $exportfilename
        // Es el nómbre del archivo destuino.
        // $exporttables:
        // Array que contiene las tablas de la base de datos que seran resguardadas. Puede especificarse un valor false para indicar que se 
        // exporten todas las tablas de la base de datos especificada por _DATABASE_NAME_DEVELOPMENT ó _DATABASE_NAME_PRODUCTION. Ejemplos son:
        // $tablas = false; ó $tablas = array("tabla1", "tabla2", "tablaetc");
        // $exportcompresion:
        // Indica cómo se enviará el archivo con los datos exportados. Puede ser FALSE, GZ ó BZ2.
        // $exportdrop:
        // Indica si se añadirá DROP antes de la creación de cada tabla.
        // -------------------------------------------------------------------------------------------------------------------------------------
        
        public function export($exportfilename, $exportdrop=false, $exporttables=false, $exportcompresion=false){
            // Definimos la base de datos de desarrollo o produción
            if($_SERVER['HTTP_HOST'] == "localhost"){
                $bd = mySQL::_DATABASE_NAME_DEVELOPMENT;
            } else {
                $bd = mySQL::_DATABASE_NAME_PRODUCTION;
            }
            
            // Array de tablas a exportar.
            if($exporttables != false){
                $exporttables= explode(",", $exporttables);
                for ($i=0;$i<count($exporttables);$i++){ $tablas[] = $exporttables[$i]; }
            } else {
                $result=$this->query("SHOW TABLES FROM $bd");
                while ($fila = $this->fetchArray(MYSQL_NUM)) {
                    $tablas[] = $fila[0];
                }
            }
            
            // Establecemos si hay compresión o no.
            if ($exportcompresion == false){
                $compresion = false;
            } else {
                $compresion = $exportcompresion;
            }
            
            // Introducimos la cabecera del archivo.
            $info['dumpversion'] = "1.0b";
            $info['fecha'] = date("d-m-Y");
            $info['hora'] = date("h:m:s A");
            $info['mysqlver'] = mysql_get_server_info();
            $info['phpver'] = phpversion();
            $aux = $this->getInfo();
            
            ob_start();
            print_r($tablas);
            $representacion = ob_get_contents();
            ob_end_clean ();
            preg_match_all('/(\[\d+\] => .*)\n/', $representacion, $matches);
            $info['tablas'] = implode(";  ", $matches[1]);
            $dump = "
# +===================================================================
# | MySQL Class {$info['dumpversion']}
# | por islavisual <comercial@islavisual.com>
# |
# | Generado el {$info['fecha']} a las {$info['hora']} por el usurio '$usurio'
# | Servidor: {$_SERVER['HTTP_HOST']}
# | Browser: {$aux['browser']} {$aux['version']}
# | SO: {$aux['so']}
# | IP: {$aux['ip']}
# | MySQL Version: {$info['mysqlver']}
# | PHP Version: {$info['phpver']}
# | Base de datos: '$bd'
# | Tablas: {$info['tablas']}
# |
# +-------------------------------------------------------------------";
            
            foreach ($tablas as $tabla) {
                $drop_table_query = "";
                $create_table_query = "";
                $insert_into_query = "";
                
                /* Se halla el query que será capaz vaciar la tabla. */
                if ($exportdrop) {
                    $drop_table_query = "DROP TABLE IF EXISTS `$tabla`;";
                } else {
                    $drop_table_query = "# No especificado.";
                }
            
                /* Se halla el query que será capaz de recrear la estructura de la tabla. */
                $create_table_query = "";
                $result = $this->query("SHOW CREATE TABLE $tabla;");
                while ($fila =$this->fetchArray(MYSQL_NUM)) {
                        $create_table_query = $fila[1].";";
                }
                
                /* Se halla el query que será capaz de insertar los datos. */
                $insert_into_query = "";
                $respuesta = $this->query("SELECT * FROM $tabla;");
                while ($fila = $this->fetchArray()) {
                        $values = array();
                        $columnas = array_keys($fila);
                        foreach ($columnas as $columna) {
                            if ( gettype($fila[$columna]) == "NULL" ) {
                                $values[]="NULL";
                            } else {
                                $values[] = "'".mysql_real_escape_string($fila[$columna])."'";
                            }
                        }
                        $insert_into_query .= "INSERT INTO `$tabla` VALUES (".implode(", ", $values).");\n";
                        unset($values);
                }
                
                $dump .="
                
# | Vaciado de tabla '$tabla'
# +------------------------------------->
$drop_table_query
    
# | Estructura de la tabla '$tabla'
# +------------------------------------->
$create_table_query

# | Carga de datos de la tabla '$tabla'
# +------------------------------------->
$insert_into_query

            ";
            }
            
            /* Envio */
            if ( !headers_sent() ) {
                header("Pragma: no-cache");
                header("Expires: 0");
                header("Content-Transfer-Encoding: binary");
                switch ($compresion) {
                case "gz":
                    header("Content-Disposition: attachment; filename=$nombre.gz");
                    header("Content-type: application/x-gzip");
                    echo gzencode($dump, 9);
                    break;
                case "bz2": 
                    header("Content-Disposition: attachment; filename=$nombre.bz2");
                    header("Content-type: application/x-bzip2");
                    echo bzcompress($dump, 9);
                    break;
                default:
                    header("Content-Disposition: attachment; filename=$nombre");
                    header("Content-type: application/force-download");
                    echo $dump;
                }
            } else {
                echo "<b><span style='color:".$this->_ERROR_COLOR."'>ATENCION</span>: No se puede enviar los encabezados pertinentes porque ya han sido enviados previamente.</b><br />\n<pre>\n$dump\n</pre>";
            }
        }
        
        // -------------------------------------------------------------------------------------------------------------------------------------------
        // LIBERA TODA LA MEMORIA ASOCIADA A $this->resource
        // -------------------------------------------------------------------------------------------------------------------------------------------
        // Solo necesita ser llamado si se está preocupado por la cantidad de memoria que está siendo usada por las consultas que devuelven conjuntos 
        // de resultados grandes. Toda la memoria de resultados asociada se liberará automaticamente al finalizar la ejecución del script. 
        // -------------------------------------------------------------------------------------------------------------------------------------------
        
        public function free(){
            mysql_free_result($this->resource);
        }
        
        // -------------------------------------------------------------------------------------------------------------------------------------------
        // CIERRA LA CONEXIÓN NO PERSISTENTE DEL SERVIDOR DE MYSQL QUE ESTÁ ASOCIADA A $this->resource.
        // -------------------------------------------------------------------------------------------------------------------------------------------
        
        public function disconnect(){ 
            if ($this->resource){ 
                return mysql_close($this->resource); 
            } 
        }
    }
?>
