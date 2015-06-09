#mysi 1.03

MYSI is a PHP class to management databases in MySQL. 

With this PHP class simple, but complete, can be make traditional queries, send to array with the all query rows, getting a single value, recover in every moment the last ID inserted, getting the rows affected of last update or delete and profit additional of functionalities like dates converter, export the DDBB to a text file compressed or uncompressed, save query logs automaticly, ...

This class can automatically handle development and production environments only by changing the values of the following constants.


#Install
>Copy all files into repository or directory.

>Update / modify the __config.php__ file with your users, passwords and database names for Development and Production.

>In addition, you can update / modify the time variables, dat variables, error variables and codification into __mysi.php__ file with your users, passwords and database names for Development and Production.

#Way to use
```php
   include "mysi/mysi.php";
   $mysi = new MYSI();
   $mysi->connect('database_name');
   $array = $mysi->query("SELECT id FROM `database_name` WHERE field_name LIKE '%value%'", ARRAY_A);
   foreach($array as $key => $value){
      echo $key." contains ".$value;
   }
```

#Variables Description
##Error Variables
#####$_IGNORE_ERRORS
>Is a list of Numbers of MySQL errors separated by comma that will be managed in a special way. If you want to know what and how errors manage, you can to see the http://dev.mysql.com/doc/refman/5.0/es/error-handling.html URL.

#####$_WARNING_COLOR
>Color to show the WARNING messages.

#####$_ERROR_COLOR
>Color to show the ERROR messages.

#####$_SHOW_WARNING_ERROR
>If this variable is set to 'true', WARNING messages will be displayed.

#####$_SHOW_IGNORED_ERRORS
>If this variable is set to 'false', IGNORED messages aren't displayed. Otherwise, the messages, will be displayed as the others.

#####$_SHOW_CONTROL_MESSAGES
>If this variable is set to 'true', ERROR messages will be displayed.

#####$_STOP_WARNING_ERROR
>If a warning error is found and this variable is seted to 'true', the application execution is forced to stop execution.

#Methods Description
##checkBadWords
__bool checkBadWords($array)__

>Check that does not exists bad words in code sent. Bad word is equivalent to prohibited sentences. For example this function is recommended when you want disable CREATE or DROP sentences.

> If file has one of array words returns a message error and execution is give by terminated. 
> If return value is 'true' means the code contain bad words.*

#####Parameters:
* array $array - Array of queries that contain the code to check.

#####Example
```php
$lines = file('export.sql');
$badWords = $mysql->checkBadWords($lines);
```

##checkToken
__bool checkToken( string $string, [string $token = ""])__

> Function to compare the sent token into $token and the string returned through by $string.

> Return a boolean value, 'true' if both tokens are the same, 'false' in another issue.

#####Parameters
>string $string - Original string to compare.

>string $token - Encrypted string to compare.

#####Example
```php
$mysql->checkToken("Esto es una prueba", "6qblJRamKigUBqqlqWgTQ==");
```

##clean
__string clean( type $text)__

> Clear text and prepare to execute.

> Returns a string clean of commets and white blanks.

#####Parameters
>type $text - Is the array / text with the queries SQL.

#####Example
```php
$mysql->clean("/*!40000 ALTER TABLE `sprint` ENABLE KEYS */; SELECT ...");
```

##compareTokens
__bool compareTokens( string $token1, [string $token2 = ""])__

> Function to check two tokens. If $token1 is empty take to compare the returned token by the _ENCODED_TOKEN

> Return a boolean value, 'true' if both tokens are the same, 'false' in another issue.

#####Parameters:
>string $token1 - First token to compare.

>string $token2 - Second token to compare.

#####Example
```php
$mysql->compareTokens("6qblJRamKigUBqqlqWgTQ==");
```

##connect
__void connect( [string $db = ""])__
> Function to connect with the database.

> This function no return anything

#####Parameters:
>Example: // Later of execute the next query, you can recover this values like $id and $name. list($id, $name) = $this->getValues('SELECT id, name FROM customers WHERE id = 1;');string $db - Name of database to connect

#####Example
```php
    $mysi->connect();
```

##createTableLog
__resource createTableLog()__

> Function to create the LOG data table.

> This function hasn't parameters and not return anything.

#####Example
```php
$mysql->createTableLog();
```

##decodeToken
__string decodeToken( string $token)__

>Function to decrypted tokens.

>Returns a string decrypted.

#####Parameters:
>string $token - String to decode

