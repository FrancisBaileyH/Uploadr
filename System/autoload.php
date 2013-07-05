<?php




function __autoload($class_name)
{

	$file_name = str_replace('\\', '/', $class_name);

	
	$file = __DOC_ROOT.$file_name.'.class.php'; 
	
	if (!is_readable($file))
	{
		die ("Unable to load file");
	}

	include_once ($file);
}


?>
