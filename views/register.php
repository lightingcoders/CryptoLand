<?php 
/**
 * Google Maps Draw Module
 * @package drawonmaps
 * 
 * File: maps.php
 * Description: List of maps page
 */

?>

	<div class="container">
		<h3 class="doc_title"><?php echo $vars['page_title']; ?></h3>
		<!-- Main component for a primary marketing message or call to action -->
		<div class="jumbotron">
			
			<div id="login-form" class="dom_login">
				<p class="text-danger"><?php echo $lang_msg['REGISTER_REQUIRED_FIELDS']; ?></p>
				<form method="post" data-toggle="validator">
				  <div class="form-group">
					<label for="exampleInputEmail1"><?php echo $lang_msg['REGISTER_USERNAME_NOTE']; ?></label>
					<input type="text" name="uname" class="form-control" id="register_username" placeholder="<?php echo $lang_msg['REGISTER_USERNAME']; ?>" required>
				  </div>
				  <div class="form-group">
					<label for="exampleInputPassword1"><?php echo $lang_msg['REGISTER_EMAIL_NOTE']; ?></label>
					<input type="email" name="email" class="form-control" id="register_email" placeholder="<?php echo $lang_msg['REGISTER_EMAIL']; ?>" required>
				  </div>
				  <div class="form-group">
					<label for="exampleInputPassword1"><?php echo $lang_msg['REGISTER_PASSWORD_NOTE']; ?></label>
					<input type="password" name="pass" class="form-control" id="register_passwd" placeholder="<?php echo $lang_msg['REGISTER_PASSWORD']; ?>" required>
				  </div>
				  <button type="submit" name="btn-signup" class="btn btn-default "><?php echo $lang_msg['REGISTER_SIGNUP']; ?></button>
				</form>
				<a href="index.php?p=login" class="signup"><?php echo $lang_msg['REGISTER_SIGNIN']; ?></a>
			</div>
		</div>
	</div> <!-- /container -->

