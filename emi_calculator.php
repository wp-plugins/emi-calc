<?php
/*
plugin name:EMI Calculator
description: Dynamic EMI calculator adjustable from admin panel
author: Rqubes Infotech
version: 1.0
*/


/*---------- create table on activation (start here) -----------*/
define( 'calculator_emi', plugin_dir_url( __FILE__ ) );
function EMIc_activation() {
	global $wpdb;
	$sql = "CREATE TABLE ".$wpdb->prefix."bank_details (
	  bank_id int(11) NOT NULL AUTO_INCREMENT,
	  bank_name varchar(255) NULL,
	  interest varchar(255) NULL,
	  PRIMARY KEY bank_id (bank_id)
	) ENGINE=InnoDB";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}
/*---------- create table on activation (end here) -----------*/

register_activation_hook( __FILE__, 'EMIc_activation' );
add_action( 'admin_menu', 'register_EMIc_banks' );
function register_EMIc_banks(){
	add_menu_page( 'Banks Interest Rate', 'EMI Calculator', 'manage_options', 'emi_calc', 'EMIc_Banks', '', 6 ); 
	wp_register_style( 'EMIc_css', plugin_dir_url( __FILE__ ).'css/EMIc.css' );
	wp_enqueue_style( 'EMIc_css' );
	wp_enqueue_script( 'custom_js', plugin_dir_url( __FILE__ ).'js/custom_js.js' );
}
function EMIc_Banks() {
	global $wpdb;
	$error_msg = '';
	if( isset( $_POST['Submit'] ) ){
		$bankname 	= sanitize_text_field( $_POST["bankname"] );
		$irate = preg_replace( '/[^0-9.]/', '', sanitize_text_field( $_POST["irate"] ) );
		/*------ update bank details (start here) ------*/
		if( isset( $_POST["bank_id"] ) && $_POST["bank_id"]!='' ) {
			$wpdb->update( $wpdb->prefix."bank_details",
			array( 'bank_name' => $bankname, 'interest' => $irate, ), array( 'bank_id' => $_POST["bank_id"] ) );
		}
		/*------ update bank details (end here) ------*/
		
		/*------ insert bank details (start here) ------*/
		else {
			$check_bank_data = $wpdb->get_col( $wpdb->prepare( "SELECT bank_name FROM wp_bank_details where bank_name = %s ",$bankname ), ARRAY_A );
			if( empty( $check_bank_data ) ) {
				$wpdb->insert( $wpdb->prefix."bank_details",
				array( 'bank_name' => $bankname, 'interest' 	=> $irate, ), array( '%s', '%s' ) );
			}
			else { ?>
				<script>
					alert( 'This Bank is Already Exist' );
				</script>
	  <?php }
		}
		/*------ insert bank details (end here) ------*/		
	} ?>
	<div id="backend_popup">
	<div id="popupbackend">
	<!---- form for insert and update bank details (start here) -------->
	<form action="<?php echo admin_url(); ?>admin.php?page=emi_calc" method="post" id="my_form">
	<table>
		<img src="../wp-content/plugins/EMI_Calculator/image/Delete.png" id="close" onclick="window.location.href='<?php echo admin_url(); ?>admin.php?page=emi_calc'"/>
		<h2 id="h2">Bank Form</h2>
		<tr>
			<td></td><td><input type="hidden" id="bnk_id" name="bank_id" value="" /></td>
		</tr>
		<tr>
			<td>Bank Name:</td><td><input type="text" id="bnk_name" name="bankname" value="" placeholder="Enter Bank Name" required/></td>
		</tr>
		<tr>
			<td>Interest Rate:</td><td><input type="text" id="bnk_rate" name="irate" value="" placeholder="0.00" required/>
			<span style="color: black; font-weight: bold; font-size: 17px;">%</span></td>
		</tr>
		<tr>
			<td></td><td><input name="Submit" type="submit" value="Submit" id="submit_bank"></td>
		</tr>
	</table>
	</form>
	<!---- form for insert and update bank details (end here) -------->
	</div>
	</div>
	<?php
	/*------ Delete Bank Details (start here) ------*/
	if( isset( $_POST['Delete'] ) ) { 
		$checkbox = $_POST['checkbox'];
		if( !empty( $checkbox ) ) {
			foreach( $checkbox as $id=>$val ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM wp_bank_details WHERE bank_id = %d ",$id ) );
			}
		}
	}
	/*------Delete Bank Details (end here)------*/
	$get_bank_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM wp_bank_details",1 ), ARRAY_A ); ?>
	<!-- list form (start here) ------>
	<form action="" method="post">
	<table style="width: 90%; background-color: #EAEAEA; margin-left: 40px;" class="table">
		<h1 class="table_heading">Table of Banks</h1>	
		<div class="manage-button">
			<input type="button" class="Add" name="Add Bank" value="New" onclick="div_show(id='')">
			<input type="submit" class="Delete" name="Delete" value="Remove">
		</div>
		<tr class="tr">
			<th class="th" style="width:5%;">S.No.</th><th class="th" style="width:10%;">Select</th><th class="th" style="width:50%;">Bank Name</th><th class="th" style="width:20%;">Interest Rate</th><th class="th" style="width:2%;">Edit</th>
		</tr>
	 <?php  $count = 1;
			foreach ( $get_bank_data as $bank ) { ?>
		<tr class="tr">
			<td class="td"><?php echo $count; ?></td>	
			<td class="td"><input name='checkbox[<?php echo $bank['bank_id']; ?>]' type='checkbox' id='checkbox[]' value="<?php echo $bank['bank_id']; ?>"></td>
			<td class="td"><?php echo $bank['bank_name']; ?></td>		
			<td class="td"><?php echo $bank['interest']; ?> %</td>
			<td>
				<button type="button" name="Edit" class="Edit" value="<?php echo $bank['bank_id']; ?>" onclick="div_show(id=<?php echo $bank['bank_id']; ?>)">Edit</button>
			</td>
		</tr>
		<?php 
			$count++;
		} ?>
		
		</table>
	</form>
	<!-- list form (end here) ------>
<?php 
} 
/*---- code to show calculator (start here) -----*/
function EMIc_shortcode( $atts, $content = null ) {
	wp_register_script( 'calc_js', calculator_emi.'js/calc_js.js' );
	wp_enqueue_script( 'calc_js' );
	wp_register_style( 'frontcss', calculator_emi.'css/front.css' );
	wp_enqueue_style( 'frontcss' ); ?>
	<h2 id="calc-header">EMI Calculator</h2>
	<div class="emi-form">
		<form action="" method="post" name="calc_form">
			<table class="table table-bordered">
				<tr class="outstanamount">
					<td>Principal:<span class="WebRupee"> (Rs.)</span></td>
					<td>
						<div>
							<input type="text" name="amount" placeholder="Enter Amount" id="principle" value="">
						</div>
						<div id="osp_range"></div>
					</td>
				</tr>
				<tr class="intrate">
					<td>Bank & Interest Rate (%)</td>
					<td>
						<select id="interest_rate" name="selected_bank">
							<option value="0">--Select Bank--</option>
							<?php global $wpdb;
							$bank_list = $wpdb->get_results( "SELECT * FROM wp_bank_details" );
							foreach ($bank_list as $single_bank) {?>
								<option value='<?php echo $demo="$single_bank->interest"; ?>'>
									<?php echo $demo="$single_bank->bank_name".' '.$demo="$single_bank->interest"; ?>%
								</option>
					  <?php }?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Tenure:</td>
					<td>
						<div class="tenurechoice">
							<div>
								<input name="loantenure" id="loanyears" class="loanperiod" value="loanyears" type="radio">
									<label id="loanyearslabel" for="loanyears">Years</label>
								<input name="loantenure" class="loanperiod" id="loanmonths" value="loanmonths" type="radio" checked="checked">
									<label id="loanmonthslabel" for="">Months</label>
							</div>
						</div>
						<div class="term">
							<input type="text" name="time_duration" placeholder="Enter Tenure" id="tenure" value="">
						</div>
					</td>
				</tr>
				<tr>
					<td></td><td><input type="button" value="calculation" id="calc_button" onclick="EMIc_calculation();"></td>
				</tr>
			</table>
		</form>
	</div>
<div id="front-popup">
	<div id="popupupdate">
		<form name="my_form" id="calc_result">
			<img src="../wp-content/plugins/EMI_Calculator/image/Delete.png" id="close" onclick="div_hide1();" />
			<div id="summary">
				<div id="">
					<h4 id="h4">Monthly EMI:</h4> <p> Rs. <span id="emi"></span></p>
				</div>
				<div id="">
					<h4 id="h4">Total Interest:</h4>
					<p>Rs. <span id="tipay"></span></p>
				</div>
				<div id="">
					<h4 id="h4">Total Payout:</h4>
					<p>Rs. <span id="totpay"></span></p>
				</div>
			</div>
		</form>
	</div>
</div>
<?php 
}
add_shortcode( 'Bank_EMI_calc','EMIc_shortcode' );
add_action( 'wp_ajax_get_bank_details', 'EMIc_bank_details' );
add_action( 'wp_ajax_nopriv_get_bank_details', 'EMIc_bank_details' );
add_filter( 'widget_text', 'EMIc_shortcode' ); 
function EMIc_bank_details()
{
	global $wpdb;
	if ( isset( $_POST['id'] ) && $_POST['id'] != "" ) {
		$get_edit_bank_data = $wpdb->get_row( $wpdb->prepare( "SELECT bank_id, bank_name ,interest  FROM ".$wpdb->prefix."bank_details where bank_id = %d ",$_POST['id'] ), ARRAY_A );
		if ( ! empty( $get_edit_bank_data ) ) {
			echo wp_json_encode( array( 'success' => '1', 'bank_data' => $get_edit_bank_data ) );
		} 
		else {
			echo wp_json_encode( array( 'error' => '1' ) );
		}
	}
	else {
		echo wp_json_encode( array( 'error' => '1' ) );
	}
	die();
} ?>
