<?php



namespace System\View;



class Template 
{



	private $registry;
	private $vars = array();
	private $content = array();



	function __construct($registry)
	{
		$this->registry = $registry;
	}



	public function __set($index, $value)
	{
		$this->vars[$index] = $value;
	}



	private function fetchView($name)
	{
		$path = __DOC_ROOT.'Application/Views/'.$name.'.php';
		
		if (!file_exists($path))
		{
			throw new \Exception('Template not found in'.$path);
		}

		if (!empty($this->vars) && is_array($this->vars))
		{
			extract($this->vars);
		}

		include($path);
	}


	
	public function render($templates = array())
	{	
		for ($i = 0; $i < count(array_keys($templates)); $i++)
		{
			$this->fetchView($templates[$i]);
		}
	}


}

?>
