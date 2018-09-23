<?php
/**
 * Google Maps Draw Module
 * @package drawonmaps
 * 
 * File: html_footer.php
 * Description: HTML footer
 */
?>


<?php 
	// do not show footer if only map display option is asked
	if (!$vars['map_o']) 
	{ 
?>
	<div id="footer">
		<div class="container">
			<div class="jumbotron">
				<?php echo $lang_msg['HTML_FOOTER_NOTE']; ?>
			</div>
		</div>
	</div>
<?php } ?>

	<!-- JavaScript Code
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->

	<!-- Bootstrap JS -->
	<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
	<!--<script type="text/javascript" src="bootstrap/bootstrap-select/bootstrap-select.min.js"></script>-->
	<script type="text/javascript" src="bootstrap/js/validator.min.js"></script>
			
	<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
	<script src="js/ie10-viewport-bug-workaround.js"></script>


  </body>
</html>
