<?php





/* Uploadr Config File */




$config = array();




/*
	Directory all files are to be uploaded to
*/
$config['upload_dir'] = 'Uploads/';




/*
	Maximum file size allowed
*/
$config['max_file_size'] = 209715200;//ini_get('upload_max_file_size');



 
/*
 * Maximum files to be uploaded @ a time
 * Should correspond with your php.ini
*/
$config['max_num_files'] = 10;





?>
