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
                $this->setPaymentInfo($ecProcess->getProcessData()->getTechnicalTbaId());
                $ecProcess->destroy();
            }
        }
        return $iRet;
    }
    
    /**
     * sets technical TBA ID to order
     *
     * @param string $technicalTbaId
     */
    protected function setPaymentInfo($technicalTbaId)
    {
        $db = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
    
        $query = 'update oxorder set oxtransid=' . $db->quote($technicalTbaId) . ' where oxid=' . $db->quote($this->getId());
        $db->execute($query);
    
        $this->oxorder__oxtransid = new \OxidEsales\Eshop\Core\Field($technicalTbaId);
    }
}
