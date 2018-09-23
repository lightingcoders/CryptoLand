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
				<form method="post">
				  <div class="form-group">
					<label for="exampleInputEmail1"><?php echo $lang_msg['LOGIN_EMAIL_ADDRESS']; ?></label>
					<input type="email" name="email" class="form-control" id="login_email" placeholder="<?php echo $lang_msg['LOGIN_EMAIL']; ?>">
				  </div>
				  <div class="form-group">
					<label for="exampleInputPassword1"><?php echo $lang_msg['LOGIN_PASSWORD']; ?></label>
					<input type="password" name="pass" class="form-control" id="login_pass" placeholder="<?php echo $lang_msg['LOGIN_PASSWORD']; ?>">
				  </div>
				  <button type="submit" name="btn-login" class="btn btn-default "><?php echo $lang_msg['LOGIN_SIGNIN']; ?></button>
				</form>
				<a href="index.php?p=register" class="signup"><?php echo $lang_msg['LOGIN_SIGNUP']; ?></a>
			</div>
		</div>
	</div> <!-- /container -->

