<?php

namespace SenByte\EasyCredit\Application\Models;

use SenByte\EasyCredit\Application\Core\OxidFacade;
use SenByte\EasyCredit\Application\Core\EasyCredit;

class EasyCreditArticle extends EasyCreditArticle_parent
{

    /**
     * @var float|null
     */
    protected $bestRate;

    /**
     * @return string
     */
    public function getEasycreditBestRate()
    {
        if (null === $this->bestRate) {
            $amount = $this->_getPriceForView($this->getPrice());
            $modelCalculation = EasyCredit::getInstance()->getProcess()->getBestModelCalculation($amount);
            $this->bestRate = $modelCalculation->getAmountOfRate();
        }

        return OxidFacade::formatCurrency($this->bestRate);
    }

    /**
     * Depending on view mode prepare price for viewing
     *
     * @param oxPrice $oPrice price object
     * @return double
     */
    protected function _getPriceForView($oPrice)
    {
        if ($this->_isPriceViewModeNetto()) {
            $dPrice = $oPrice->getNettoPrice();
        } else {
            $dPrice = $oPrice->getBruttoPrice();
        }

        return $dPrice;
    }

    /**
     * Checks and return true if price view mode is netto
     * Differing from the parent, we do not allow customer
     * specific brutto/netto configuration.
     *
     * @return bool
     */
    protected function _isPriceViewModeNetto()
    {
        $blResult = (bool) OxidFacade::getShopConfVar('blShowNetPrice');
        return $blResult;
    }

    /**
     * @return string
     */
    public function getEasycreditExampleCalculationLink()
    {
        $amount = $this->_getPriceForView($this->getPrice());
        $shopId = OxidFacade::getShopConfVar('EASYCREDIT_SHOP_ID');

        return sprintf(\EasyCredit\Config::EXAMPLE_CALCULATION_LINK, $shopId, $amount);
    }

    /**
     * @return bool
     */
    public function isEasycreditFinanceable()
    {
        $this->getEasycreditBestRate();
        $amount = $this->_getPriceForView($this->getPrice());

        return (\EasyCredit\Config::isValidOrderAmount($amount) && $this->bestRate !== null);
    }
}
