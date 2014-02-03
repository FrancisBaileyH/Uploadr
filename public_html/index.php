<?php

session_start();


define('__DOC_ROOT', '/path/to/docroot/');


include(__DOC_ROOT.'System/autoload.php');
include(__DOC_ROOT.'Application/Config/config.php');


define('__UPLOAD_DIR', __DOC_ROOT.$config['upload_dir']);


$registry = new System\Registry();
$registry->config = $config;
$registry->csrf = new Lib\CSRF\CSRF_Protect();
$router = new System\Routing\Router($registry);
$router->loader();

?>
