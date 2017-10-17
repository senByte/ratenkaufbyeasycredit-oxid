[{assign var=isActivePaymentChecked value=false}]
[{assign var=dynvalue value=$oView->getEasycreditDynValues()}]

[{oxstyle include=$oViewConf->getModuleUrl("easycredit", "out/src/css/easycredit.css")}]
[{oxscript include=$oViewConf->getModuleUrl("easycredit", "out/src/js/easycredit.js")}]

[{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]
    [{assign var=isActivePaymentChecked value=true}]
[{/if}]

[{if $dynvalue.is_active}]
<div class="well well-sm">
	<dl class="easyCredit-payment">
	            <dt>
	                <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}]>
	                <label for="payment_[{$sPaymentID}]"><b>[{ $paymentmethod->oxpayments__oxdesc->value}] [{oxmultilang ident="EASYCREDIT_FROM"}] [{$dynvalue.bestRate}] &euro; / [{oxmultilang ident="EASYCREDIT_MONTH"}]</b></label>
	                <div class="ec-row">
			            <div class="flr">
		                    <select id="installment_select" name="dynValue[easycredit][installment]" onchange="updateECData()">
		                        [{foreach key=i item=installmentPlan from=$dynvalue.installmentPlans}]
		                            <option value="[{$installmentPlan.payment_schedule.number_of_rates}]" [{if $dynvalue.easycredit.installment==$installmentPlan.payment_schedule.number_of_rates}]selected="selected" [{/if}]>
		                                [{$installmentPlan.payment_schedule.number_of_rates}]
		                                [{oxmultilang ident="EASYCREDIT_TERMS"}]
		                                [{$installmentPlan.payment_schedule.amount_of_rate|number_format:2:",":"."}]
		                                [{oxmultilang ident="EASYCREDIT_EURO"}]
		                            </option>
		                        [{/foreach}]
		                    </select>
			            </div>
			        </div>
	            </dt>
	
	            <dd class="full-width [{if $isActivePaymentChecked}] activePayment[{/if}]">
	                [{include file="ec_checkout_option_details.tpl" dynValue=$dynvalue}]
	            </dd>
	</dl>
</div>
[{/if}]
