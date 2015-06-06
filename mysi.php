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
    require_once( dirname(__FILE__) . "/config.php");
    
    define("OBJECT", "OBJECT");
    define("ARRAY_A", "ARRAY_A");
    define("ARRAY_N", "ARRAY_N");
    
    class MYSI{
        public    $resource;
        private   $total_queries = 0;
        
        private   $_DATABASE_NAME_DEVELOPMENT = '';         // For intern use.
        private   $_USER_DEVELOPMENT          = '';         // For intern use.
        private   $_PASS_DEVELOPMENT          = '';         // For intern use.

        private   $_DATABASE_NAME_PRODUCTION  = '';         // For intern use.
        private   $_USER_PRODUCTION           = '';         // For intern use.
        private   $_PASS_PRODUCTION           = '';         // For intern use.

        const     _TOKEN_KEY                  = 'date("Y-m-d H:i:s", $_SERVER["REQUEST_TIME"]);';
        var       $_ENCODED_TOKEN             = "";            //  Contains the token generated.
        
        var       $_IGNORE_ERRORS             = '1062';        // Is a list of Numbers of errors comma separated MySQL that will be managed in a special way. If you want to know what and how errors manage, you can to see the http://dev.mysql.com/doc/refman/5.0/es/error-handling.html URL.
        var       $_WARNING_COLOR             = 'orange';      // Color to show the WARNING messages.
        var       $_ERROR_COLOR               = 'red';         // Color to show the ERROR messages.
        var       $_SHOW_WARNING_ERROR        = true;          // If this variable is set to TRUE WARNING messages are displayed.
        var       $_SHOW_IGNORED_ERRORS       = false;         // If this variable is set to FALSE IGNORED messages aren't displayed. Otherwise, the messages, will be displayed as the others.
        var       $_SHOW_CONTROL_MESSAGES     = true;          // If this variable is set to TRUE ERROR messages are displayed.
        var       $_STOP_WARNING_ERROR        = false;         // If a warning error is found and this variable is seted to TRUE, the application execution  is force to die().
        
        var       $_SEPARADOR_SQL             = ";";           // Separator for executing multiple statements. At runtime, are separated by this value and then executed one by one.
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
        var       $error_text                 = "";            // 
        var       $error_class                = "";            // 
        var       $error_line                 = "";            // 
        var       $error_method               = "";            // 
        var       $_CURRENT_DB                = "";            // The database currently selected.
        
        protected $execStartTime              = 0;             // Is used to save the initial time of a query. Never change this property
        protected $execEndTime                = 0;             // Is used to save the ended time of a query. Never change this property
        var       $completedIn                = 0;             // Is used to save the transcurred time of a query. Never change this property
        
        // -------------------------------
        // Events Log variables
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
                     
        function __construct($db = ""){
            $this->connect($db);
        }

        /*************************************************************************************************************
         FUNCTIONS TO DATA TRANSFER CONTROL
         ----------------------------------
         You can use to to avoid repeated sentences in the same moment.
         In addition, you can use it to management and sent of secure sentences
         ************************************************************************************************************/
        
        /**
         * Function to encrypted tokens. The algorithm is made through to _TOKEN_KEY constant to make the sent string.
         * Example: $mysql->encodeToken("Complete. SELECT * FROM blog_tags WHERE 1;");
         * @param string $string String to encode
         * @return string String encrypted.
         **/
        function encodeToken($string) {
            eval("\$auxToken = ".self::_TOKEN_KEY);
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

        /**
         * Function to decrypted tokens.
         * Example: $mysql->decodeToken("jHd8dnWBUVtNd4Nvf1CcnKKhlKTlJsgn1GflpKMm6qblJRamKigUBqqlqWgTQ==");
         * @param string $token String to decode
         * @return string String decrypted.
         */
        function decodeToken($token) {
            eval("\$auxToken = ".self::_TOKEN_KEY);
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
        
        /**
         * Function to compare the sent token into $token and the string returned through by $string.
         * Example: $mysql->checkToken("Esto es una prueba", "6qblJRamKigUBqqlqWgTQ==");
         * @param string $string Original string to compare.
         * @param string $token Encrypted string to compare.
         * @return bool Return a boolean value, 'true' if both tokens are the same, 'false' in another issue.
         */
        function checkToken($string, $token=""){
            $current_token = $token;
            if($current_token == "") $current_token = $this->_ENCODED_TOKEN;
            
            $aux = $this->createToken($string);
            if($aux == $current_token) return true;
            
            return false;
        }
        
        /**
         * Function to check two tokens. If $token1 is empty take to compare the returned token by the _ENCODED_TOKEN
         * Example: $mysql->compareTokens("6qblJRamKigUBqqlqWgTQ==");
         * @param string $token1 First token to compare.
         * @param string $token2 Second token to compare.
         * @return bool Return a boolean value, 'true' if both tokens are the same, 'false' in another issue.
         */
        function compareTokens($token1, $token2=""){
            if($token2 == "") $token2 = $this->_ENCODED_TOKEN;
            
            if($token1 == $token2) return true;
            
            return false;
        }
        
        /**
         * Function to check if sent token already is into database.
         * Example: $mysql->existsToken("6qblJRamKigUBqqlqWgTQ==");
         * @param string $token Encrypted string to check.
         * @return bool Return true if exists, false in another issue.
         */
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
        
        /*************************************************************************************************************
         FUNCTIONS TO LOG MANAGEMENT
         ---------------------------
         This functions are used to event management like, for example, check queries history from a few days ago.
         The days number is seted by '_SIZE_LOG_IN_DAYS' constant. This functionality is enabled only if
         '_SAVE_QUERIES_IN_LOG' is seted to 'true'. By default this variable is 30 days.
        *************************************************************************************************************/

        /**
         * Function to recover the client data that browse by the system / web.
         * Recover, between another data, the name and version browser, IP and operative system.
         * To use this functionality is necessary have loaded previously the getInfo Class. In another words, insert
         * into your code the next line code:
         * include "getInfo.class.php";
         * Example: $mysql->getInfo();
         * @param void
         * @return array Return an array with all information of user.
         */
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

        /**
         * Function to create the LOG data table.
         * Example: $mysql->createTableLog();
         * @param void
         * @return resource
         */

        public function createTableLog(){
            $query = $this->_LOG_TABLE_DEF;
            $query = @str_replace('<table_log>', $this->_LOG_TABLE_NAME, $query);
            $result = mysql_query($query);
            $this->last_query = $query;
            
            return $result;
        }

        /** Private function to intern use
         * @param $rights
         * @param $class
         * @param $join_plus
         * @return bool
         */
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
                
        /**
         * Function to insert a event into LOG table. In addition save, the name and version browser, IP and operative
         * system and page...
         * Example: $mysql->insertEntryLog("Access to");
         * @param string $event String to save into database
         */
        public function insertEntryLog($event=''){
            $call = debug_backtrace();
            
            if($event == ''){
                for($xCount = 2; $xCount < 10; $xCount++){
                    if( substr($call[$xCount]['function'], 0, 7) != "include") break;
                }
                
                $_CLASS_ALLOWED = self::classAllowed($GLOBALS['_USER_LOG_MODE'], $call[$xCount]['class'], $GLOBALS['_DEBUG_JOIN_PLUS']);

                if($_CLASS_ALLOWED || $this->last_error_id != ""){
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
                    $result = @mysql_query("INSERT INTO ".$this->_LOG_TABLE_NAME." (user_id, fecha, pagina, linea, funcion, params, evento, conexion) VALUES ('".$GLOBALS['_USER_ID']."', '".date("Y-m-d H:i:s", $_SERVER["REQUEST_TIME"])."', '".mysql_real_escape_string($call[$xCount]['class'])."', '".$call[$xCount-1]['line']."', '".$call[$xCount]['function']."', '".urldecode(http_build_query($call[$xCount]['args']))."', '".mysql_real_escape_string($event)."', '".$GLOBALS['_CONNECTION_TYPE']."');\n");
                }
            }
        }
       
        /**
         * Function to recover variables of private use from out of this class.
         * To allow recover a variable, the variable must be declare into $_ALLOWED_VARS. If the variable name
         * does not into this list, the system will deny the request and will show an error message.
         * @param string $name The variable name to recover
         * @return string Return the value of variable
         */
        public function __get($name){
            $_ALLOWED_VARS = "completedIn, total_queries, last_insert_id, affected_rows, selected_rows";
            if(strpos($_ALLOWED_VARS, $name) !== false){
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

        /**
         * Function to recover the system hour and transform to milliseconds.
         * @return float
         */
        private function uTime (){
            list ($msec, $sec) = explode(' ', microtime());
            $microtime = (float)$msec + (float)$sec;
            return $microtime;
        }
        
        /**
         * Function to detect the coded type of a text
         * @param string $t String to check.
         * @return string Return if the text has ASCII codification or UTF8 codification
         */
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

        /**
         * Function to check if a text is encoded like ISO-8859-1.  If the response is 'true', the string is coverted to UTF-8,  otherwise, the text is decoded.
         * Example: $mysql->utf8($row['name']);
         * @param string $t String to check
         * @return string if UTF-8 format
         */
        public function utf8($t){
            return ($this->detectCodeText($t)==ISO_8859_1) ? utf8_encode($t) : utf8_decode($t);
        }
        
        /**
         * Function to convert a date of type string to array format.
         * The parameters than you can to use into $format variable are the PHP same that.
         * Example: @extract($this->time2Array($value,"Y-m-d"));
         * @param $date
         * @param $format
         * @return array|bool
         */
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
                "tm_sec"  => isset($out['s'])?((int) $out['s']):0,
                "tm_min"  => isset($out['s'])?((int) $out['M']):0,
                "tm_hour" => isset($out['s'])?((int) $out['H']):0,
                "tm_mday" => (int) $out['d'],
                "tm_mon"  => isset($out['m'])?$out['m']-1:0,
                "tm_year" => $out['Y'] > 1900 ? $out['Y']-1900 : 0,
            );
            
            return $ret;
        } 
        
        /**
         * Convert a date format, from format received by $format to UNIX format.
         * The values with only characters or numbers produce a error.
         * You must to have caution  because this function has behaviours defined to help into the development.
         * If the string sent is "2012/33", will be returned 02-02-2012.
         * If the string sent is "oct-01", will be returned 01-10-2012.
         * If the string sent is "2012-oct", will be returned 01-10-2012.
         * Example: $mysql->mkTimeFormat(date("d-m-Y H:i:s"), "d-m-Y H:i:s");
         * @param string $value Is string to convert.
         * @param string $format Is target format.
         * @return int Return the UNIX timestamp
         */
        public function mkTimeFormat($value, $format=""){
            $tm_year = 0; $tm_mon = 0; $tm_mday = 0; $tm_hour = 0; $tm_min = 0; $tm_sec = 0; $f = $format;
            if($f == "") $f = $this->_FORMAT_DATETIME_FRMWRK;
            
            for ($x = 0; $x < count($this->_NAMES_MONTH); $x++){
                $value = str_ireplace($this->_NAMES_MONTH[$x], ($x % 12)+1, $value);
            }

            $value = str_replace("/", "-", $value);
            $f = str_replace("/", "-", $f);
            @extract($this->time2Array($value,$f));

            if($tm_year == 0 && $tm_mon == 0 && $tm_mday == 0 && $tm_hour == 0 && $tm_min == 0 && $tm_sec == 0 && $format==""){
                $f = str_replace("/", "-", $this->_FORMAT_DATE_FRMWRK);
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
        
        /**
         * Convert a string with date format, from format seted by $format_source variable to $format variable.
         * The values with only characters or numbers produce a error.
         * If $format_source is empty, by default take the local format defined into class. The possible formats are
         * defined in _FORMAT_DATE_FRMWRK and _FORMAT_DATETIME_FRMWRK depends of if the values is time or date type.
         * If $format is empty, by default take the local format defined into class in the _FORMAT_DATETIME_DB variable
         * You must to have caution  because this function has behaviours defined to help into the development.
         * If the string sent is "2012/33", will be returned 02-02-2012.
         * If the string sent is "oct-01", will be returned 01-10-2012.
         * If the string sent is "2012-oct", will be returned 01-10-2012.
         * Example: $mysql->toDateFormat("2012-oct-10","Y-m-d", "d-m-Y");
         * @param string $value String to transform.
         * @param string $format_source Source format
         * @param string $format Target format
         * @return Return a date type.
         */
        public function toDateFormat($value, $format_source="", $format=""){
            $mktime = 0;
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
        
        /**
         * Check if the sent string is a date type. You must send the format to check.
         * The parameters than you can to use into $format variable are the PHP same that.
         * Examples:
         *      $mysql->isDate("2012/33", "Y/d") is transformed to 02-02-2012, like result will be return 'true'.
         *      $mysql->isDate("oct-01", "m-d") is transformed to 01-10-2012, like result will be return 'true'.
         *      $mysql->isDate("oct", "m") will be return error, like result will be return 'false'.
         *      $mysql->isDate("31", "d") will be return error, like result will be return 'false'.
         *      $mysql->isDate("31/10/2012", '') is transformed to 31-10-2012, like result will be return 'true'.
         * @param string $value String to check.
         * @param string $format Format defined.
         * @return bool Return true or false.
         */

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
        
        /**
         * Check if the sent value is a number
         * @param number $value Value to test
         * @return bool Return true or false.
         */
        public function isNumber($value){
            if (is_numeric($value)) return true;
            return false;
        }

        /**
         * Check if the sent value is a string
         * @param string $value Value to test.
         * @return bool Return true or false.
         */
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

        /**
         * Function to calculate the days difference between two dates like days, hours, minutes and seconds.
         * @param $dInit
         * @param $dEnd
         * @param string $dInit_format Is Optional. * If is empty, by default take the local format defined into class.
         * The possible formats are defined in _FORMAT_DATE_FRMWRK and _FORMAT_DATETIME_FRMWRK depends of if the values
         * is time or date type.
         * @param string $dEnd_format Is Optional. * If is empty, by default take the local format defined into class.
         * The possible formats are defined in _FORMAT_DATE_FRMWRK and _FORMAT_DATETIME_FRMWRK depends of if the values
         * is time or date type.
         * @return array Will be returned an array with the format array("d" => 0, "h" => 1, "m" => 33, "s" => 2)
         * with the result.
         */
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
            //echo date("d-m-Y", $dInit)." ".date("d-m-Y", $dEnd)."<br />";
            
            // Hacemos los cálculos
            $d=intval(($dEnd-$dInit)/86400);
            $h=intval((($dEnd-$dInit) - ($d*86400))/3600);
            $m=intval((($dEnd-$dInit) - ($d*86400)-($h*3600))/60);
            $s=intval((($dEnd-$dInit) - ($d*86400)-($h*3600)-($m*60)));
    
            return array("d" => $d, "h" => $h, "m" => $m, "s" => $s);
        }
        
        /**
         * Function to connect with the database.
         * @param string $db Name of database to connect
         */
        public function connect($db = ""){
            //Overwritting default behavior to handle different localhost, dev and production configurations
            $this->_USER_DEVELOPMENT = DB_USER;
            $this->_USER_PRODUCTION = DB_USER;
            $this->_PASS_DEVELOPMENT = DB_PASSWORD;
            $this->_PASS_PRODUCTION = DB_PASSWORD;

            if($db != ""){
                if($_SERVER['HTTP_HOST'] == "localhost"){
                    $this->resource=(mysql_connect($_SERVER['HTTP_HOST'], $this->_USER_DEVELOPMENT, $this->_PASS_DEVELOPMENT)) or die(mysql_error());
                    mysql_select_db($db==""?$this->_DATABASE_NAME_DEVELOPMENT:$db, $this->resource) or die(mysql_error());
                } else {
                    $this->resource=(mysql_connect("localhost", $this->_USER_PRODUCTION, $this->_PASS_PRODUCTION)) or die(mysql_error());
                    mysql_select_db($db==""?$this->_DATABASE_NAME_PRODUCTION:$db, $this->resource) or die(mysql_error());
                }

                mysql_query("SET NAMES '".$this->_CHARSET_PREFERRED."'",$this->resource);
                mysql_query("SET CHARACTER SET ".$this->_CHARSET_PREFERRED, $this->resource);
                if($this->_CHARSET_PREFERRED == "utf8")
                    mysql_query("SET SESSION collation_connection = '".$this->_CHARSET_PREFERRED."_unicode_ci'", $this->resource);
                else {
                    mysql_query("SET SESSION collation_connection = '".$this->_CHARSET_PREFERRED."'", $this->resource);
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

                $this->_CURRENT_DB = $db;
            }
        }
        
        /**
         * Select a database.
         * @param $db_name
         * @return bool
         */
        public function usedb($db_name){
            echo "Function usedb() is deprecated.";
            return false;
            if(!isset($this->resource)){
                $this->_DATABASE_NAME_DEVELOPMENT = $db_name;
                $this->_DATABASE_NAME_PRODUCTION  = $this->_DATABASE_NAME_DEVELOPMENT;
            }
        }
        
        /**
         * Function to make requests to database.
         * If _SHOW_CONTROL_MESSAGES is setet to 'true', will be show the ERROR messages into screen.
         * If _SHOW_WARNING_ERROR is setet to 'true', will be show the WARNING messages into screen.
         * If _STOP_WARNING_ERROR is setet to 'true', the execution is stopped like if had have occurred a fatal error.
         * @param string $query Sentence to execute.
         * @param string $output Type of data to return. The possible options are: ARRAY_A, ARRAY_N, OBJECT.
         * @param bool $prepare For execute sentences of lines multiple like FUNCTIONS or PROCEDURES
         * @return resource Return a pointer to resulting object
         */
        public function query($query, $output = "", $prepare = true){
            $this->total_queries++;
            $this->selected_rows = 0;
            $this->affected_rows = 0;
            $this->execStartTime = $this->uTime();
            
            if (preg_match( '/^\s*(select) /i', $query) || preg_match( '/^\s*(show) /i', $query)){
                $queries_count = 0;
                $queries_arr = explode($this->_SEPARADOR_SQL, $query);
                foreach($queries_arr as $query_item){
                    $query_item = trim($query_item);
                    if($query_item != "") $queries_count++;
                }
                if($queries_count > 1){
                    $this->last_error_id  = '0001';
                    $this->last_error_msg = "There are many queries. Use to SELECT and SHOW a single statement.";
                    $this->error_class    = 'MYSI';
                    $this->error_line     = '';
                    $this->error_method   = 'QUERY';
                    return false;
                }

                $this->resource = mysql_query($query);
                //$result = $this->resource;
                $this->last_query = $query;
                if(!$this->resource) $this->showError();
                $this->selected_rows = $this->num_rows();
                $this->execEndTime = $this->uTime(); 
                $this->completedIn = round($this->execEndTime - $this->execStartTime, 5) ;
                
                if($this->_ENABLED_LOG) $this->insertEntryLog($this->last_query);
                
                if($output != "") return $this->getContain($output);
                else return $this->resource;
            } else {
                if($prepare) $query_array = $this->prepare_queries($query); else $query_array = array($query);
//                var_dump($query_array); echo "\n\n";
                foreach ($query_array as $sentence){
                    if (trim($sentence) != ""){
                        if($this->_UTF8_ENCODE){
                            $this->resource = mysql_query($sentence);
                        } else {
                            $this->resource = mysql_query($sentence);
                        }
                        $result = $this->resource;
                        $this->last_query = $sentence;

                        if (!$this->resource && stripos($this->last_query, 'DELIMITER') === false ) {
                            $this->showError();
                        } elseif( stripos($this->last_query, 'DELIMITER') === false) {
                            if (preg_match( '/^\s*(insert) /i', $sentence)){ $this->affected_rows += 1; $this->last_insert_id = mysql_insert_id(); }
                            else $this->affected_rows += $this->affected_rows();
                            $this->execEndTime = $this->uTime(); 
                            $this->completedIn = round($this->execEndTime - $this->execStartTime, 5) ;
                            
                            if($this->_ENABLED_LOG) $this->insertEntryLog($this->last_query);
                            
                        } // End If (!$this->resource)
                    } // End if (trim($sentence) != "")
                } // End foreach
            } 
            
            return $this->resource;
        }

        /**
         * Function of private use. Is used by 'query' function to return the resulting data in a specific format.
         * @param array|object|string $output Return the result. This field depends of output format defined in 'query' function.
         * @return array|stdClass
         */
        private function getContain($output){
            if($this->selected_rows == 1){
                $result = @mysql_fetch_array($this->resource, MYSQL_ASSOC);
            } else {
                while($row = @mysql_fetch_array($this->resource, MYSQL_ASSOC)){
                    $result[] = $row;
                    if(mysql_errno() != 0) $this->showError();
                } 
            }
            
            if ( $output == OBJECT ){
                // If the output is an object then return row as such..
                $object = new stdClass();
                foreach ($result as $key => $value){ $object->$key = $value; }
                return $object;
                
            } elseif ( $output == ARRAY_A ){
                // If the output is an associative array then return row as such..
                if(!empty($result)) return $result; else null;
            } elseif ( $output == ARRAY_N ){
                // If the output is an numerical array then return row as such..
                if(!empty($result)) return array_values($result); else null;
            }
        }

        /**
         * Function to prepare complex instructions or several queries simultaneously.
         * @param string $queries List of queries or sentence of lines multiple.
         * @return array Return string with the right format to execute into server.
         */
        public function prepare_queries($queries){
            $queries_list = array();
            $xCount = 0;
            
            $posi = stripos($queries, 'delimiter');
            if($posi !== false){
                $queries_array = explode("DELIMITER", str_ireplace("delimiter ", "DELIMITER ", $queries));
                for($x = 0; $x < count($queries_array); $x++){
                    $query_section = trim($queries_array[$x]);
                    
                    if($query_section == "" || $query_section ==  $this->_SEPARADOR_SQL ) continue;
                    
                    if($x == 0){
                        $delimiter = ';';
                    } else {
                        $delimiter = trim(substr($query_section, 0, strpos($query_section, "\n")));
                        if($delimiter == "") $delimiter = trim(substr($query_section, 0, strpos($query_section, " ")));
                    }
                    $this->_SEPARADOR_SQL = $delimiter;
                    
                    $query_section = trim(substr($query_section, stripos($query_section, $delimiter)+strlen($delimiter)));
                    $arr = explode($delimiter, $query_section);
                    
                    $delimiterCount = 0;
                    foreach ($arr as $query){
                        $query = trim($query);
                        
                        if($query == "") continue;
                        
                        if($delimiterCount == 0){
                            $queries_list[] = 'DELIMITER '.$this->_SEPARADOR_SQL;
                            $delimiterCount++;
                        }
                        $queries_list[] = $query; //.$this->_SEPARADOR_SQL;
                        $xCount++;
                    }
                }
            } else {
                $delimiter = $this->_SEPARADOR_SQL;
                $queries_array = explode($delimiter, $queries);
                
                foreach ($queries_array as $query){
                    $query = trim($query);
                    if($query != ""){
                        $queries_list[] = $query; //.$delimiter;
                        $xCount++;
                    }
                }
            }
            return $queries_list;
        }
        
        /**
         * Function to recover the rows number of last executed query.
         * @return int Number of returned rows
         */
        private function num_rows(){
            if($this->resource){
                $n = mysql_num_rows($this->resource);
                if (mysql_errno() != 0) $this->showError();
                if ($n == 0 || $n === false || $n == "" || $n == NULL) return 0;

                return $n;
            }
        }
        
        /**
         * Function to recover the affected rows number of last executed query. This value only change when a UPDATE or DELETE sentence is executed.
         * If you execute several sentences with 'prepare' parameter seted to 'true', this value will be the sum of all queries.
         * @return int Number of affected rows
         */
        private function affected_rows(){
            $n = mysql_affected_rows();
            if (mysql_errno() != 0) $this->showError();
            if ($n == 0 || $n  == -1 || $n == "" || $n == NULL) return 0;
            
            return $n;
        }
        
        /**
         * Function to update and show the last occurred error.
         * @return void
         */
        public function showError(){
                    error_reporting(E_ALL^E_NOTICE);
                    $call = debug_backtrace();
                    for($xCount = 1; $xCount < 10; $xCount++){
                        if($call[$xCount]['function'] != "query" 
                        && $call[$xCount]['function'] != "showError" 
                        && $call[$xCount]['function'] != "getValue" 
                        && $call[$xCount]['function'] != "getListValues"
                        && substr($call[$xCount]['function'], 0, 7) != "include") break;
                    }
                    
                    $this->last_error_id  = mysql_errno();
                    $this->last_error_msg = mysql_error();
                    $this->error_class    = mysql_real_escape_string($call[$xCount]['class']);
                    $this->error_line     = $call[$xCount-1]['line'];
                    $this->error_method   = $call[$xCount]['function'];
                    
                    $siteError = '<b style="color:'.$this->_ERROR_COLOR.'">CLASS</b>: '.$this->error_class.'<br /><b style="color:'.$this->_ERROR_COLOR.'">LINE: </b>'.$this->error_line.'<br /><b style="color:'.$this->_ERROR_COLOR.'">METHOD: </b>'.$this->error_method;
                    
                    if (strpos($this->_IGNORE_ERRORS, (string) mysql_errno()) !== false){
                            if ($this->_SHOW_WARNING_ERROR){
                                    if ($this->_SHOW_CONTROL_MESSAGES && $this->_SHOW_IGNORED_ERRORS) $this->error_text = '<b style="color:'.$this->_WARNING_COLOR.'">WARNING</b>:<br> Error: ' . mysql_errno() . ": " . mysql_error()."<br>".'<i>'.$sentence."</i><br>\n";
                                    if ($this->_STOP_WARNING_ERROR && $this->_SHOW_IGNORED_ERRORS) return false;
                            } elseif($this->_SHOW_CONTROL_MESSAGES) {
                                    $this->insertEntryLog('ERROR '.$this->last_error_id . ': '.$this->last_error_msg);
                                    $this->error_text = '<b style="color:'.$this->_ERROR_COLOR.'">FATAL ERROR</b>:<br>Error: ' . $this->last_error_id . ": " . $this->last_error_msg . '<br /><b style="color:'.$this->_ERROR_COLOR.'">QUERY: </b>'.$this->last_query."<br />$siteError<br />";
                            }
                    } elseif($this->_SHOW_CONTROL_MESSAGES) {
                            $this->insertEntryLog('ERROR '.$this->last_error_id . ': '.$this->last_error_msg);
                            $this->error_text = '<b style="color:'.$this->_ERROR_COLOR.'">FATAL ERROR</b>:<br>Error: ' . $this->last_error_id . ": " . $this->last_error_msg . '<br /><b style="color:'.$this->_ERROR_COLOR.'">QUERY: </b>'.$this->last_query."<br />$siteError<br />";
                    }
        }

        /**
         * Function to get next auto increment value from a table.
         * @param string $table Table name
         * @param string $db Database name. By default the database name used will be the seted by _CURRENT_DB variable.
         * @return string Return the next value
         */
        public function getNextAutoIncrement($table, $db=""){
            if($db == "") $db = $this->_CURRENT_DB;
            return $this->getValue("SELECT AUTO_INCREMENT FROM information_schema.tables WHERE table_name = '".$table."' AND table_schema = '".$db."' ;");
        }
                
        /**
         * Funtion to recover a specific value. Make the request to database sent by first parameter and, if the query
         * is a type of SHOW, EXPLAIN, ... allow choice the column to return setting the second parameter.
         * If the result is empty, by default the value seted into _EMPTY_FIELD_BY_DEFAULT is returned.
         * Example:
         *      $mysql->getValue("SELECT name FROM customers WHERE id = 1;");
         *      $mysql->query("SHOW FULL COLUMNS FROM customers", 1);
         * @param string $sentence Query to execute.
         * @param int $field_number Column to return
         * @return string|number Resulting value.
         */
        public function getValue($sentence, $field_number = 0){
            $result = $this->query($sentence);

            if ($this->selected_rows == 0){
                return $this->_EMPTY_FIELD_BY_DEFAULT;
            } else {
                $row = mysql_fetch_row($result);
                return $row[$field_number];
            }
        }

        /**
         * Escapes special characters in a string for use in SQL statements referenced by $this->resource (the MySQL connection identifier)
         * @param string $sentence Query to execute.
         * @return string Return string escaped.
         */
        public function real_escape($sentence){
            return mysql_real_escape_string($sentence);
        }

        /**
         * Make the request to database and returns a list with the values of the requested fields.
         * The list is a array which can then be retrieved using the 'list()' PHP instruction, or treat it as a PHP array in every another issue.
         * If no match returns the value set by default _EMPTY_FIELD_BY_DEFAULT.
         * Example:
         *      // Later of execute the next query, you can recover this values like $id and $name.
         *      list($id, $name) = $this->getValues('SELECT id, name FROM customers WHERE id = 1;');
         * @param string $sentence Query to execute.
         * @return array Array with the result of execute the query.
         */
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

        /**
         * Function to delete rows from a table
         * @param string $table Table name.
         * @param string $cond String with the conditional sentence.
         * Example:
         *      $mysql->delete("customers", "id = 1");
         */
        public function delete($table, $cond){
            $sentence = "DELETE FROM ".$table." WHERE ".$cond;
            $this->query($sentence);
        }
        
        /**
         * Retrieves results referenced by $this->resource (the MySQL connection identifier) as an associative array of numbers, names, or both.
         * Example:
         *      $result = $mysql->query("SELECT * FROM names WHERE 1;");
         *      while($row = $mysql->fetchArray()){
         *          echo $row[0]." ".$mysql->utf8($row['name']);
         *      }
         * @param int $type Is optional. The sent parameter indicates how the partnership will be. Its values can be MYSQL_NUM, MYSQL_ASSOC or MYSQL_BOTH. By default, MYSQL_BOTH is used.
         * @return array
         */
        public function fetchArray($type=MYSQL_BOTH){
            $result = @mysql_fetch_array($this->resource, $type);
            if(mysql_errno() != 0){
                $this->showError();
            }
            
            return $result;
        }
        
        /**
         *  Retrieves results referenced by $this->resource (the MySQL connection identifier) as an object.
         * @param string $class IS the name of the class to instantiate to set the properties and return. In other words, it indicates the object class name resulting where will be transferred.
         * @return object|stdClass
         */
        function fetchObject($class=""){
            if($class == "") $result = @mysql_fetch_object($this->resource);
            else  $result = @mysql_fetch_object($this->resource, $class);
            
            if(mysql_errno() != 0){
                $this->showError();
            }
            
            return $result;
        }

        /**
         * Recover a specific field name of a column. The source to extract this information is the associate to $this->resource.
         * @param int $num indicates the field number to read the name
         * @return string
         */
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

        /**
         * Function to export, complete or partially, a database.
         * Examples:
         *      $mysql->export("export.txt");
         *      $mysql->export("export.txt", true);
         *      $mysql->export("export.txt", false, 'enterprises,customers', 'bz2');
         * @param string $exportfilename The target filename.
         * @param bool $exportdrop It is optional. Indicates if the table must be dropped before to re-create it.
         * @param bool|array $exporttables It is optional. Its a array that contains the tables of the database that will be stored. You can specify a 'false' value to indicate that all tables of the database are exported. Its default value is false.
         * @param bool $exportcompresion It is optional. If this parameter is seted to 'true', the file will be compressed.
         */
        public function export($exportfilename, $exportdrop=false, $exporttables=false, $exportcompresion=false){
            // Definimos la base de datos de desarrollo o produción
            if($_SERVER['HTTP_HOST'] == "localhost"){
                $bd = self::_DATABASE_NAME_DEVELOPMENT;
            } else {
                $bd = self::_DATABASE_NAME_PRODUCTION;
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
# | MySQL Class ".$info['dumpversion']."
# | por islavisual <comercial@islavisual.com>
# |
# | Generado el ".$info['fecha']." a las ".$info['hora']." por el usurio '$usurio'
# | Servidor: ".$_SERVER['HTTP_HOST']."
# | Browser: ".$aux['browser']." ".$aux['version']."
# | SO: ".$aux['so']."
# | IP: ".$aux['ip']."
# | MySQL Version: ".$info['mysqlver']."
# | PHP Version: ".$info['phpver']."
# | Base de datos: '$bd'
# | Tablas: ".$info['tablas']."
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
        
        /**
         * Free / liberate all memory referenced by $this->resource (the MySQL connection identifier). Only needs to be requested if you are executing queries that required too much memory. The natural behaviour is free / liberate all the memory when the script is ended
         * Example:
         *      $mysql->free();
         * @param void
         * @return void
         */
        public function free(){
            mysql_free_result($this->resource);
        }
        
        /**
         * Close MySQL connection referenced by $this->resource (the MySQL connection identifier).
         * Example:
         *      $mysql->disconnect();
         * @param void
         * @return void
         */
        public function disconnect(){ 
            if ($this->resource){ 
                return mysql_close($this->resource); 
            } 
        }

        /**
         * Check that does not exists bad words in code sent. Bad word is equivalent to prohibited sentences. For example this function is recommended when you want disable CREATE or DROP sentences.
         * If file has one of array words returns a message error and execution is give by terminated.
         * Example:
         *      $lines = file('export.sql');
         *      $badWords = $mysql->checkBadWords($lines);
         * @param type $array Array of queries that contain the code to check.
         * @return boolean If return value is 'true' means the code contain bad words.
         */
        public function checkBadWords($array){
            $badWords = array('CREATE DATABASE', 'DROP DATABASE', 'USE');
            
            $halt = false;
            foreach($array as $line){
                $subLines = explode(";",$line);
                foreach($subLines as $subLine){
                    $subLine = " ".utf8_decode(trim($subLine));
                    foreach($badWords as $word){
                        $place = strpos($subLine, " ".$word." ");
                        if($place !== false){
                            $halt = true;
                            break;
                        }
                    }
                    if($halt) break;
                }
                if($halt) break;
            }
            
            if($halt) return $word;
            
            return false;
        }
        
        /**
         * Clear text and prepare to execute.
         * @param type $text Is the array / text with the queries SQL
         * @return string Returns a string clean of commets and white blanks
         */
        public function clean($text){
            if(!is_array($text)){
                $lines = explode("\n", $text);
            } else {
                $lines = $text;
            }
            
            $linesAux = array();
            foreach ($lines as $line){
                $line = trim($line);
                if($line != ""){
                    if(substr($line, 0, 2) != '--' && $line != "" && substr($line, 0, 3) != '/*!'){
                        $linesAux[]= $line;
                    } elseif(trim(substr($line, 0, 3)) == '/*!'){
                        $linesAux[]= substr($line, stripos($line, " "), stripos($line, "*/")-stripos($line, " ")).";";
                    }
                }
            }
            $lines = implode("\n", $linesAux);
            $lines = explode(";", $lines);
            $str = "";
            foreach ($lines as $line){
                if(trim($line)) $str .= trim($line).";\n";
            }
            
            return $str;
        }
    }
?>
