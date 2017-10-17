function updateECData() {
    data = {
        consent: jQuery('#consent').is(":checked") ? 1 : 0,
        installment: jQuery('#installment_select').val(),
    }
    jQuery.ajaxSetup({
        cache: true
    });
    jQuery.post('index.php?cl=easycredit_update', data, function(data) {
    	if(jQuery('#consent').is(":checked")) {
    		jQuery('#consent').parent().removeClass('alert alert-danger')
    	}
    });
}

window.onload = function () {
	updateECData();
	$('#paymentNextStepBottom').click(function() {
		if(jQuery('#payment_easycredit').is(":checked")) {
			if(jQuery('#consent').is(":checked")) {
				return true;
			} else {
				jQuery('#consent').parent().addClass('alert alert-danger');
				return false;
			}
		}
	});
};