[{if $payment->oxuserpayments__oxpaymentsid->value == "easycredit"}]
    [{$smarty.block.parent}]

[{ oxmultilang ident="MODULE_EMAIL_ORDER_CONFIRMATION_EASYCREDIT_IDENTIFIER" }]: [{$oViewConf->getTechnicalTbaId()}]

[{else}]
    [{$smarty.block.parent}]
[{/if}]
