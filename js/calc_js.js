var f=jQuery.noConflict( false ); 
f(document).ready(function () { 
      f('.loanperiod').click(function(){
            if(f(this).is(':checked')){
                 var cd=f(this).val(),
                     tp=f('#tenure'),
                     gh=f('#itm'),
                     ghv=f('#itm').val(),
                     zx='';
                     tpv=0;
                 if(cd=='loanyears'){
                     zx=(tpv/12);tp.val(zx);
                     gh.val('yth');
                 }
                 if(cd=='loanmonths'&& ghv !='mth' ){ zx=Math.round(tpv*0); tp.val(zx);} 
            }   
         });
        });
function EMIc_calculation(obj) {  
	var amount = document.forms["calc_form"]["amount"].value;
	var bank = document.forms["calc_form"]["selected_bank"].value;
	var time = document.forms["calc_form"]["time_duration"].value;
    if (amount == null || amount == "") {
        alert("Please fill Loan Amount");
        return false;
    }
	if(amount == 0) {
		alert("please fill Valid Amount")	
		return false;
	}
	if (bank == null || bank == "" || bank == 0) {
        alert("Please select Bank");
        return false;
    }
    if (time == null || time == "") {
        alert("Please fill Loan Perid");
        return false;
    }
	if(time == 0) {
		alert("please fill Valid Time Period")	
		return false;	
	}
        var op=f("#principle").val(),
        ir=f("#interest_rate").val(),
        ten=f("#tenure").val(),
        ipp=pap=tp=ip='',
        em=f('#emi'),
        tipay=f('#tipay'),
        totpay=f('#totpay');
    if ((!isNaN(op) && op !== 0)||(!isNaN(ir) && ir !== 0)||(!isNaN(ten) && ten !== 0)) {
		var emi = 0,P = 0,n = 1,r = 0;                                       
		P = parseFloat(op);
		r = parseFloat(parseFloat(ir) / 100);
		n = parseFloat(ten);
		if(f('#loanyears').is(':checked')) { n=n*12; }
		if (P !== 0 && n !== 0 && r !== 0)
		emi = parseFloat((P * r / 12) * [Math.pow((1 + r / 12), n)] / [Math.pow((1 + r / 12), n) - 1]);
		em.text(CommaFormatted(emi.toFixed(2)));
		tp=(emi*n).toFixed(2);
		ip=(tp-P);
		ipp=((ip/tp)*100).toFixed(2);
		pap=(100-ipp).toFixed(2);
		tipay.text(CommaFormatted(ip.toFixed(2)));
		totpay.text(CommaFormatted(((tp*100)/100)));
		ipp=parseFloat(ipp);         
		pap=parseFloat(pap);    
		document.getElementById('front-popup').style.display = "block";      
	}
}  
function CommaFormatted(amount) {
	var numberStr = amount.toString();
    var thousandsMatcher = /(\d+)(\d{3})$/;
    var thousandsAndRest = thousandsMatcher.exec(numberStr);
    if (!thousandsAndRest) return numberStr;
    return thousandsAndRest[1].replace(/\B(?=(\d{2})+(?!\d))/g, ",") + "," + thousandsAndRest[2];
}
function div_hide1(){
	document.getElementById('front-popup').style.display = "none";
}




                 




