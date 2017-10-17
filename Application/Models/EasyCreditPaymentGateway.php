<?php

namespace SenByte\EasyCredit\Application\Models;

use SenByte\EasyCredit\Application\Core\EasyCredit;

class EasyCreditPaymentGateway extends EasyCreditPaymentGateway_parent
{
    /**
     * Executes payment, returns true on success.
     * returns false if basket or order amount has changed
     *
     * @param double $dAmount Goods amount
     * @param object $oOrder  User ordering object
     *
     * @return bool
     */
    public function executePayment($dAmount, &$oOrder)
    {
        $blSuccess = parent::executePayment($dAmount, $oOrder);

        if ($blSuccess && $this->_oPaymentInfo->oxuserpayments__oxpaymentsid->value == 'easycredit') {
            $ec = EasyCredit::getInstance();
            $ecProcess = $ec->getProcess();

            if (!$ecProcess->checkBasketContent($this->getSession()->getUser(), $this->getSession()->getBasket())) {
                $ecProcess->getProcessData()->initEmpty();
                $ecProcess->getProcessData()->save();
                $this->_iLastErrorNo = -334;
                $this->_sLastError = 'Warenkorb hat sich geändert';
                
                return false;
            }

            $blSuccess = $ecProcess->agree();
            if ($blSuccess) {
                $this->getSession()->deleteVariable('ec_rv');
            }
        }

        return $blSuccess;
    }
}
