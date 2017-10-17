[{$smarty.block.parent}]
[{if $oViewConf->isEasycreditEnabled() && $oDetailsProduct->isEasycreditFinanceable()}]
    <div class="easycredit_widget"
         style="padding-left: 100px; height: 40px;background: url('[{$oViewConf->getEasycreditLogo()}]') no-repeat left top 0.5em transparent; background-size: 90px;">
        <strong>[{oxmultilang ident="EASYCREDIT_FINANCEABLE_FROM"}] [{$oDetailsProduct->getEasycreditBestRate()}] / [{oxmultilang ident="EASYCREDIT_MONTH"}]</strong>
        <br>
        <span id="easycredit_acc_cond">
            <a href="[{$oDetailsProduct->getEasycreditExampleCalculationLink()}]"
               target="_blank">[{oxmultilang ident="EASYCREDIT_FINANCING_MORE_INFOS"}]</a>
        </span>
    </div>
[{/if}]
