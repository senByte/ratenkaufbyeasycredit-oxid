<?php

namespace SenByte\EasyCredit\Application\Models;

use SenByte\EasyCredit\Application\Core\EasyCredit;

/**
 * extends Oxid Order Model
 * destroys easycredit process once order is finalized
 * 
 * @author info@senbyte.com
 * @copyright 2017 senByte UG
 */

class EasyCreditOrder extends EasyCreditOrder_parent
{

    /**
     * @param \OxidEsales\Eshop\Application\Model\Basket $oBasket              Basket object
     * @param object                                     $oUser                Current User object
     * @param bool                                       $blRecalculatingOrder Order recalculation
     *
     * @return integer
     */
    public function finalizeOrder(\OxidEsales\Eshop\Application\Model\Basket $oBasket, $oUser, $blRecalculatingOrder = false) {
        $iRet = parent::finalizeOrder($oBasket, $oUser, $blRecalculatingOrder);
        
        if ($oBasket->getPaymentId() == 'easycredit') {
            $ec = EasyCredit::getInstance();
            $ecProcess = $ec->getProcess();
            if ($iRet == 1) {
                $ecProcess->destroy();
            }
        }
        return $iRet;
    }
}
