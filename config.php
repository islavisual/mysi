<?php
    if($_SERVER['SERVER_NAME'] == 'localhost'){
        //Lucas' config
        define('LOCALHOST',true);
        define('DEVELOPMENT_ENVIRONMENT',false);
        define('PRODUCTION_ENVIRONMENT',false);
        define('DB_USER','user');
        define('DB_PASSWORD','password');
        define('DB_NAME','taskman');
    } else {

        define('LOCALHOST',false);
        define('DEVELOPMENT_ENVIRONMENT',false);
        define('PRODUCTION_ENVIRONMENT',true);
        define('DB_USER','user_pro');
        define('DB_PASSWORD','password_pro');
        define('DB_NAME','evidaliahost_islavisual');
    }
?>
