[{$smarty.block.parent}]
[{assign var="iPayError" value=$oView->getPaymentError() }]
[{assign var="dynval" value=$oView->getEasycreditDynValues()}]

[{ if $iPayError == -333}]
	[{if method_exists($oViewConf, 'getActiveTheme') && $oViewConf->getActiveTheme() == 'mobile'}]
        <div class="payment-row">
            <div class="alert alert-error">[{ oxmultilang ident="MODULE_PAYMENT_EASYCREDIT_ERROR_RED" }]</div>
        </div>    
    [{elseif method_exists($oViewConf, 'getActiveTheme') && $oViewConf->getActiveTheme() == 'flow'}]
        <div class="alert alert-danger">[{ oxmultilang ident="MODULE_PAYMENT_EASYCREDIT_ERROR_RED" }]</div>
    [{else}]
        <div class="status error">[{ oxmultilang ident="MODULE_PAYMENT_EASYCREDIT_ERROR_RED" }]</div>
    [{/if}]
[{elseif $iPayError == -334}]
	[{if method_exists($oViewConf, 'getActiveTheme') && $oViewConf->getActiveTheme() == 'mobile'}]
        <div class="payment-row">
        	<div class="alert alert-error">
        		[{foreach key=i item=error from=$dynval.errors}]
			    [{$error}]<br>
			    [{/foreach}]
        	</div>
        </div>
    [{elseif method_exists($oViewConf, 'getActiveTheme') && $oViewConf->getActiveTheme() == 'flow'}]
		<div class="alert alert-danger">
		    [{foreach key=i item=error from=$dynval.errors}]
		    [{$error}]<br>
		    [{/foreach}]
		</div>
	 [{else}]
        <div class="status error">
        	[{foreach key=i item=error from=$dynval.errors}]
		    [{$error}]<br>
		    [{/foreach}]
        </div>
    [{/if}]
[{/if}]