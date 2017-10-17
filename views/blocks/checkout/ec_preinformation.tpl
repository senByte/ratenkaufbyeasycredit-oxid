[{$smarty.block.parent}]
[{assign var="sPaymentID" value=$payment->oxpayments__oxid}]
[{if $sPaymentID == "easycredit"}]
    <div id="ecPaymentPreInformation" style="margin-bottom: 35px;">
        <h3 class="section">
            <strong>[{ oxmultilang ident="MODULE_PAYMENT_EASYCREDIT_TEXT_PAYMENT_INFORMATION" }]</strong>
        </h3>
    
        <p><strong>[{ oxmultilang ident="MODULE_PAYMENT_EASYCREDIT_TEXT_IMPORTANT_INFORMATION" }]</strong></p>
        <p>[{$oViewConf->getRepaymentPlanText()}]</p>

        <p class="agb">
            <a target="_blank" href="https://ratenkauf.easycredit.de/ratenkauf/content/intern/vorvertraglicheInformationen.jsf?vorgangskennung=[{$oViewConf->getTbaId()}]">[{ oxmultilang ident="MODULE_PAYMENT_EASYCREDIT_TEXT_PRE_INFORMATION" }]</a>
        </p>
    </div>
[{/if}]
