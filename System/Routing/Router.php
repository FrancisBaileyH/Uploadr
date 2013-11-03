<?php



namespace System\Routing;




class Router
{



	private $allowed = [ 'index', 'auth'];
	private $registry;
	private $args = array();
	public  $action;
	public  $controller;
	public 	$request;



	public function __construct($registry)
	{
		$this->registry = $registry;
		$this->request = new Request();
		$this->request->setRequest($_GET, 'GET');
		$this->request->setRequest($_POST, 'POST');
		$this->request->setRequest($_FILES, 'FILES');
	}

	
	
	public function loader()
	{
		$file = $this->getController();
		$file = str_replace('\\', '/', $file);
		$file = __DOC_ROOT.$file;
		

		$class = $this->controller.'Controller';
		
		$object = new $class($this->registry, $this->request->getRequest());


		if (!is_callable(array($object, $this->action)))
		{
			$action = 'index';
		}
		else
		{
			$action = $this->action;
		}
		
						
		$object->$action();
	}



	private function getController()
	{
		$route = empty($_GET['route']) ? '' : $_GET['route'];
		
			
		if (!empty($route))
		{
			$parts = explode('/', $route);
					
			if (empty($parts[0]) || !in_array($parts[0], $this->allowed))
			{
				$this->controller = 'Application\Controllers\Index';
				$this->action = 'notFound';
			}
			else
			{
				$this->controller = 'Application\Controllers\\'.ucfirst(strtolower($parts[0]));
				
				if (!empty($parts[1]))
				{
					$this->action = $parts[1];
				}
			}
		}
		else
		{
			$this->controller = 'Application\Controllers\Index';
		}
		
		$this->filter();
		
			
	   	return $this->controller;
	}
	
	
	
	private function filter()
	{
		if (empty($_SESSION['AUTH']))
		{
			$this->controller = 'Application\Controllers\Auth';
		}
		if (!empty($_GET['dir']) && $_GET['dir'] == '.')
		{
			header('location: index.php');
		}
	}
	
	
	

}



?>
