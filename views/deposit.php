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
		<h3 class="doc_title">Deposit</h3>
		<!-- Main component for a primary marketing message or call to action -->
		<div class="jumbotron">
        <div class="alert alert-warning">
  <strong>Warning!</strong> Test mode enabled , no transactions are actually made
</div>

<form  method="post" action="index.php?p=map_save" role="form" data-toggle="validator">
							<div class="row" id="allmarks"></div>  
							<div class="row" id="map_info">
<div class="col-lg-9"> 
								<div class="form-group">
									<label for="map_title" class="control-label"><?php echo $lang_msg['DEPOSIT_AMOUNT']; ?></label>
									<input type="text" class="form-control" id="map_title" name="map_title" placeholder="<?php echo $lang_msg['DEPOSIT_AMOUNT']; ?>" required>

									<label for="map_title" class="control-label"><?php echo $lang_msg['DEPOSIT_YOUR_ADDRESS']; ?></label>
									<input type="text" class="form-control" id="map_title" name="map_title" placeholder="<?php echo $lang_msg['DEPOSIT_YOUR_ADDRESS']; ?>" required>
									<label for="map_title" class="control-label"><?php echo $lang_msg['DEPOSIT_OWNER']; ?></label> <br />


									<div class="help-block with-errors"></div>
								</div>
							<p> After you send ETH register your deposit request usning the button below , our team will verify your request and validate the deposit if it is legit  </p>
								<input type="hidden" id="map_center_lat" name="map_center_lat">
								<input type="hidden" id="map_center_lng" name="map_center_lng">
								<input type="hidden" id="map_zoom" name="map_zoom">
								<input type="hidden" id="map_typeid" name="map_typeid">
								<input type="hidden" id="user_id" name="user_id" value="<?php echo (isset($_SESSION['user']) && intval($_SESSION['user'])>0?$_SESSION['user']:0); ?>">
								<button type="submit" class="btn btn-default" id="addmap_btn"><?php echo $lang_msg['DEPOSIT_BUTTON_REGISTER']; ?></button>
 </div>

<div class="col-lg-3"> 
<img style="margin-top:10px;" class="mx-auto d-block" height="200px" width="140px" src="deposit_address.png" />

 </div>






							</div>  
						</form>













		<table class="table table-striped">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Source address</th>
      <th scope="col">Amount</th>
      <th scope="col">Received in CIT</th>
      <th scope="col">Status</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">1</th>
      <td>0xD8D4b950D1C304C31A4734bC439B18b468cD0fC2</td>
      <td>300 ETH</td>
      <td>100 CIT</td>
      <td>Pending</td>
    </tr>

  </tbody>
</table>

 








		</div> 
	</div> <!-- /container -->

