<?php



namespace Application\Controllers;



use System\Controller;



class AuthController extends Controller\BaseController {




	private $password = 'Bu98//asR';
	private $username = 'KattenMedHatten';
	
	private $errors = array();




	public function index()
	{
		if (!empty($_SESSION['AUTH']))
		{
			header('location: index.php');
		}
		$this->template->errors = empty($this->errors) ? array('&nbsp;') : $this->errors;
		$this->template->render(['header', 'login', 'footer']);
	}


	
	public function auth()
	{
		if (self::validateForm())
		{
			if (empty($_SESSION['ATTEMPTS']) || $_SESSION['ATTEMPTS'] < 6)
			{
				if ($_POST['username'] !== $this->username || $_POST['password'] !== $this->password)
				{
					$errors['AUTH'] = "Invalid Username/Password";
					$_SESSION['ATTEMPTS'] = (empty($_SESSION['ATTEMPTS']) ? 1 : $_SESSION['ATTEMPTS'] + 1);
				}
				else
				{
					$_SESSION['ATTEMPTS'] = array();
					$_SESSION['AUTH'] = '1';
					header('location: uploadr.localhost/index.php');
				}
			}
			else
			{
				$errors['ATTEMPTS'] = "Locked Out: Maximum Login Attempts Exceeded";
			}
			
			if (!empty($errors))
			{
				$this->errors = $errors;
			}
		}
			
		self::index();
	}



	public function logout()
	{
		if (!empty($_SESSION['AUTH']))
		{
			$_SESSION = array();
			unset($_SESSION);
		}

		self::index();
	}





	private function validateForm()
	{
		if (isset($_POST['submit']))
		{
			if (empty($_POST['username']))
			{
				$errors['EMPTY'] = "Missing Field";
			}
			if (empty($_POST['password']))
			{
				$errors['EMPTY'] = "Missing Field";
			}
			
			if (!empty($errors))
			{
				$this->errors = $errors;
				return false;
			}
			else
			{
				return true;
			}
		}
	}



}




