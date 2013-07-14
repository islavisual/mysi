mysi
====

PHP Class to management databases in MySQL. Includes, among other things, basic functions and export in plain text and compressed

With this simple but complete PHP class can from simple queries like getting a single value, to export the DB to a text file compressed or uncompressed with several added advantages such as that at all times, we know the number of rows or tuples selected, deleted or updated and the execution time of the query and, all with very simple instructions.

Way to use
------------------
  include "../../clases/mySql.class.php" ;
	$mysql = new mySQL;


Then it's a matter of making calls to the functions you want to use as a PHP class either:
  $mysql->connect();

Just then, first of all is to set the user, password and database associated to the class. The class can automatically handle development and production environments only by changing the values of the following constants.

Contamos con una descripción detallada en el URL http://www.islavisual.com/articulos/desarrollo_web/clase-en-php-para-mysql-de-islavisual, aunque se escribe en español.
