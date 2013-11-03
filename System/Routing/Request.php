<?php



namespace System\Routing;



class Request 
{



	public $request = array();
		
	
	
	public function setRequest($request, $type)
	{
		$this->request[$type] = $request;
	}
	
	
	
	public function getRequest()
	{
		return $this->request;
	}
	
	
}



?>
