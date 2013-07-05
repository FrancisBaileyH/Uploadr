<?php

session_start();


$site_path = dirname(realpath(__FILE__));
define('__SITE_PATH', $site_path);
define('__DOC_ROOT', '/home/kattenclean/Uploadr/');


include(__DOC_ROOT.'System/autoload.php');
include(__DOC_ROOT.'Application/Config/config.php');


$registry = new System\Registry();
$registry->config = $config;
$uploadr = new System\Routing\Router($registry);
$uploadr->loader();


?>
