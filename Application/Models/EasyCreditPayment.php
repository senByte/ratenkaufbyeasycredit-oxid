<?php

namespace SenByte\EasyCredit\Application\Models;

use EasyCredit\Transfer\ProcessData;
use SenByte\EasyCredit\Application\Core\OxidFacade;
use SenByte\EasyCredit\Application\Core\EasyCredit;

class EasyCreditPayment extends EasyCreditPayment_parent
{

    /**
     * @param array  $aDynvalue    dynamical value (in this case oxidcreditcard and oxiddebitnote are checked only)
     * @param string $sShopId      id of current shop
     * @param oxuser $oUser        the current user
     * @param double $dBasketPrice the current basket price (oBasket->dprice)
     * @param string $sShipSetId   the current ship set
     * @return bool true if payment is valid
     */
    public function isValidPayment($aDynvalue, $sShopId, $oUser, $dBasketPrice, $sShipSetId)
    {
        $isValid = parent::isValidPayment($aDynvalue, $sShopId, $oUser, $dBasketPrice, $sShipSetId);
        
        if ($this->oxpayments__oxid != "easycredit") {
            $this->getSession()->deleteVariable('ec_rv');
            return $isValid;
        }
        
        if (!$ecReturnValue = OxidFacade::getRequestParameter('ec_rv')) {
            $ecReturnValue = $this->getSession()->getVariable('ec_rv');
        }
        $ecProcess = EasyCredit::getInstance()->getProcess();
        
        if ($ecReturnValue == 'dec') {
            $this->getSession()->deleteVariable('ec_rv');
            $this->_iPaymentError = -333;
            return false;
        } else if ($ecReturnValue == 'ack') {
            if ($ecProcess->getDecision()->getDecision()->getResult() === 'GRUEN') {
                $isValid = true;
                $this->getSession()->setVariable('ec_rv', 'ack');
            } else {
                $this->_iPaymentError = -336;
                $this->getSession()->deleteVariable('ec_rv');
                return false;
            }
        } else {
            $ecProcess->getProcessData()->initEmpty();
            $ecProcess->getProcessData()->save();
            $this->calculate($this->getSession()->getBasket());
            $this->_iPaymentError = -335;
            $this->getSession()->deleteVariable('ec_rv');
            return false;
        }
        return $isValid;
    }

    /**
     * @param $oBasket
     */
    public function calculate($oBasket)
    {
        if ($this->oxpayments__oxid != "easycredit") {
                return parent::calculate($oBasket);
        }
        
        $ecProcess = EasyCredit::getInstance()->getProcess();
        $processData = $ecProcess->getProcessData();

        if ($processData->getStatus() !== \EasyCredit\Process\Status::ACCEPTED
            || $processData->isValid() !== true
            || !$ecProcess->checkBasketContent($this->getSession()->getUser(), $oBasket)
        ) {
            $this->_oPrice = null;

            return;
        }

        $financingDetails = $ecProcess->getFinancingDetails();

        $oPrice = oxNew('oxPrice');
        if (is_callable($oPrice, 'setNettoMode')) {
            $oPrice->setNettoMode($this->_blPaymentVatOnTop);
        }
        $oPrice->setPrice($financingDetails->getInstallmentPlan()->getInterestRate()->getAccruingInterest());

        return $this->_oPrice = $oPrice;
    }

    /**
     * @param $oBasket
     * @return mixed
     */
    public function getPaymentPrice($oBasket)
    {
        $this->calculate($oBasket);

        if ($this->_oPrice !== null) {
            return $this->_oPrice;
        }

        return parent::getPaymentPrice($oBasket);
    }
}
