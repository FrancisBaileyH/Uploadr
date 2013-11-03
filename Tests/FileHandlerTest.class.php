<?php





class FileHandlingTest extends PHPUnit_Framework_TestCase 
{
	
	
	
	public function TestRmDir()
	{
		mkdir('/home/kattenmedia/Uploadr/Uploads/Test', 0700);
		mkdir('/home/kattenmedia/Uploadr/Uploads/Test/Test2', 0700);
		file_put_contents('/home/kattenmedia/Uploadr/Uploads/Test/Test2/test.txt', 'Hello World');
		
		$fileHander = new FileHandler();
		
		$fileHanlder->rmDir('/home/kattenmedia/Uploadr/Uploads/Test');
		$this->assertFalse(is_dir('/home/kattenmedia/Uploadr/Uploads/Test'));
		$this->assertFalse(is_file('/home/kattenmedia/Uploadr/Uploads/Test'));
	}
	
	
}



