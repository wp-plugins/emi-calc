//Function To Display Popup
function div_show(id) {
	if ( id != "" ) {
		var data = {
			'action': 'EMIc_bank_details',
			'id': id
		};
		jQuery.post(ajaxurl,data,function(responce) {
			console.log(responce);
			var results = JSON.parse(responce);
			if(results.success == 1) {
				document.getElementById( 'bnk_name' ).value=results.bank_data.bank_name;
				document.getElementById( 'bnk_id' ).value=results.bank_data.bank_id;
				document.getElementById( 'bnk_rate' ).value=results.bank_data.interest;
				document.getElementById( 'backend_popup' ).style.display = "block";
			}
		});
	} 
	else {
		document.getElementById( 'backend_popup' ).style.display = "block";
	}
}
//Function to Hide Popup
function div_hide() {
	document.getElementById( 'backend_popup' ).style.display = "none";
}

