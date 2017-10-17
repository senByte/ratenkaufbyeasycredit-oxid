[{assign var="paymentCost" value=$basket->getPaymentCosts()}]
[{if $basket->getPaymentId() == "easycredit"}]
    [{if $basket->getPaymentCosts() }]
        <tr valign="top">
            <td style="padding: 5px; border-bottom: 2px solid #ccc;[{ if $basket->getDelCostVat() }]border-bottom: 1px solid #ddd;[{/if}]">
                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                    [{if $basket->getPaymentCosts() >= 0}][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PAYMENTCHARGEDISCOUNT1" }][{else}][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PAYMENTCHARGEDISCOUNT2" }][{/if}] [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_PAYMENTCHARGEDISCOUNT3" }]
                </p>
            </td>
            <td style="padding: 5px; border-bottom: 2px solid #ccc;[{ if $basket->getDelCostVat() }]border-bottom: 1px solid #ddd;[{/if}]" align="right">
                <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 0;">
                    [{ $basket->getFPaymentCosts() }] [{ $currency->sign}]
                </p>
            </td>
        </tr>
    [{/if}]
[{else}]
[{$smarty.block.parent}]
[{/if}]
