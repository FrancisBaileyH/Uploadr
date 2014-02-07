<?php



namespace Lib;




class FileHandler {



	/*
	 * Hold The File and Dir Names
	*/ 
	private $filesArray = array();
	private $dirsArray = array();
		

	
	public function setDirsArray($dirs)
	{
		$this->dirsArray[] = $dirs;
	}



	public function setFilesArray($files)
	{
		$this->filesArray[] = $files;
	}


	
	public function getDirsArray()
	{
		return $this->dirsArray;
	}



	public function getFilesArray()
	{
		return $this->filesArray;
	}
	
	
	
	/*
	 * Fetches files via glob
	 * 
	 * @dir: directory to fetch files
	 * @target: pattern to search for
	 * 		(*, .txt, hello*, etc)
	 *
	 * @return: returns array of files and dirs
	*/  
	public function globFiles($dir, $target)
	{
		$files = false;
					
		foreach (glob($dir.$target) as $file)
		{
			$files[] = $file;
		}
					
		return $files;
	}
	
	
	
	
	public function rmFile($file)
	{
		unlink($file);
		
		return !$this->checkFile($file);
	}
	
	
	
	public function rmDirectory($dir)
	{
		return rmdir($dir);
	}
	
	

	public function uploadFile($tmpPath, $targetPathFileName)
	{
		return move_uploaded_file($tmpPath, $targetPathFileName);
	}
	
	
	
	public function createDir($targetPath, $dirname, $perms = 0600)
	{
		return mkdir($targetPath.$dirname, $perms);
	}




	/*
	 * 
	 * Used to sort array
	 * returned by globFiles
	 * 
	 * sets filesArray or dirsArray
	 * for later use
	*/ 
	public function sortContents($contents = array())
	{
					
		foreach ($contents as $content)
		{
			if ($this->checkDir($content))
			{
				$this->setDirsArray($content);
			}
			elseif ($this->checkFile($content))
			{
				$this->setFilesArray($content);
			}
								
		}
	}
	
	
	
		
	public function checkDir($dir)
	{
		return is_dir($dir);
	}
	
	
	
	public function checkFile($file)
	{
		return is_file($file);
	}
	
	
	
	
	/*
	 * Converts Bytes to Readable format
	 * 
	 * @return: returns string
	*/
	public function bytesToSize($bytes, $precision = 2)
	{
		$unit = [ 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB' ];

		if ( $bytes > 0 )
		{
			return round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), $precision). ' '.$unit[$i];
		}
		
		return $bytes . 'B';
				
	}
	
	
	
	
	/*
	 *  Credit to Laravel for rmDir function
	 * 
	 * Checks if dir is empty
	 * if yes removes file and repeats 
	 * recursively
	*/
	public function rmDirRecursive($path, $preserve = false)
	{
		$contents = new \FilesystemIterator($path);
		
		foreach ($contents as $content)
		{
			if ($this->checkDir($content))
			{
				$this->rmDirRecursive($content->getPathname());
			}
			else
			{
				$this->rmFile($content->getPathname());
			}
		}
		
		if ( !$preserve ) @$this->rmDirectory($path);
		
	}

	
	
	
	/*
	 * Combines use of sortFiles() and
	 * globFiles()
	 * 
	 * @return: bool
	*/ 
	public function fetchFiles($dir, $target)
	{
		$files = $this->globFiles($dir, $target);
				
		if (is_array($files))
		{
			$this->sortContents($files);
			return true;
		}
		
		return false;
		
	}
	
	
}