#####Example
```php
$mysql->decodeToken("jHd8dnWBUVtNd4Nvf1CcnKKhlKTlJsgn1GflpKMm6qblJRamKigUBqqlqWgTQ==");
```

##delete
__void delete( string $table, string $cond)__

>Function to delete rows from a table

> This function no returns anything.

#####Parameters:
>string $table - Table name.

>string $cond - String with the conditional sentence. 

#####Example
```php
$mysql->delete("customers", "id = 1");
```

##disconnect
__void disconnect()__
> Close MySQL connection referenced by $this->resource (the MySQL connection identifier). 

> This function no returns anything.

#####Parameters:
>Has no parameters.

#####Example
```php
    $mysi->connect();
```

##elapsedTime
__array elapsedTime( date $dInit, date $dEnd, [string $dInit_format = ""], [string $dEnd_format = ""])__

>Function to calculate the days difference between two dates like days, hours, minutes and seconds.

>Will be returned an array with the format array("d" => 0, "h" => 1, "m" => 33, "s" => 2) with the result.

#####Parameters:
>$dInit date $dInit - Initial date.

>$dEnd date $dEnd - Final date.

>string $dInit_format - Is Optional. * If is empty, by default take the local format defined into class. The possible formats are defined in _FORMAT_DATE_FRMWRK and _FORMAT_DATETIME_FRMWRK depends of if the values is time or date type.

>string $dEnd_format - Is Optional. * If is empty, by default take the local format defined into class. The possible formats are defined in _FORMAT_DATE_FRMWRK and _FORMAT_DATETIME_FRMWRK depends of if the values is time or date type.

##encodeToken
__string encodeToken( string $string)__
 
>Function to encrypted tokens. The algorithm is made through to _TOKEN_KEY constant to make the sent string.

>Returns String encrypted.

#####Parameters:
* string $string - String to encode

#####Example
```php
$mysql->encodeToken("Complete. SELECT * FROM blog_tags WHERE 1;");
```

##existsToken
__bool existsToken( [string $token = ""])__

>Function to check if sent token already is into database.

>Returns true if exists, false in another issue.

#####Parameters
>string $token - Encrypted string to check.

#####Example
```php
$mysql->existsToken("6qblJRamKigUBqqlqWgTQ==");
```

##export
__void export( string $exportfilename, [bool $exportdrop = false], [bool|array $exporttables = false], [bool $exportcompresion = false])__

>Function to export, complete or partially, a database.

> This function no returns anything.

#####Parameters:
>string $exportfilename - The target filename.

>bool $exportdrop - It is optional. Indicates if the table must be dropped before to re-create it.

>bool|array $exporttables - It is optional. Its a array that contains the tables of the database that will be stored. You can specify a 'false' value to indicate that all tables of the database are exported. Its default value is false.

>bool $exportcompresion - It is optional. If this parameter is seted to 'true', the file will be compressed.


#####Examples
```php
$mysql->export("export.txt"); 
$mysql->export("export.txt", true); 
$mysql->export("export.txt", false, 'enterprises,customers', 'bz2');
```

##fetchArray
__array fetchArray( [int $type = MYSQL_BOTH])__

>Retrieves results referenced by $this->resource (the MySQL connection identifier) as an associative array of numbers, names, or both.

>This function no returns anything.

#####Parameters
>int $type - Is optional. The sent parameter indicates how the partnership will be. Its values can be MYSQL_NUM, MYSQL_ASSOC or MYSQL_BOTH. By default, MYSQL_BOTH is used.

#####Example
```php
$result = $mysql->query("SELECT * FROM names WHERE 1;"); 
while($row = $mysql->fetchArray()){ 
    echo $row[0]." ".$mysql->utf8($row['name']); 
}
```

##fetchObject
__object|stdClass fetchObject( [string $class = ""])__

>Retrieves results referenced by $this->resource (the MySQL connection identifier) as an object.

>This function no returns anything.

#####Parameters
* string $class - IS the name of the class to instantiate to set the properties and return. In other words, it indicates the object class name resulting where will be transferred.

##field_name
__string field_name( int $num)__

>Recover a specific field name of a column. The source to extract this information is the associate to $this->resource.

>This function no returns anything.

#####Parameters
>int $num - indicates the field number to read the name.* 
 
##free
__void free()__

>Free / liberate all memory referenced by $this->resource (the MySQL connection identifier). Only needs to be requested if you are executing queries that required too much memory. The natural behaviour is free / liberate all the memory when the script is ended

