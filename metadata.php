<?php


/**
 * Metadata version
*/
$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = array(
    'id' => 'easycredit',
    'title' => 'ratenkauf by easyCredit',
    'description' => 'Modul zur Integration der Zahlart ratenkauf by easyCredit',
    'version' => '2.0.0',
    'author' => 'senByte UG',
    'thumbnail' => 'thumb.png',
    'url' => 'https://www.easycredit.de/ratenkaufpartner',
    'email' => 'info@senbyte.com',
    'controllers' => array(
        'easycredit_update' => SenByte\EasyCredit\Application\Controllers\EasyCreditUpdate::class,
    ),
    'templates' => array(
        'ec_checkout_select_payment.tpl' => 'senbyte/ratenkaufbyeasycredit/views/checkout/ec_checkout_select_payment.tpl',
        'ec_checkout_option_details.tpl' => 'senbyte/ratenkaufbyeasycredit/views/checkout/ec_checkout_option_details.tpl',
    ),
    'extend' => array(
        \OxidEsales\Eshop\Application\Controller\PaymentController::class => SenByte\EasyCredit\Application\Controllers\EasyCreditPaymentController::class,
        \OxidEsales\Eshop\Application\Controller\OrderController::class => SenByte\EasyCredit\Application\Controllers\EasyCreditOrderController::class,
        \OxidEsales\Eshop\Application\Model\Order::class => SenByte\EasyCredit\Application\Models\EasyCreditOrder::class,
        \OxidEsales\Eshop\Application\Model\PaymentGateway::class => SenByte\EasyCredit\Application\Models\EasyCreditPaymentGateway::class,
        \OxidEsales\Eshop\Core\ViewConfig::class => SenByte\EasyCredit\Application\Core\EasyCreditViewConfig::class,
        \OxidEsales\Eshop\Application\Model\Article::class => SenByte\EasyCredit\Application\Models\EasyCreditArticle::class,
        \OxidEsales\Eshop\Application\Model\Payment::class => SenByte\EasyCredit\Application\Models\EasyCreditPayment::class,
    ),
    'settings' => array(
        array(
            'group' => 'main',
            'name' => 'EASYCREDIT_SHOP_ID',
            'type' => 'str',
            'value' => '',
            'position' => 0,
        ),
        array(
            'group' => 'main',
            'name' => 'EASYCREDIT_SHOP_TOKEN',
            'type' => 'str',
            'value' => '',
            'position' => 10,
        ),
        array(
            'group' => 'LOGGING',
            'name' => 'EASYCREDIT_LOG_LEVEL',
            'type' => 'select',
            'value' => 'error',
            'constraints' => 'error|debug',
            'position' => 20,
        ),
        array(
            'group' => 'PROXY',
            'name' => 'EASYCREDIT_PROXY_STATUS',
            'type' => 'select',
            'value' => '0',
            'constraints' => '0|1',
            'position' => 30,
        ),
        array(
            'group' => 'PROXY',
            'name' => 'EASYCREDIT_PROXY_HOST',
            'type' => 'str',
            'value' => '',
            'position' => 40,
        ),
        array(
            'group' => 'PROXY',
            'name' => 'EASYCREDIT_PROXY_PORT',
            'type' => 'str',
            'value' => '',
            'position' => 50,
        ),
        array(
            'group' => 'PROXY',
            'name' => 'EASYCREDIT_PROXY_USERNAME',
            'type' => 'str',
            'value' => '',
            'position' => 60,
        ),
        array(
            'group' => 'PROXY',
            'name' => 'EASYCREDIT_PROXY_PASSWORD',
            'type' => 'str',
            'value' => '',
            'position' => 70,
        ),
    ),
    'blocks' => array(
        array(
            'template' => 'page/details/inc/productmain.tpl',
            'block' => 'details_productmain_social',
            'file' => 'views/blocks/product/ec_productmain.tpl',
        ),
        array(
            'template' => 'page/checkout/inc/basketcontents.tpl',
            'block' => 'checkout_basketcontents_paymentcosts',
            'file' => 'views/blocks/checkout/ec_checkout_payment_costs.tpl',
        ),
        array(
            'template' => 'page/checkout/payment.tpl',
            'block' => 'select_payment',
            'file' => 'views/blocks/checkout/ec_select_payment.tpl',
        ),
        array(
            'template' => 'page/checkout/payment.tpl',
            'block' => 'checkout_payment_errors',
            'file' => 'views/blocks/checkout/checkout_payment_errors.tpl'
        ),
        array(
            'template' => 'page/checkout/order.tpl',
            'block' => 'shippingAndPayment',
            'file' => 'views/blocks/checkout/ec_preinformation.tpl',
        ),
        array(
            'template' => 'email/html/order_owner.tpl',
            'block' => 'email_html_order_owner_paymentinfo',
            'file' => 'views/email/html/order_owner.tpl',
        ),
        array(
            'template' => 'email/plain/order_owner.tpl',
            'block' => 'email_plain_order_ownerpaymentinfo',
            'file' => 'views/email/plain/order_owner.tpl',
        ),
    ),
    'events' => array(
        'onActivate' => 'SenByte\EasyCredit\Application\Core\EasyCreditInstaller::onActivate',
        'onDeactivate' => 'SenByte\EasyCredit\Application\Core\EasyCreditInstaller::onDeactivate',
    ),
);
