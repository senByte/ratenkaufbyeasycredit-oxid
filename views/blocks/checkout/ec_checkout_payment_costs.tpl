[{assign var="paymentCost" value=$oxcmp_basket->getFPaymentCosts()}]
[{assign var="sPaymentID" value=$payment->oxpayments__oxid}]
[{if $sPaymentID == "easycredit"}]
    [{if $paymentCost}]
        <tr>
            <th>
             [{ oxmultilang ident="MODULE_PAYMENT_EASYCREDIT_TEXT_INTEREST" }]:
            </th>
            <td id="basketPaymentGross">[{$oxcmp_basket->getFPaymentCosts()}]&nbsp;[{ $currency->sign }]</td>
        </tr>
    [{/if}]
[{else}]
    [{$smarty.block.parent}]
[{/if}]