#####Parameters
>Has no parameters.

#####Example
```php
$mysql->free();
```

##getInfo
__array getInfo()__

>Function to recover the client data that browse by the system / web.

>Recover, between another data, the name and version browser, IP and operative system. To use this functionality is necessary have loaded previously the getInfo Class. In another words, insert into your code the next line code: include "getInfo.class.php"; 

>Returns an array with all information of user.

#####Parameters
>Has no parameters.

#####Example
```php
$mysql->getInfo();
```

##getListValues
__array getListValues( string $sentence)__

>Make the request to database and returns a list with the values of the requested fields.

>The list is a array which can then be retrieved using the 'list()' PHP instruction, or treat it as a PHP array in every another issue. If no match returns the value set by default _EMPTY_FIELD_BY_DEFAULT. 

>Returns an array with the result of execute the query.
    
#####Parameters
>string $sentence - Query to execute.

#####Example
```php
// Later of execute the next query, you can recover this values like $id and $name. 
list($id, $name) = $this->getValues('SELECT id, name FROM customers WHERE id = 1;');
```

##getNextAutoIncrement
__string getNextAutoIncrement( string $table, [string $db = ""])__

>Function to get next auto increment value from a table.

>Returns the next value.
    
#####Parameters:
>string $table - Table name.

>string $db - Database name. By default the database name used will be the seted by _CURRENT_DB variable.

#####Examples
```php
$mysql->getNextAutoIncrement('customers');
$mysql->getNextAutoIncrement('customers', 'database_name');
```

##getValue
__string|number getValue( string $sentence, [int $field_number = 0])__

>Funtion to recover a specific value. Make the request to database sent by first parameter and, if the query is a type of SHOW, EXPLAIN, ... allow choice the column to return setting the second parameter.

>If the result is empty, by default the value seted into _EMPTY_FIELD_BY_DEFAULT is returned. 

>Returns the resulting value.
    
#####Parameters
>string $sentence - Query to execute.

>int $field_number - Column to return

Examples
```php
$mysql->getValue("SELECT name FROM customers WHERE id = 1;"); 
$mysql->getValue("SHOW FULL COLUMNS FROM customers", 1);
```

##insertEntryLog
__void insertEntryLog( [string $event = ''])__

>Function to insert a event into LOG table. In addition save, the name and version browser, IP and operative system and page...

>This function no returns anuthing

#####Parameters:
>string $event - String to save into database.

#####Example
```php
$mysql->insertEntryLog("Access to");
```

##isDate
__bool isDate( string $value, [string $format = ""])__

>Check if the sent string is a date type. You must send the format to check.

>The parameters than you can to use into $format variable are the PHP same that. 

>Returns true or false.

#####Parameters
>string $value - String to check.

>string $format - Format defined.


#####Examples
```php
$mysql->isDate("2012/33", "Y/d");    //is transformed to 02-02-2012, like result will be return 'true'.
$mysql->isDate("oct-01", "m-d");     //is transformed to 01-10-2012, like result will be return 'true'.
$mysql->isDate("oct", "m");          //will be return error, like result will be return 'false'.
$mysql->isDate("31", "d");           //will be return error, like result will be return 'false'.
$mysql->isDate("31/10/2012", '');    //is transformed to 31-10-2012, like result will be return 'true'.
```
##isNumber
__bool isNumber( number $value)__

>Check if the sent value is a number

>Returns true or false.

Parameters:
>number $value - Value to test

##isString
__bool isString( number $value)__

>Check if the sent value is a string

>Returns true or false.

Parameters:
>string $value - Value to test

##mkTimeFormat
__int mkTimeFormat( string $value, [string $format = ""])__

>Convert a date format, from format received by $format to UNIX format.

>The values with only characters or numbers produce a error. You must to have caution because this function has behaviours defined to help into the development. 

>If the string sent is "2012/33", will be returned 02-02-2012. 

>If the string sent is "oct-01", will be returned 01-10-2012. 

>If the string sent is "2012-oct", will be returned 01-10-2012. 

>Returns the UNIX timestamp

#####Parameters:
>string $value - Is string to convert.

>string $format - Is target format.

#####Example
```php
$mysql->mkTimeFormat(date("d-m-Y H:i:s"), "d-m-Y H:i:s");
```

##prepare_queries
__array prepare_queries( string $queries)__

>Function to prepare complex instructions or several queries simultaneously.

>Returns string with the right format to execute into server.

#####Parameters
>string $queries - List of queries or sentence of lines multiple.

