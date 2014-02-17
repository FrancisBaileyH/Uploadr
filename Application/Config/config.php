<?php


/* Uploadr Config File */


$config = array();


/*
 * Directory all files are to be uploaded to
*/
$config['upload_dir'] = __DOC_ROOT . 'Uploads/';


/*
 *  Maximum file size as set in your php.ini
*/
$config['max_file_size'] = ini_get('upload_max_filesize');

 
/*
 * Maximum files to be uploaded @ a time
 * Should correspond with your php.ini
*/
$config['max_num_files'] = ini_get('max_file_uploads');


