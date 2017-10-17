<?php

namespace SenByte\EasyCredit\Application\Core;

use OxidEsales\Eshop\Core\Registry;

class OxidFacade
{
    /**
     * @return string
     */
    public static function getVersion()
    {
        return Registry::getConfig()->getVersion();
    }

    /**
     * @param string $varName
     * @return mixed
     */
    public static function getShopConfVar($varName)
    {
        return Registry::getConfig()->getShopConfVar($varName);
    }

    /**
     * @return oxbasket
     */
    public static function getBasket()
    {
        return Registry::getSession()->getBasket();
    }

    /**
     * @param double $number
     * @return string
     */
    public static function formatCurrency($number)
    {
        return Registry::getLang()->formatCurrency($number);
    }

    /**
     * @param double $number
     * @return float
     */
    public static function fRound($number)
    {
        return (double)Registry::getUtils()->fRound($number);
    }

    /**
     * @param string $text
     * @return string
     */
    public static function translateString($text)
    {
        return Registry::getLang()->translateString($text);
    }

    /**
     * @param string $parameter
     * @return mixed
     */
    public static function getRequestParameter($parameter)
    {
        return Registry::getConfig()->getRequestParameter($parameter);
    }
    
    /**
     * @return string
     */
    public static function getShopUrl()
    {
        return Registry::getConfig()->getShopUrl();
    }
}
