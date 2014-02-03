		<section id="fileform">
			<div class="inner">
				<div class="non-semantic-protector">
				<h1 class="ribbon">
					<strong class="ribbon-content">~ Uploadr ~</strong>
				</h1>
				</div>
				<div class="innerchild">
				<div class="uploadform">
				<form action="index.php?route=index/upload&amp;dir=<?php echo $uriDir; ?>" method="POST" enctype="multipart/form-data" id="uploadfile">
				<fieldset>
					<legend></legend>					
					<div id="selectfile">
						<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $maxsize; ?>" id="maxsize" />
						<input type="button" name="filebutton" value="Select File" id="filebutton" onclick="selectFile()"/>
						<input type="file" id="file" name="files[]" onchange="alertFile()" multiple />
						<input type="hidden" name="csrf" value="<?php echo $csrf; ?>" />
					</div>
					<div id="submitfile">
						<input type="submit" value="Upload File" name="submit" id="upload" />
					</div>
				</fieldset>
				</form>
				</div>
				<div class="createdir">
				<form action="index.php?route=index/createDir&amp;dir=<?php echo $uriDir ?>" method="POST" id="createdir">
				<fieldset>
					<legend></legend>
					<div id="typedir">
						<input type="text" name="dirname" />
					</div>
					<div id="seccreatedir">
						<input type="hidden" value="<?php echo $csrf; ?>" name="csrf" />
						<input type="submit" value="Create Folder" name="createdir" />
					</div>
				</fieldset>
				</form>
				</div>
				<div class="msg-container">
				<?php
					if (!empty($errors))
					{
						foreach($errors as $error)
						{
							echo '<p class="error">'.$error.'</p>';
						}
					}
				?>
				</div>
				<div type="hidden" id="currentdir" value="<?php echo  'index.php?dir='.$uriDir; ?>" ></div>
				<div class="parentdir"><?php echo  (!empty($prevdir) ? '<a href="index.php?dir='.$prevdir.'">Parent Directory</a>' : ''); ?></div> 
				<div class="files"><?php
								if (!empty($dirsArray))
								{
									foreach ($dirsArray as $dirArray)
									{
										echo '<ul id="dirs"><li><a href="index.php?dir='.$dirArray['name'].'">'.$dirArray['displayName'].'</a></li>'
										     .'<li>&nbsp;</li><li><a>&nbsp;</a></li>'
										     .'<li><form class="deleteform" action="index.php?route=index/delete" method="POST">'
											 .'<input type="hidden" name="dir" value="'.$uriDir.'" />'
											 .'<input type="hidden" name="file" value="'.$dirArray['name'].'" />'
					                         .'<input type="hidden" name="csrf" value="'.$csrf.'" />'
					                         .'<input type="hidden" name="type" value="dir" />'
					                         .'<input type="submit" name="delete" value="X" onclick="promptRm()"/>'
				                             .'</form></li></ul>';
									}
								}
								if (!empty($filesArray))
								{
									foreach($filesArray as $fileArray)
									{
										echo '<ul><li title="'.$fileArray['name'].'">'.$fileArray['displayName'].'</li><li>'.$fileArray['size'].'</li>'
										 	 .'<li><a href="index.php?route=index/download&amp;dir='.$uriDir.'&amp;file='.$fileArray['uriname'].'">D</a></li>'
										 	 .'<li><form class="deleteform" action="index.php?route=index/delete" method="POST">'
											 .'<input type="hidden" name="dir" value="'.$uriDir.'" />'
											 .'<input type="hidden" name="file" value="'.$fileArray['uriname'].'" />'
											 .'<input type="hidden" name="csrf" value="'.$csrf.'" />'
											 .'<input type="hidden" name="type" value="file" />'
											 .'<input type="submit" name="delete" value="X" />'
										 	 .'</form><li></ul>';
																	
									}
								}
							?>
				</div>
				</div>
			</div>
		</section>
