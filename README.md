mysi
====
[Description in progress]
MYSI is a PHP class to management databases in MySQL. Includes, among other things, basic functions and export in plain text and compressed.

With this simple, but complete, PHP class can be make queries and getting a single value, get an array with the all rows, export the DDBB to a text file compressed or uncompressed, save query logs, recover in every moment the last ID inserted, rows affected, convert easyly dates from all formats to MySQL format, and more functionalities.

Way to use
----------
   <code>include "mysi.php";</code><br>
   <code>$mysi = new MYSI;</code>


Then it's a matter of making calls to the functions you want to use as a PHP class either:<br>
   <code>$mysi->connect("database_name");</code>

Just then, first of all is to set the user, password and database associated to the class. The class can automatically handle development and production environments only by changing the values of the following constants.

We have detail description in http://www.islavisual.com/articulos/desarrollo_web/clase-en-php-para-mysql-de-islavisual URL, although is writed in spanish.
