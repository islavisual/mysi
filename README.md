mysi
====

PHP Class to management databases in MySQL. Includes, among other things, basic functions and export in plain text and compressed

With this simple but complete PHP class can from simple queries like getting a single value, to export the DB to a text file compressed or uncompressed with several added advantages such as that at all times, we know the number of rows or tuples selected, deleted or updated and the execution time of the query and, all with very simple instructions.

Way to use
----------
   include "../../clases/mysi.php";<br>
   $mysql = new mysi;


Then it's a matter of making calls to the functions you want to use as a PHP class either:
   $mysql->connect();

Just then, first of all is to set the user, password and database associated to the class. The class can automatically handle development and production environments only by changing the values of the following constants.

We have detail description in http://www.islavisual.com/articulos/desarrollo_web/clase-en-php-para-mysql-de-islavisual URL, although is writed in spanish.

mysi Scheme Compare (beta Version)
==================================

Compare Two schemes databases. The first parameter is the source and the  second parameter is the target.

mysiSC consume little memory and is very fast.

You can configure variables _EXECUTE_ALTER_TABLE, _EXECUTE_CREATE_FUNCTION, _EXECUTE_CREATE_INDEX, _EXECUTE_CREATE_PROCEDURE, _EXECUTE_CREATE_TABLE, _EXECUTE_CREATE_TRIGGER , _EXECUTE_CREATE_VIEW, _GET_VARIABLES, _DATATYPES  to custom the different search modes the tool.

If _EXECUTE_ALTER_TABLE is set to TRUE, the tool will execute the sentences type ALTER  automatically.

So will _EXECUTE_CREATE_FUNCTION, _EXECUTE_CREATE_INDEX, _EXECUTE_CREATE_PROCEDURE, _EXECUTE_CREATE_TABLE, _EXECUTE_CREATE_TRIGGER, _EXECUTE_CREATE_VIEW statements.

Way to use
----------
mysiSC::$_GET_VARIABLES = false;<br>
mysiSC::compareDump('database1_name', 'database2_name');<br>
