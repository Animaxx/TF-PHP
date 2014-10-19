<?php
// TFCore/TFConfig.php
return array(
    'zend' => array(
        'autoload_zend' => true,
        'db' => true,
        'dom' => true,
        'http_client' => true,
    ),
    'db' => array(
        'database_type' => 'mysql',
        'host' => 'localhost',
        'port' => 3306,
        'login' => 'root',
        'password' => 'demopassword',
        'database' => 'demodb',
        'when_error_goon' => false,
    )
);