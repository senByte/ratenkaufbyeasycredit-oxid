[{if $payment->oxuserpayments__oxpaymentsid->value == "easycredit"}]
    [{$smarty.block.parent}]
    <p>[{ oxmultilang ident="MODULE_EMAIL_ORDER_CONFIRMATION_EASYCREDIT_IDENTIFIER" }]: [{$oViewConf->getTechnicalTbaId()}]</p>
[{else}]
    [{$smarty.block.parent}]
[{/if}]
