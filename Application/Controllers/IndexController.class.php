<?php



namespace Application\Controllers;



use System\Controller;



define('__UPLOAD_DIR', $this->registry->config['upload_dir']);



class IndexController extends Controller\BaseController
{


	
	private $errors = array();
	private $filesArray = array();
	private $dirsArray = array();
	private $dir = null;		



	
	public function index()
	{
		if (self::validateForm()) 
		{ 
			if (!self::upload())
			{
				$this->errors['upload_fail'] = "Error: Unable To Upload File At This Time";
			}
		}
		if (!empty($_GET['dir']) && $_GET['dir'] == '.') 
		{ 
			header('location: index.php'); 
		}
		self::fetchFiles();
		
		$this->template->dirsArray  = $this->dirsArray;		
		$this->template->filesArray =  $this->filesArray;
		$this->template->uriDir     = empty($_GET['dir']) ? '' : $_GET['dir'];
		$this->template->prevdir    = !empty($_GET['dir']) ? dirname($_GET['dir']) : '';
		$this->template->errors     = $this->errors;
						
		$this->template->render(['header', 'uploadr', 'footer']);
	}


	
	public function notFound()
	{
		$this->template->render(['header', '404', 'footer']);
	}

	

	/*
		Check for MAX POST SIZE, max file size combined
	*/
	private function validateForm()
	{
		if (isset($_POST['submit']))
		{
			if (empty($_FILES['files']['name'][0]))
			{
				$errors['m_field'] = "Please Select A File";
			}
			else
			{
				$dir = self::sanitizeGetDir();
				$count = count($_FILES['files']['name']);
					
				for ($i = 0; $i < $count; $i++)
				{
					$file_parts = pathinfo($_FILES['files']['name'][$i]);					
							
					if (file_exists($dir.$_FILES['files']['name'][$i]))
					{
						$errors['f_exists'.$i] = "The File: ".$file_parts['basename']. " Exists In Directory";
					}
					if (empty($file_parts['extension']) || !in_array(strtolower($file_parts['extension']), $this->registry->config['file_extension_whitelist']))
					{
						$errors['f_ext'.$i] = "Extension Not Supported on: ".$file_parts['basename'];
					}
					if ($_FILES['files']['size'][$i] > $this->registry->config['max_file_size'])
					{
						$errors['f_size'.$i] = "Max File Size Exceeded on: ".$file_parts['basename'];
					}
				}
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


	
	private function upload()
	{
		$dir = self::sanitizeGetDir();
		$count = count($_FILES['files']['name']);
		$returnFlag = false;
		$i = 0;
		
		
		do
		{
			$uploadfile = $dir.basename(/*preg_replace("/[^a-zA-Z0-9s.]/", " ",*/ $_FILES['files']['name'][$i]);
							
			if (!move_uploaded_file($_FILES['files']['tmp_name'][$i], $uploadfile))
			{
				return false;
			}
			else
			{
				$returnFlag = true;
				$i++;
			}
		}

		while ($i < $count && $returnFlag != false);

		unset($_FILES);

		return true;
		
	}



				
	private function fetchFiles()
	{
		$dir = self::sanitizeGetDir();	
	
		if (is_dir($dir))
		{
			foreach (glob($dir.'*') as $file)
			{
				if (is_dir($file))
				{
					$name = str_replace(__UPLOAD_DIR, '', $file); 
					$displayName = basename($name);
					$displayName = self::appendFileName($name, 60);
					$this->dirsArray[$name] = [ 'name' => htmlentities($name), 'displayName' => $displayName ];
				}
				else
				{
					$name = basename($file);
					$size = self::bytesToSize(filesize($file));
					$displayName = self::appendFileName($name, 25);
					$this->filesArray[$name] = [ 'name' => htmlentities($name), 'displayName' => $displayName, 'size' => $size, 'mod' => filemtime($file) ];
				}
														
			}
	
			if (empty($this->filesArray) || empty($this->dirsArray))
			{
				$this->msg['FETCH'] = "No Files In Directory";
			}
		}
		else
		{
			$errors['DIREXIST'] = "Non-Existant Directory";
		}
		if (!empty($errors))
		{
			$this->errors = $errors;
		}
	}



	
        public function download()
	{
		if (!empty($_GET['file']))
		{
			$dir = self::sanitizeGetDir();

			if (is_file($dir.basename($_GET['file'])))
			{
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.basename(urldecode($_GET['file'])).'"');
				header('Content-Transfer-Encoding: binary');
				header('Connection: Keep-Alive');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				
				readfile($dir.basename(urldecode($_GET['file'])));
			}
			else
			{
				$errors['exist'] = "File Does Not Exist";
			}
		}
		else
		{
			self::index();
		}
	}


	
	public function delete()
	{
		if (!empty($_GET['file']))
		{
			$dir = self::sanitizeGetDir();
			
			if (is_file($dir.basename($_GET['file'])))
			{
				try
				{
					unlink($dir.basename($_GET['file']));
				}
				catch (Exception $e)
				{
					$errors['RMFAIL'] = "Unable To Delete File";
					//$log->$e->getMessage();
				}
			}
			else
			{
				$errors['EXISTS'] = "Unable To Delete File Because File Does Not Exist";
			}

			if (!empty($errors))
			{
				$this->errors = $errors;
			}
		}
		
		self::index();
	}

	
	
	public function createDir()
	{
		$dir = self::sanitizeGetDir();
		
		if (self::validateCreateDir($dir))
		{	
			try
			{
				$file = $_POST['dirname'];
				mkdir($dir.basename($file), 0700);			
			}
			catch (Exception $e)
			{	
				$error['mkdir'] = 1;
				//$log->$e->getMessage();
			}
		}
		
		self::index();
			
	}



	private function validateCreateDir($dir = null)
	{
		if (!empty($_POST['dirname']))
		{
			if (preg_match('/^[\w-]+$/', $_POST['dirname']))
			{
				if (is_dir($dir.basename($_POST['dirname'])))
				{
					$errors['exists'] = "Directory Exists Already";
				}
			}
			else
			{
				$errors['MATCH'] = "Only Characters: a-z, A-Z, 0-9, _ and - Allowed";
			}
		}	
		else
		{
			$errors['empty'] = "Missing Field";
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

	
		
	private function appendFileName($filename, $strlen = 30)
	{
		if (is_string($filename))
		{
			if (strlen($filename) > $strlen)
			{
				$string = substr($filename, 0, $strlen);
				$appendedFileName = $string.'...';
				return $appendedFileName;
			}
			else
			{
				return $filename;
			}
		}
		else
		{
			return false;
		}
	}
				


	
	private function sanitizeGetDir()
	{
		$dir = empty($_GET['dir']) ? __UPLOAD_DIR : __UPLOAD_DIR.urldecode($_GET['dir']);
		$dir = !strstr(substr($dir, -1) , '/') ? $dir.'/' : $dir;
		$dir = str_replace('../', '', $dir);
				
		return $dir;
	}


	
	private function bytesToSize($bytes, $precision = 2)
	{
		if ($bytes > 0)
		{ 
    			$unit = array('B','KB','MB','GB','TB','PB','EB');

    			return round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), $precision).' '.$unit[$i];
		}
		else
		{
			return $bytes;
		}
	}


	
}





?>
