<?php



namespace System\Routing;



use System\Controller;
use Application\Controllers;



class Router
{



	private $filter = [ 'index', 'auth'];
	private $registry;
	private $args = array();
	public  $action;
	public  $controller;



	function __construct($registry)
	{
		$this->registry = $registry;
	}



	public function loader()
	{
		$file = self::getController();
		$file = str_replace('\\', '/', $file);
		$file = __DOC_ROOT.$file;
			

		if (!is_readable($file))
		{
			die('404 Not Found');
		}

		include $file;

		$class = $this->controller.'Controller';
		
		$controller = new $class($this->registry);

		if (!is_callable(array($controller, $this->action)))
		{
			$action = 'index';
		}
		else
		{
			$action = $this->action;
		}

		$args = empty($this->args) ? '' : $this->args;
	

		$controller->$action($args);
	}



	private function getController()
	{
		$route = empty($_GET['route']) ? '' : $_GET['route'];
			
		if (!empty($route))
		{
			$parts = explode('/', $route);
					
			if (empty($parts[0]) || !in_array($parts[0], $this->filter))
			{
				$this->controller = 'Application\Controllers\Index';
				$this->action = 'notFound';
			}
			else
			{
				$this->controller = 'Application\Controllers\\'.ucfirst($parts[0]);
				if (!empty($parts[1]))
				{
					$this->action = $parts[1];
				}
				if (!empty($parts[2]))
				{
					for ($i = 2; $i < count($parts); $i++)
					{	
						$this->args[$i-2] = $parts[$i];
					}
				}
			}
		}
		else
		{
			$this->controller = 'Application\Controllers\Index';
		}
		if (empty($_SESSION['AUTH']))
		{
			$this->controller = 'Application\Controllers\Auth';
		}
		
	   	return $this->controller.'Controller.class.php';
	}



}



?>
