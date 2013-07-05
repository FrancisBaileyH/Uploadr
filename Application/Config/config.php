<?php





/* Uploadr Config File */




$config = array();





/*
	A whitelist of allowable file extensions to prevent
	Unwanted files from being uploaded
*/
$config['file_extension_whitelist'] = [
					'jpg', 'jpeg', 'mp4', 'mp3', 'gif', 'png', 'avi', 'txt', 'php', 'html', 'css',
				        'zip', 'tar', 'bz2', 'pdf'
				      ];



/*
	The root dir for file uploads
*/
$config['root_upload_dir'] = '/Uploads/';




/*
	Directory all files are to be uploaded to
*/
$config['upload_dir'] = __DOC_ROOT.'/Uploads/';




/*
	Maximum file size allowed
*/
$config['max_file_size'] = 524288000;





?>
