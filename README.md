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

>If file has one of array words returns a message error and execution is give by terminated. 

#####Return
_If return value is 'true' means the code contain bad words._

#####Parameters:
_array $array - Array of queries that contain the code to check._

#####Example
```php
$lines = file('export.sql');
$badWords = $mysql->checkBadWords($lines);
```

##checkToken
__bool checkToken( string $string, [string $token = ""])__

>Function to compare the sent token into $token and the string returned through by $string.

#####Return
>  Return a boolean value, 'true' if both tokens are the same, 'false' in another issue.

#####Parameters
>  string $string - Original string to compare.

>  string $token - Encrypted string to compare.

#####Example
```php
$mysql->checkToken("Esto es una prueba", "6qblJRamKigUBqqlqWgTQ==");
```

##clean
__string clean( type $text)__

Clear text and prepare to execute.
####Return
*  Returns a string clean of commets and white blanks.

####Parameters
* type $text - Is the array / text with the queries SQL
```php
$mysql->clean("/*!40000 ALTER TABLE `sprint` ENABLE KEYS */; SELECT ...");
```

##compareTokens
__bool compareTokens( string $token1, [string $token2 = ""])__

Function to check two tokens. If $token1 is empty take to compare the returned token by the _ENCODED_TOKEN

####Return
*  Return a boolean value, 'true' if both tokens are the same, 'false' in another issue.

####Parameters:
*  string $token1 - First token to compare.
*  string $token2 - Second token to compare.


##connect
__void connect( [string $db = ""])__

####Return:
*  void

####Parameters:
*  string $db - Name of database to connect
```php
    $mysi->connect();
```

##createTableLog
__resource createTableLog()__

Function to create the LOG data table.

```php
$mysql->createTableLog();
```
####Return:
* void

####Parameters:
*  void


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
