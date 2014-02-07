About
=====================================
Uploadr is a simple AJAX/PHP file uploader.  I made it as a simple application for personal use and practice.

Installation
=====================================
Installation is fairly simple.  You can just download the application and point your web root to public_html.  
From there you'll need to change the document root in index.php

    /* Index.php */
    <?php

    session_start();

    /*
      * Simply change /path/to/app to the appliaction direcory
      * I.e. /home/user/Uploadr/
    */
    define('__DOC_ROOT', '/path/to/app/');


    include(__DOC_ROOT.'System/autoload.php');
    include(__DOC_ROOT.'Application/Config/config.php');


    define('__UPLOAD_DIR', __DOC_ROOT.$config['upload_dir']);


    $registry = new System\Registry();
    $registry->config = $config;
    $registry->csrf = new Lib\CSRF\CSRF_Protect();
    $router = new System\Routing\Router($registry);
    $router->loader();

    ?>

By default config.php (located in Application/Config/ ) points all uploads to the Uploads directory included in the app and
outside of webroot.  You can change the directory at any time, just ensure, for security
purposes, that it's outside of your web root.

TroubleShooting
========================================
You need to make sure the Uploads directory, or whichever directory you choose to store your uploads, has sufficient
permissions to write and read files.

*Note
==============================================
I made this application before I truly understood the importance of unit tests.
As such, there are no unit tests. Use this app at your own discretion.  
