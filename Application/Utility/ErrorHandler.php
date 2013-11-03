<?php



namespace Application\Utility;



class ErrorHandler
{



	public $errors = array();
	
	
	
	public function setErrors($msg)
	{
		$this->errors[] = $this->sanitizeErrors($msg);
	}
	
	
	
	private function sanitizeErrors($msg)
	{
		return htmlentities($msg);
	}
	
	
	
	public function getError($index)
	{
		return $this->errors[$index];
	}
	
	
	
	public function getAllErrors()
	{
		return $this->errors;
	}
	

}



?>
