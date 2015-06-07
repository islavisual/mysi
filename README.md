#mysi 1.02

MYSI is a PHP class to management databases in MySQL. Includes, among other things, basic functions and export in plain text and compressed.

With this simple, but complete, PHP class can be make queries and getting a single value, get an array with the all rows, export the DDBB to a text file compressed or uncompressed, save query logs, recover in every moment the last ID inserted, rows affected, convert easyly dates from all formats to MySQL format, and more functionalities.

Way to use
==========
   <code>include "mysi.php";</code><br>
   <code>$mysi = new MYSI;</code>


Then it's a matter of making calls to the functions you want to use as a PHP class either:<br>
   <code>$mysi->connect("database_name");</code>

Just then, first of all is to set the user, password and database associated to the class. The class can automatically handle development and production environments only by changing the values of the following constants.

In the definition class, every function, is commented through to PHP DOC to make easier the understanding every one.

#Methods
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
* string $string - Original string to compare.
* string $token - Encrypted string to compare.

#####Example
```php
$mysql->checkToken("Esto es una prueba", "6qblJRamKigUBqqlqWgTQ==");
```

##clean
__string clean( type $text)__

> Clear text and prepare to execute.

> Returns a string clean of commets and white blanks.

#####Parameters
* type $text - Is the array / text with the queries SQL.

#####Example
```php
$mysql->clean("/*!40000 ALTER TABLE `sprint` ENABLE KEYS */; SELECT ...");
```

##compareTokens
__bool compareTokens( string $token1, [string $token2 = ""])__

> Function to check two tokens. If $token1 is empty take to compare the returned token by the _ENCODED_TOKEN

> Return a boolean value, 'true' if both tokens are the same, 'false' in another issue.

#####Parameters:
*  string $token1 - First token to compare.
*  string $token2 - Second token to compare.

#####Example
```php
$mysql->compareTokens("6qblJRamKigUBqqlqWgTQ==");
```

##connect
__void connect( [string $db = ""])__
> Function to connect with the database.

> This function no return anything

#####Parameters:
*  string $db - Name of database to connect

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

> Returns a string decrypted.

#####Parameters:
* string $token - String to decode

#####Example
```php
$mysql->decodeToken("jHd8dnWBUVtNd4Nvf1CcnKKhlKTlJsgn1GflpKMm6qblJRamKigUBqqlqWgTQ==");
```

##delete
__void delete( string $table, string $cond)__

>Function to delete rows from a table

> This function no returns anything.

#####Parameters:
* string $table - Table name.
* string $cond - String with the conditional sentence. 

#####Example
```php
$mysql->delete("customers", "id = 1");
```

##disconnect
__void disconnect()__
> Close MySQL connection referenced by $this->resource (the MySQL connection identifier). 

> This function no returns anything.

#####Parameters:
*  Has no parameters.

#####Example
```php
    $mysi->connect();
```

##elapsedTime
__array elapsedTime( date $dInit, date $dEnd, [string $dInit_format = ""], [string $dEnd_format = ""])__

>Function to calculate the days difference between two dates like days, hours, minutes and seconds.

>Will be returned an array with the format array("d" => 0, "h" => 1, "m" => 33, "s" => 2) with the result.

#####Parameters:
* $dInit date $dInit - Initial date.
* $dEnd date $dEnd - Final date.
* string $dInit_format - Is Optional. * If is empty, by default take the local format defined into class. The possible formats are defined in _FORMAT_DATE_FRMWRK and _FORMAT_DATETIME_FRMWRK depends of if the values is time or date type.
* string $dEnd_format - Is Optional. * If is empty, by default take the local format defined into class. The possible formats are defined in _FORMAT_DATE_FRMWRK and _FORMAT_DATETIME_FRMWRK depends of if the values is time or date type.

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
* string $token - Encrypted string to check.

#####Example
```php
$mysql->existsToken("6qblJRamKigUBqqlqWgTQ==");
```

##export
__void export( string $exportfilename, [bool $exportdrop = false], [bool|array $exporttables = false], [bool $exportcompresion = false])__

>Function to export, complete or partially, a database.

> This function no returns anything.

#####Parameters:
* string $exportfilename - The target filename.
* bool $exportdrop - It is optional. Indicates if the table must be dropped before to re-create it.
* bool|array $exporttables - It is optional. Its a array that contains the tables of the database that will be stored. You can specify a 'false' value to indicate that all tables of the database are exported. Its default value is false.
* bool $exportcompresion - It is optional. If this parameter is seted to 'true', the file will be compressed.


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
* int $type - Is optional. The sent parameter indicates how the partnership will be. Its values can be MYSQL_NUM, MYSQL_ASSOC or MYSQL_BOTH. By default, MYSQL_BOTH is used.

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
* int $num - indicates the field number to read the name.* 
 
##free
__void free()__

>Free / liberate all memory referenced by $this->resource (the MySQL connection identifier). Only needs to be requested if you are executing queries that required too much memory. The natural behaviour is free / liberate all the memory when the script is ended

>Example: $mysql->free();

Parameters
>Has no parameters.










#Some Examples:
```php
    $mysi->connect();
    $mysi->usedb('database_name');
    $mysi->query("SHOW FULL COLUMNS FROM `database_name`");
    $mysi->getValue("SELECT id FROM `database_name` WHERE name='Paul'");
    $mysi->query("SHOW FULL COLUMNS FROM customers", 1);
    $mysi->real_escape(sprintf("SELECT * FROM users WHERE user='%s' AND password='%s'");
    $mysi->export("export.txt", false, 'enterprises,customers', 'bz2');
```

We have detail description in http://www.islavisual.com/articulos/desarrollo_web/clase-en-php-para-mysql-de-islavisual URL, although is writed in spanish.
