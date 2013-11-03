<?php




function __autoload($class_name)
{

	$file_name = str_replace('\\', '/', $class_name);

	
	$file = __DOC_ROOT.$file_name.'.php'; 
	
		
	if (!is_readable($file))
	{
		echo "Unable to load file: ".$file;
		exit(); 
	}

	include_once ($file);
}


?>
