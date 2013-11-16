<?php




namespace Application\Controllers;




use Lib;




class IndexController extends BaseController
{



	private $FileHandler = NULL;
	private $dirsArray  = array();
	private $filesArray = array();
	private $validFiles = array();
	private $dir;
	



	public function __construct($registry, $request)
	{
		parent::__construct($registry, $request);
			
		$this->FileHandler = new Lib\FileHandler();
		$this->dir = $this->sanitizeDirname();
	}


		
	public function index()
	{
		$this->fetchFiles();
		$this->template->maxsize    = $this->registry->config['max_file_size'];
		$this->template->dirsArray  = $this->sortDirsArray($this->FileHandler->getDirsArray());
		$this->template->filesArray = $this->sortFilesArray($this->FileHandler->getFilesArray());
		$this->template->errors     = $this->ErrorHandler->getAllErrors();
		$this->template->uriDir     = empty($this->request['GET']['dir']) ? '' : $this->request['GET']['dir'];
		$this->template->prevdir    = !empty($this->request['GET']['dir']) ? dirname($this->request['GET']['dir']) : '';
		$this->template->render(['header', 'uploadr', 'footer']);
	}


	
	public function notFound()
	{
		$this->template->render(['header', '404', 'footer']);
	}
	
	
	
	public function download()
        {
		if (!empty($this->request['GET']['file']))
		{
			$file = $this->dir.urldecode($this->request['GET']['file']);
			
			if ($this->FileHandler->checkFile($file))
			{
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.basename($file).'"');
				header('Content-Transfer-Encoding: binary');
				header('Connection: Keep-Alive');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: '.filesize($file));			       

				readfile($file);
			}
			else
			{
				$this->ErrorHandler->setErrors('Unable To Download: Non-Existant File');
				$this->index();
			}
		}
		
	}
	
	
	
	private function validateCreateDir($dirname)
	{
		$flag = true; 
		
		if (!empty($dirname))
		{
			$dirname = basename($dirname);
			
			if (preg_match('/^[\w-]+$/', $dirname))
			{
				if ($this->FileHandler->checkDir($this->dir.$dirname))
				{
					$this->ErrorHandler->setErrors('Directory Exists Already');
					$flag = false;
				}
				if (strlen($dirname) > 60 )
				{
					$this->ErrorHandler->setErrors('Maximum 60 Characters Allowed');
					$flag = false;
				}
			}
			else
			{
				$this->ErrorHandler->setErrors('Only Characters: a-z, A-Z, 0-9, _ and - Allowed');
				$flag = false;
			}
		}	
		else
		{
			$this->ErrorHandler->setErrors('Missing Field');
			$flag = false;
		}

		return $flag; 
		
	}
	
	
	
	public function createDir()
	{
		if ($this->validateCreateDir($this->request['POST']['dirname']))
		{
			if (!$this->FileHandler->createDir($this->dir, basename($this->request['POST']['dirname']), 0700))
			{
				$this->ErrorHandler->setErrors('Unable To Create Directory At This Time');
			}
			
		}
		
		$this->index();
	}


	
	public function deleteDir()
	{
		if ($this->FileHandler->checkDir($this->dir.$this->request['GET']['name']))
		{
			$this->FileHandler->rmDirRecursive($this->dir.$this->request['GET']['name']);
			
			if ($this->FileHandler->checkDir($this->dir.$this->request['GET']['name']))
			{
				$this->ErrorHandler->setErrors('Unable To Remove Directory At This Time');
			}
		}
		else
		{
			$this->ErrorHandler->setErrors('Unable To Remove Directory: Non-Existant Directory');
		}
		
		$this->index();
	}
	
	
	
	public function delete()
	{
		if ($this->FileHandler->checkFile($this->dir.$this->request['GET']['file']))
		{
			if (!$this->FileHandler->rmFile($this->dir.urldecode($this->request['GET']['file'])))
			{
				$this->ErrorHandler->setErrors('Unable To Remove File At This Time');
			}
		}
		else
		{
			$this->ErrorHandler->setErrors('Unable To Remove: Non-Existant File');
		}
		
		$this->index();
	}
	
	
	
	private function fetchFiles()
	{
		if ($this->FileHandler->checkDir($this->dir))
		{
			if (!$this->FileHandler->fetchFiles($this->dir, '*'))
			{
				$this->ErrorHandler->setErrors('No Files In Directory');
			}
		}
		else
		{
			$this->ErrorHandler->setErrors('Non-Existant Directory');
		}
	}
	
	
	
	private function sortDirsArray($dirsArray = array())
	{
		for ($i = 0; $i < count($dirsArray); $i++)
		{
			$name = htmlentities(str_replace(__UPLOAD_DIR, '', $dirsArray[$i]));
			$displayName = basename($name);
			$displayName = $this->appendString($displayName, 40);
			$deleteName = basename($name);
			
			$this->dirsArray[] = [ 'name' => $name, 'displayName' => $displayName, 'deleteName' => $deleteName ];
		}
		
		return $this->dirsArray;
	}
	
	
	
	private function sortFilesArray($filesArray = array())
	{
		for ($i = 0; $i < count($filesArray); $i++)
		{
			$name = htmlentities(basename($filesArray[$i]));
			$uriname = urlencode(basename($filesArray[$i]));
			$displayName = $this->appendString($name, 25);
			$size = $this->FileHandler->bytesToSize(filesize($filesArray[$i]));
			
			$this->filesArray[] = [ 'name' => $name, 'displayName' => $displayName, 'uriname' => $uriname,'size' => $size ];
		}
		
		return $this->filesArray;
	}
	
	
			
	
	private function appendString($string, $strlen = 30)
	{
		if (is_string($string))
		{
			if (strlen($string) > $strlen)
			{
				$string = substr($string, 0, $strlen);
				return $string.'...';
			}
			else
			{
				return $string;
			}
		}
	}
	
	
	
	
	private function sanitizeDirname()
	{
		$dir = empty($this->request['GET']['dir']) ? __UPLOAD_DIR : __UPLOAD_DIR.urldecode($this->request['GET']['dir']);
		$dir = !strstr(substr($dir, -1) , '/') ? $dir.'/' : $dir;
		$dir = str_replace('../', '', $dir);
				
		return $dir;
	}
			
			
	
	public function upload()
	{
			
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')
		{
			$this->ajaxUpload();
		}	
		else
		{
			$this->validateUpload($this->request, $this->dir);
		
			if (!empty($this->validFiles[0]))
			{
				$i = 0;
						
				do
				{
					
					if (!$this->FileHandler->uploadFile($this->validFiles[$i]['tmp_name'], $this->dir.$this->validFiles[$i]['name']))
					{
						$this->ErrorHandler->setErrors('Error: Unable to Upload '.$this->validFiles[$i]['name']);
					}	
								
					$i++;
				
				}
				while ($i < count($this->validFiles));
				
			}
		
			$this->index();
		
		}
		
	}
	
	
	
	
	private function ajaxUpload()
	{
		
		if (!empty($_FILES[0]))
		{
			
			if ($this->FileHandler->checkFile($this->dir.$_FILES[0]['name']))
			{
				$response[] = "File Exists In Directory";
			}
			if ($_FILES[0]['size'] > $this->registry->config['max_file_size'])
			{
				$response[] = "File Exceeds Maximum Upload Size";
			}
			if (empty($response))
			{
				if (!$this->FileHandler->uploadFile($_FILES[0]['tmp_name'], $this->dir.$_FILES[0]['name']))
				{
					$response[] = "Unable to Upload File At This Time";
				}
				else
				{
					$response[] = 1;
				}
			}
			
		}
		else
		{
			$response[] = "Please Select A File To Upload";
		}
					
		header('content-type: application/json');
		echo json_encode($response);
	}
	
	


	private function validateUpload($request, $dir)
	{
		if (!empty($request['FILES']['files']['name'][0]))
		{
			$files = $request['FILES']['files'];
			
			for ($i = 0; $i < count($files['name']); $i++)
			{
				$flag = true;
				
				if ($files['size'][$i] > $this->registry->config['max_file_size'])
				{
					$this->ErrorHandler->setErrors("Max File Size Exceeded On: ".$files['name'][$i]);
					$flag = false;
				}
				if ($this->FileHandler->checkFile($this->dir.$files['name']))
				{
					$this->ErrorHandler->setErrors("File Exists in Directory");
					$flag = false;
				}
				
				if ($flag == true)
				{
					$this->validFiles[] = [ 'tmp_name' => $files['tmp_name'][$i], 'name' => $files['name'][$i] ];
				}
			}
		}
		else
		{
			$this->ErrorHandler->setErrors("Please Select a File To Upload");
		}	
	
	}
	
	
		
	
}





?>
