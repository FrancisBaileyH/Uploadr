<?php




namespace Application\Controllers;



use System\View;
use Application\Utility;
use Lib;



abstract class BaseController
{
	protected $template;
	protected $ErrorHandler;
	protected $CSRFProtect;
	

	public function __construct($registry)
	{
		$this->config = $registry->config;
		$this->template = new View\Template($registry);
		$this->ErrorHandler = new Utility\ErrorHandler();
		$this->CSRFProtect = $registry->csrf;
	}

	
}



?>
