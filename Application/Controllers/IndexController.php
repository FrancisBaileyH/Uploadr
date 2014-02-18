<?php


namespace Application\Controllers;


use Lib;


class IndexController extends BaseController
{
	private $FileHandler = NULL;
	private $dirsArray  = array();
	private $filesArray = array();
	private $validFiles = array();
	private $csrfValid = true;
	private $dir;
		

	public function __construct($registry)
	{
		parent::__construct($registry);
			
		$this->FileHandler = new Lib\FileHandler();
		$this->dir = $this->sanitizeDirname( empty( $_GET['dir'] ) ? '' : $_GET['dir'] );
		$this->config['max_file_size'] = $this->FileHandler->returnBytes( $this->config['max_file_size'] );
	}

	
	public function index()
	{
		$this->fetchFiles();
		$this->template->maxnum     = $this->config['max_num_files'];
		$this->template->maxsize    = $this->config['max_file_size'];
		$this->template->dirsArray  = $this->sortDirsArray($this->FileHandler->getDirsArray());
		$this->template->filesArray = $this->sortFilesArray($this->FileHandler->getFilesArray());
		$this->template->errors     = $this->ErrorHandler->getAllErrors();
		$this->template->uriDir     = empty($_GET['dir']) ? '' : $_GET['dir'];
		$this->template->prevdir    = !empty($_GET['dir']) ? dirname($_GET['dir']) : '';
		$this->template->csrf       = $this->CSRFProtect->getToken();
		$this->template->render(['header', 'uploadr', 'footer']);
	}

	
	public function notFound()
	{
		$this->template->render(['header', '404', 'footer']);
	}
	
	
	private function checkCSRF( $token )
	{
		return $this->CSRFProtect->isTokenValid( $token );
	}
	
		
	public function download()
        {
		if (!empty($_GET['file']))
		{
			$file = $this->dir.urldecode($_GET['file']);
			
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
	
	
	/*
	 * Validate the create directory form
	 * return false if any criteria is not met
	*/ 
	private function validateCreateDir($input)
	{
		$flag = true; 
		
		if ( empty( $input['csrf'] ) || !$this->checkCSRF( $input['csrf'] ) )
		{
			$flag = false;
			$this->ErrorHandler->setErrors( "Invalid CSRF token, refresh the page and try again" );
		}
		else if (!empty($input['dirname']))
		{
			$dirname = $input['dirname'];
			
			if (preg_match('/^[\w-]+$/', $dirname))
			{
				if ($this->FileHandler->checkDir($this->dir.$dirname))
				{
					$this->ErrorHandler->setErrors('Directory Exists Already');
					$flag = false;
				}
				if (strlen($dirname) > 60)
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
	
	
	/*
	 * Create directory function
	*/ 
	public function createDir()
	{
		if ($this->validateCreateDir($_POST))
		{
			if (!$this->FileHandler->createDir($this->dir, basename($_POST['dirname']), 0700))
			{
				$this->ErrorHandler->setErrors('Unable To Create Directory At This Time');
			}
			
		}
		
		$this->index();
	}

	
	/*
	 * Delete entire directory including any
	 * sub directories and files within it
	*/ 
	private function deleteDir( $file )
	{
		if ($this->FileHandler->checkDir($this->dir.$file))
		{
			$this->FileHandler->rmDirRecursive($this->dir.$file);
			
			if ($this->FileHandler->checkDir($this->dir.$file))
			{
				$this->ErrorHandler->setErrors('Unable To Remove Directory At This Time');
			}
		}
		else
		{
			$this->ErrorHandler->setErrors('Unable To Remove Directory: Non-Existant Directory');
		}

	}
	
	
	/*
	 * Checks if delete type is for file or directory
	 * If it's a file it's handled directly in the function
	 * else it calls deleteDir()
	*/
	public function delete()
	{
		if ( empty( $_POST['csrf'] ) || !$this->checkCSRF( $_POST['csrf'] ) )
		{
			$this->ErrorHandler->setErrors( "Invalid CSRF token, refresh the page and try again" );
		}
		else
		{
			if (!empty($_POST['type']))
			{
				$file = empty($_POST['file']) ? '' : urldecode( $_POST['file'] ); 
				$file = basename( $file );
							
				if ($_POST['type'] == 'file')
				{
					if ($this->FileHandler->checkFile($this->dir.$file))
					{
						if (!$this->FileHandler->rmFile($this->dir.$file))
						{
							$this->ErrorHandler->setErrors('Unable To Remove File At This Time');
						}
					}
					else
					{
						$this->ErrorHandler->setErrors('Unable To Remove: Non-Existant File');
					}
		
				}
				else
				{
					$this->deleteDir( $file );
				}
			}
		}
		
		$this->index();
	}
		
	
	/*
	 * Fetches all files from specified directory
	 * files are stored in array in FileHandler object
	*/ 
	private function fetchFiles()
	{
		if ($this->FileHandler->checkDir($this->dir))
		{
			# Fetch Normal Files
			# Fetch Hidden Files
			# Check Results
			$resNF = $this->FileHandler->fetchFiles($this->dir, '*');
            $resHF = $this->FileHandler->fetchFiles($this->dir, '.[A-Za-z0-9]*' );


			if (!$resNF && !$resHF)
			{
				$this->ErrorHandler->setErrors('No Files In Directory');
			}
		}
		else
		{
			$this->ErrorHandler->setErrors('Non-Existant Directory');
		}
	}
	
	
	/*
	 * Fetches directories from FileHandler object 
	 * and prepares them for display to client
	*/ 
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
		
	/*
	 * Fetches files from FileHandler object
	 * and prepares them for display to client
	*/ 
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
				
	
	/*
	 * If file name is too long, shorten it to display nicely
	*/ 
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
	
	
	
	private function sanitizeFilename( $file )
	{
		$file = str_replace( '../', '', $file );
	}
	
	
	
	/*
	 * Cleans up directory name from $_GET
	 * Ensures directory traversal cannot occur
	*/ 	
	private function sanitizeDirname( $dir = null )
	{
		if ( empty( $dir ) )
		{
			$cleanDir = __UPLOAD_DIR;
		}
		else
		{
			$cleanDir = str_replace('../', '', $dir);
			$cleanDir = trim( $cleanDir, '.' );
			$cleanDir = __UPLOAD_DIR.urldecode( $cleanDir );
		    $cleanDir = !strstr(substr($cleanDir, -1) , '/') ? $cleanDir.'/' : $cleanDir;
		}
				
		return $cleanDir;
	}
			
	
	/*
	 * Checks if ajax request and calls appropriate function
	 * else deals with files directly
	*/ 	
	public function upload()
	{
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')
		{
			$this->ajaxUpload();
		}	
		else
		{
	  	   $this->validateUpload($_FILES, $_POST, $this->dir);
		
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
				while ($i < count($this->validFiles) && $i < $this->registry['config']['max_file_uploads']);
			}
			$this->index();
		}
	}
		
	
	private function ajaxUpload()
	{
		if ( empty( $_POST[0]) || !$this->checkCSRF( $_POST[0] ) )
		{
			$response[] = "Invalid CSRF token, refresh the page and try again";
		}
		else if (!empty($_FILES[0]))
		{
			$_FILES[0]['name'] = basename( $_FILES[0]['name'] );
			
			if ($this->FileHandler->checkFile($this->dir.$_FILES[0]['name']))
			{
				$response[] = "File Exists In Directory";
			}	
			if ($_FILES[0]['size'] > $this->config['max_file_size'])
			{
				$response[] = "File Exceeds Maximum Upload Size of " . $this->FileHandler->bytesToSize( $this->config['max_file_size'] );
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
	
	


	private function validateUpload($request, $post, $dir)
	{
		if (empty( $post['csrf'] ) || !$this->checkCSRF( $post['csrf'] ))
		{
			$this->ErrorHandler->setErrors( "Invalid CSRF token, refresh the page and try again" );
		}
		else if (!empty($request['FILES']['files']['name'][0]))
		{
			$files = $request['FILES']['files'];
			
			for ($i = 0; $i < count($files['name']); $i++)
			{
				$flag = true;
				$files['name'][$i] = basename( $files['name'][$i] );
				
				if ($files['size'][$i] > $this->config['max_file_size'])
				{
					$this->ErrorHandler->setErrors("Max File Size Exceeded of ".$this->FileHandler->bytesToSize( $this->config['max_file_size'] ). " On: ".$files['name'][$i]);
					$flag = false;
				}
				if ($this->FileHandler->checkFile($dir.$files['name'][$i]))
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
