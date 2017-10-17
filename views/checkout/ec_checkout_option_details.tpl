[{assign var=consentMsg value="MODULE_PAYMENT_EASYCREDIT_TEXT_CONSENT_TEXT"|oxmultilangassign}]

<label>
	<input type="checkbox" name="dynValue[easycredit][consent]" onchange="updateECData();" value="1" id="consent" [{if $dynValue.easycredit.consent=='1'}]checked="checked"[{/if}]/>
	[{$dynValue.consent|replace:'%s':$oxcmp_shop->oxshops__oxcompany->value}]
</label>
<div id="consent_data" style="display:none">
    <div class="input-textarea withdrawal_textarea" style="width: 98%;height: 300px;overflow:scroll; border: 1px solid #aaa;
    padding: 10px;">[{$dynValue.data_processing_service_integration}]</div>
</div>
