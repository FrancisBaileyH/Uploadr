<?php




namespace System\Controller;



use System\View;



abstract class BaseController
{
	


	protected $registry;
	protected $template;



	public function __construct($registry)
	{
		$this->registry = $registry;
		$this->template = new View\Template($this->registry);
	}




	abstract function index();
}



?>
