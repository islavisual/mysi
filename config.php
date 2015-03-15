<?php
    if($_SERVER['SERVER_NAME'] == 'localhost'){
        //Lucas' config
        define('LOCALHOST',true);
        define('DEVELOPMENT_ENVIRONMENT',false);
        define('PRODUCTION_ENVIRONMENT',false);
        define('DB_USER','root');
        define('DB_PASSWORD','1123581321');
        define('DB_NAME','taskman');
    } else {

        define('LOCALHOST',false);
        define('DEVELOPMENT_ENVIRONMENT',false);
        define('PRODUCTION_ENVIRONMENT',true);
        define('DB_USER','islavisu');
        define('DB_PASSWORD','1123581321');
        define('DB_NAME','evidaliahost_islavisual');
    }
?>