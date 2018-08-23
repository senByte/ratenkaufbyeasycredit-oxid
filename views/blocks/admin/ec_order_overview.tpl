<tr>
    <td class="edittext">[{oxmultilang ident="ORDER_OVERVIEW_PAYMENTTYPE"}]:</td>
    <td class="edittext"><b>[{$paymentType->oxpayments__oxdesc->value}]</b></td>
</tr>
[{if $paymentType->oxuserpayments__oxpaymentsid->value == "easycredit"}]
	<tr>
		<td class="edittext">[{oxmultilang ident="EASYCREDIT_TECHNICAL_TBA_ID"}]: </td>
		<td class="edittext"><b>[{$edit->oxorder__oxtransid->value}]</b></td>
	</tr>
[{/if}]
<tr>
    <td class="edittext">[{oxmultilang ident="ORDER_OVERVIEW_DELTYPE"}]: </td>
    <td class="edittext"><b>[{$deliveryType->oxdeliveryset__oxtitle->value}]</b><br></td>
</tr>