#####Example
```php
$query="
DELIMITER |
CREATE TRIGGER testref BEFORE INSERT ON test1
FOR EACH ROW BEGIN
INSERT INTO test2 SET a2 = NEW.a1;
DELETE FROM test3 WHERE a3 = NEW.a1;
UPDATE test4 SET b4 = b4 + 1 WHERE a4 = NEW.a1;
END;
|
DELIMITER ;";
$mysql->prepare_queries($query);
```
##query
__resource query( string $query, [string $output = ""], [bool $prepare = true])__

>Function to make requests to database.

>If _SHOW_CONTROL_MESSAGES is setet to 'true', will be show the ERROR messages into screen. If _SHOW_WARNING_ERROR is setet to 'true', will be show the WARNING messages into screen. If _STOP_WARNING_ERROR is setet to 'true', the execution is stopped like if had have occurred a fatal error.

>Returns a pointer to resulting object.

#####Parameters:
>string $query - Sentence to execute.

>string $output - Type of data to return. The possible options are: ARRAY_A, ARRAY_N, OBJECT.

>bool $prepare - For execute sentences of lines multiple like FUNCTIONS or PROCEDURES

#####Example
```php
$mysi->query("INSERT INTO custumers (`id` , `name`) VALUES (1, 'Islavisual')`");
$mysi->query("SELECT * FROM `customers` WHERE name LIKE '%IS%'");
// Recover users into array
$users = $mysi->query("SELECT * FROM `user` WHERE 1 AND `status` = 1", ARRAY_A);
```

##real_escape
__string real_escape( string $sentence)__

>Escapes special characters in a string for use in SQL statements referenced by $this->resource (the MySQL connection identifier).

>Return string escaped.

#####Parameters
>string $sentence - Query to execute.

#####Example
```php
$mysi->real_escape(sprintf("SELECT * FROM users WHERE user='%s' AND password='%s'");
```

##showError
__void showError()__

>Function to update and show the last occurred error.

>This function no returns anything.

#####Parameters
>No has parameters.

##time2Array
__array|bool time2Array( $date $date, $format $format)__

>Function to convert a date of type string to array format.

>The parameters than you can to use into $format variable are the PHP same that. 

#####Parameters:
>string $date - String date type to convert.

>string  $format - String with the input format.

#####Example
```php
@extract($this->time2Array($value,"Y-m-d"));
```

##toDateFormat
__toDateFormat( string $value, [string $format_source = ""], [string $format = ""])__

>Convert a string with date format, from format seted by $format_source variable to $format variable.

>The values with only characters or numbers produce a error. 

>If $format_source is empty, by default take the local format defined into class. The possible formats are defined in _FORMAT_DATE_FRMWRK and _FORMAT_DATETIME_FRMWRK depends of if the values is time or date type. 

>If $format is empty, by default take the local format defined into class in the _FORMAT_DATETIME_DB variable You must to have caution because this function has behaviours defined to help into the development. 

>If the string sent is "2012/33", will be returned 02-02-2012. If the string sent is "oct-01", will be returned 01-10-2012. If the string sent is "2012-oct", will be returned 01-10-2012. 

>Returns a date in string format.

#####Parameters:
>string $value - String to transform.

>string $format_source - Source format.

>string $format - Target format.

#####Example
```php
$mysql->toDateFormat("2012-oct-10","Y-m-d", "d-m-Y");
```

##usedb (Deprecated function)
usedb( $db_name $db_name)__

>Select a database.

>This function no returns anything.

#####Parameters:
>string $db_name - Database name

##utf8
__string utf8( string $t)__

>Function to check if a text is encoded like ISO-8859-1. If the response is 'true', the string is coverted to UTF-8, otherwise, the text is decoded.

>Returns a string in UTF-8 format.

#####Parameters
>string $t - String to check

#####Example
```php
$mysql->utf8($row['name']);
```

##__get

__string __get( string $name)__

>Function allow to recover variables of private use from out of this class.

>To allow recover a variable, the variable must be declare into $_ALLOWED_VARS. If the variable name does not into this list, the system will deny the request and will show an error message.

By default, ```php$_ALLOWED_VARS``` contains completedIn, total_queries, last_insert_id, affected_rows and selected_rows vars.

>Returns the value of variable

#####Parameters
>string $name - The variable name to recover

We have detail description in http://www.islavisual.com/articulos/desarrollo_web/clase-en-php-para-mysql-de-islavisual URL, although is writed in spanish.
