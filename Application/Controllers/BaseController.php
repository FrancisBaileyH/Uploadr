<?php




namespace Application\Controllers;



use System\View;
use Application\Utility;



abstract class BaseController
{
	


	protected $registry;
	protected $request = [];
	protected $template;
	protected $ErrorHandler;
	


	public function __construct($registry, $request)
	{
		$this->registry = $registry;
		$this->request = $request;
		$this->template = new View\Template($this->registry);
		$this->ErrorHandler = new Utility\ErrorHandler();
	}

	
}



?>
