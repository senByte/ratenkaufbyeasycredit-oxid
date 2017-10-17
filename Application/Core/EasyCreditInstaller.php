<?php

namespace SenByte\EasyCredit\Application\Core;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Core\DatabaseProvider;

require_once __DIR__.'/../../lib/autoload.php';

class EasyCreditInstaller extends FrontendController
{

    /**
     * @return boolean
     */
    public static function onActivate()
    {
        if (self::isInstalled()) {
            return false;
        }

        $fromAmount = \EasyCredit\Config::MIN_ORDER_AMOUNT;
        $toAmount = \EasyCredit\Config::MAX_ORDER_AMOUNT;
        DatabaseProvider::getDb()->Execute(
            "INSERT INTO oxpayments ".
            "(OXID, OXACTIVE, OXDESC, OXADDSUM, OXADDSUMTYPE, OXFROMBONI, OXFROMAMOUNT, OXTOAMOUNT, OXVALDESC, OXCHECKED, OXDESC_1, OXVALDESC_1, OXDESC_2, OXVALDESC_2, OXDESC_3, OXVALDESC_3, OXLONGDESC, OXLONGDESC_1, OXLONGDESC_2, OXLONGDESC_3, OXSORT) ".
            "VALUES ".
            "('easycredit', 1, 'Ratenkauf by easyCredit', 0, 'abs', 0, {$fromAmount}, {$toAmount}, '', 1, 'Ratenkauf by easyCredit', '', '', '', '', '', '', '', '', '', 0); "
        );

        return true;
    }

    /**
     * @return boolean
     */
    public static function onDeactivate()
    {
        if (!self::isInstalled()) {
            return false;
        }

        DatabaseProvider::getDb()->Execute(
            "DELETE FROM oxpayments WHERE OXID = 'easycredit';"
        );
        
        DatabaseProvider::getDB()->Execute(
            "DELETE FROM oxtplblocks WHERE OXMODULE = 'easycredit';"
        );

        return true;
    }

    /**
     * @return bool
     */
    public static function isInstalled()
    {
        $query = "SELECT COUNT(oxid) FROM oxpayments WHERE oxid IN ('easycredit')";
        if (DatabaseProvider::getDb(true)->getOne($query) != 1) {
            return false;
        }

        return true;
    }
}
