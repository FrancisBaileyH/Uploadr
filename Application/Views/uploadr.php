		<section id="fileform">
			<div class="inner">
				<div class="non-semantic-protector">
				<h1 class="ribbon">
					<strong class="ribbon-content">~ Uploadr ~</strong>
				</h1>
				</div>
				<div class="innerchild">
				<div class="form">
				<form action="index.php?dir=<?= $uriDir; ?>" method="POST" enctype="multipart/form-data" id="uploadfile">
				<fieldset>
					<legend></legend>					
					<div id="selectfile">
						<input type="button" onclick="selectFile()" name="filebutton" value="Select File" /><input onchange="alertFile()" id="file" type="file" name="files[]" multiple />
					</div>
					<div id="submitfile">
						<input type="submit" value="Upload File" name="submit" />
					</div>
				</fieldset>
				</form>
				</div>
				<div class="createdir">
				<form action="index.php?route=index/createDir&amp;dir=<?= $uriDir; ?>" method="POST" id="createdir">
				<fieldset>
					<legend></legend>
					<div id="typedir">
						<input type="text" name="dirname" />
					</div>
					<div id="seccreatedir">
						<input type="submit" value="Create Folder" name="createdir" />
					</div>
				</fieldset>
				</form>
				</div>
				<div class="msg-container" id="uploads"></div>
				<div class="msg-container">
				<?php
					if (!empty($errors))
					{
						foreach($errors as $error)
						{
							echo '<p class="error">'.$error.'</p>';
						}
						//print_r($errors);
					}
				?>
				</div>
				<div class="parentdir"><?= (!empty($prevdir) ? '<a href="index.php?dir='.$prevdir.'">Parent Directory</a>' : '&nbsp;'); ?></div>
				<div class="files"><?php
								if (!empty($dirsArray))
								{
									foreach ($dirsArray as $dirArray)
									{
										echo '<ul><li><a href="index.php?dir='.$dirArray['name'].'">'.$dirArray['displayName'].'</a></li><li></li><li>&nbsp;</li><li><a href="index.php?route=index/deleteDir&amp;dir='.$dirArray['name'].'"></a></li></ul>';
									}
								}
								if (!empty($filesArray))
								{
									foreach($filesArray as $fileArray)
									{
										echo    '<ul><li title="'.$fileArray['name'].'">'.$fileArray['displayName'].'</li><li>'.$fileArray['size'].'</li>'.
											'<li><a href="index.php?route=index/download&amp;dir='.$uriDir.'&amp;file='.urlencode($fileArray['name']).'">D</a></li>'.
											'<li><a href="index.php?route=index/delete&amp;dir='.$uriDir.'&amp;file='.urlencode($fileArray['name']).'">X</a><li></ul>';
																	
									}
								}
							?>
				</div>
				</div>
			</div>
		</section>
