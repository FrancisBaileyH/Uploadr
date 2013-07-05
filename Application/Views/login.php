		<section id="fileform">
			<div class="inner">
				<div class="non-semantic-protector">
				<h1 class="ribbon">
					<strong class="ribbon-content">~ Uploadr ~</strong>
				</h1>
				</div>
				<div class="innerchild">
					<form class="login" action="index.php?route=Auth/auth" method="POST">
					<fieldset>
						<legend></legend>
						<?php
			
							if (!empty($errors))
							{
								foreach($errors as $error)
								{
									echo '<p class="error">'.$error.'</p>';	
								}
							}
						
						?>
						<div>	
							<label>Username</label><input type="text" name="username" />
						</div>
						<div>
							<label>Password</label><input type="password" name="password" />
						</div>
						<div>
							<input type="submit" name="submit" value="login" />
						</div>
					</fieldset>
					</form>
				</div>
			</div>
		</section>
