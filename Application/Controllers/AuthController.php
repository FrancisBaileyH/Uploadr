<?php



namespace Application\Controllers;




class AuthController extends BaseController {




	private $password = '*';
	private $username = '*';
	
	


	public function index()
	{
		if (!empty($_SESSION['AUTH']))
		{
			header('location: index.php');
		}
		$this->template->errors = $this->ErrorHandler->getAllErrors();
		$this->template->render(['header', 'login', 'footer']);
	}


	
	public function auth()
	{
		if ($this->validateForm())
		{
			if (empty($_SESSION['ATTEMPTS']) || $_SESSION['ATTEMPTS'] < 6)
			{
				if ($this->request['POST']['username'] !== $this->username || $this->request['POST']['password'] !== $this->password)
				{
					$this->ErrorHandler->setErrors('Invalid Username/Password');
					$_SESSION['ATTEMPTS'] = (empty($_SESSION['ATTEMPTS']) ? 1 : $_SESSION['ATTEMPTS'] + 1);
				}
				else
				{
					$_SESSION['ATTEMPTS'] = array();
					$_SESSION['AUTH'] = '1';
				}
			}
			else
			{
				$this->ErrorHandler->setErrors('Locked Out: Maximum Login Attempts Exceeded');
			}
		}
			
		$this->index();
	}



	public function logout()
	{
		if (!empty($_SESSION['AUTH']))
		{
			$_SESSION = array();
			unset($_SESSION);
		}

		$this->index();
	}





	private function validateForm()
	{
		if (isset($this->request['POST']['submit']))
		{
			$flag = true;
			
			if (empty($this->request['POST']['username']) || empty($this->request['POST']['password']))
			{
				$this->ErrorHandler->setErrors('Missing Field');
				$flag = false;
			}
			
			return $flag;
		}
	}



}




