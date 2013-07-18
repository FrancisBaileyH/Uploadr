		<section id="fileform">
			<div class="inner">
				<div class="non-semantic-protector">
				<h1 class="ribbon">
					<strong class="ribbon-content">~ Uploadr ~</strong>
				</h1>
				</div>
				<div class="innerchild">
				<div class="form">
				<form action="index.php?dir=<?= $dir; ?>" method="POST" enctype="multipart/form-data" id="uploadfile">
				<fieldset>
					<legend></legend>					
					<div id="selectfile">
						<input type="button" onclick="getFile()" name="filebutton" value="Select File" /><input onchange="alertFile()" id="file" type="file" name="files[]" multiple />
					</div>
					<div id="submitfile">
						<input type="submit" value="Upload File" name="submit" />
					</div>
				</fieldset>
				</form>
				</div>
				<div class="createdir">
				<form action="index.php?route=index/createDir&amp;dir=<?= $dir; ?>" method="POST" id="createdir">
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
				<div class="msg-containter" id="uploads"></div>
				<div class="msg-container">
				<?php
					if (!empty($errors))
					{
						/*foreach($errors as $error)
						{
							echo '<p class="error">'.$error.'</p>';
						}*/
						print_r($errors);
					}
				?>
				</div>
				<div class="parentdir"><?= (!empty($prevdir) ? '<a href="index.php?dir='.$prevdir.'">Parent Directory</a>' : '&nbsp;'); ?></div>
				<div class="files"><?php
								if (!empty($files))
								{
								
									foreach($files as $file)
									{
										if ($file['type'] == 'dir')
										{
											echo '<ul><li><a href="index.php?dir='.$file['name'].'">'.basename($file['name']).'</a></li><li></li><li>&nbsp;</li><li><a href="index.php?route=index/deleteDir&amp;dir='.$file['name'].'"></a></li></ul>';
										}
										else
										{
											echo    '<ul><li>'.$file['displayname'].'</li><li>'.$file['size'].'</li>'.
												'<li><a href="index.php?route=index/download&amp;dir='.$dir.'&amp;file='.urlencode($file['name']).'">D</a></li>'.
												'<li><a href="index.php?route=index/delete&amp;dir='.$dir.'&amp;file='.urlencode($file['name']).'">X</a><li></ul>';
										}							
									}
								}
							?>
				</div>
				</div>
			</div>
		</section>
