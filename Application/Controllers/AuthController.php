<?php


namespace Application\Controllers;


class AuthController extends BaseController 
{

	private $password = '*';
	private $username = '*';


	public function index()
	{
		if (!empty($_SESSION['AUTH']))
		{
			header('location: index.php');
		}
		$this->template->csrf   = $this->CSRFProtect->getToken();
		$this->template->errors = $this->ErrorHandler->getAllErrors();
		$this->template->render(['header', 'login', 'footer']);
	}
	
	
	public function auth()
	{
		if ($this->validateForm())
		{
			if (empty($_SESSION['ATTEMPTS']) || $_SESSION['ATTEMPTS'] < 6)
			{
				if ($_POST['username'] !== $this->username || $_POST['password'] !== $this->password)
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
			
			header( 'location: /');
		}

		$this->index();
	}


	private function validateForm()
	{
		if (isset($_POST['submit']))
		{
			$flag = true;	
			
			if ( empty( $_POST['csrf'] ) || !$this->CSRFProtect->isTokenValid( $_POST['csrf'] ) )
			{
				$this->ErrorHandler->setErrors( "Invalid CSRF token, refresh the page and try again" );
				$flag = false;
			} 
			else if (empty($_POST['username']) || empty($_POST['password']))
			{
				$this->ErrorHandler->setErrors('Missing Field');
				$flag = false;
			}
			
			return $flag;
		}
	}



}




