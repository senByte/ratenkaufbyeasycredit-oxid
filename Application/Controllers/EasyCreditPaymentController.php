<?php

namespace SenByte\EasyCredit\Application\Controllers;

/**
 * @author info@senbyte.com
 * @copyright 2017 senByte UG
 */

use EasyCredit\Transfer\ProcessInitialize;
use EasyCredit\Config as ecConfig;
use EasyCredit\Transfer\CallbackUrls as ecCallbackUrls;
use EasyCredit\Transfer\ProcessData;
use EasyCredit\Transfer\TechnicalShopParams;
use SenByte\EasyCredit\Application\Core\EasyCredit;
use SenByte\EasyCredit\Application\Core\OxidFacade;

class EasyCreditPaymentController extends EasyCreditPaymentController_parent
{

    /**
     * @var Easycredit
     */
    protected $ec;

    /**
     * @var \EasyCredit\Process\Process|OxidEasycreditProcess
     */
    protected $ecProcess;

    /**
     * @var array
     */
    protected $paymentList;

    /**
     * @var double|null
     */
    protected $bestRate;

    /**
     * @var array|null
     */
    protected $dynValues;

    public function __construct()
    {
        $this->ec = EasyCredit::getInstance();
        $this->ecProcess = $this->ec->getProcess();

        if ($this->ecProcess->getProcessData()->getStatus() === \EasyCredit\Process\Status::CONFIRMED) {
            $this->ecProcess->getProcessData()->initEmpty();
        }

        $messages = $this->ecProcess->getProcessData()->getMessages();

        unset($messages['ERROR_ADDRESS_UNEQUAL'], $messages['ERROR_ADDRESS_PACKSTATION'], $messages['ERROR_ADDRESS_NOT_IN_GERMANY']);

        $this->ecProcess->getProcessData()->setMessages($messages);

        $this->ecProcess->getProcessData()->setCurrentStep(\EasyCredit\Transfer\ProcessData::STEP_PAYMENT);
    }

    /**
     * @return mixed
     */
    public function render()
    {
        return parent::render();
    }

    /**
     * @return array
     */
    public function getPaymentList()
    {
        $this->paymentList = parent::getPaymentList();

        $this->checkPaymentRequirements();

        if (isset($this->paymentList['easycredit'])) {
            $this->paymentList['easycredit']->setDynValues($this->getEasycreditDynValues());
        }

        return $this->paymentList;
    }

    /**
     * @return array|null
     */
    public function getEasycreditDynValues()
    {
        if ($this->dynValues === null) {
            $this->dynValues = array();
            if (\EasyCredit\Config::isValidOrderAmount($this->getOrderTotal())) {

                $modelCalculation = $this->ecProcess->getModelCalculation();
                $status = $this->ecProcess->getProcessData()->getStatus();
    
                $consent = $this->ecProcess->getLegislativeText()->getDataProcessingPaymentPage();
                
                $this->dynValues['is_active'] = true;
                $this->dynValues['declined'] = ($status === \EasyCredit\Process\Status::DECLINED);
                $this->dynValues['consent'] = $consent;
    
                $this->dynValues['installmentPlans'] = array_reverse($modelCalculation->getResults()->toArray());
                $this->dynValues['representativeExample'] = $modelCalculation->getRepresentativeExample();
    
                $this->dynValues['bestRate'] = OxidFacade::formatCurrency($this->bestRate);
                $this->dynValues['order_amount'] = $this->getOrderTotal();
                $this->dynValues['show_mobilephone_verify'] = false;
    
                $this->dynValues['errors'] = $this->ecProcess->getProcessData()->getMessages();
                if (isset($this->dynValues['errors']['MobilfunknummerValidierenActivity.Errors.KEINE_MOBILFUNKNUMMER'])) {
                    $this->dynValues['show_mobilephone_verify'] = true;
                }
            }
        }

        $this->fillDynValuesFromProcessData();

        return $this->dynValues;
    }

    protected function fillDynValuesFromProcessData()
    {
        $data = array();

        $processData = $this->ecProcess->getProcessData();

        $data['consent'] = $processData->getAgreement()->getDataProcessing();
        $data['installment'] = $processData->getTerm();

        if ($processData->getCustomer()->getPersonData()->getBirthDate() instanceof \DateTime) {
            $birthdate = $processData->getCustomer()->getPersonData()->getBirthDate();
            $data['birthdate'] = array(
                'year' => $birthdate->format('Y'),
                'month' => $birthdate->format('m'),
                'day' => $birthdate->format('d'),
            );
        }

        $data['mobile_phone'] = $processData->getCustomer()->getContact()->getMobilphone();
        $data['mobilephone_verify'] = $processData->getCustomer()->getContact()->getMobilphoneVerify();

        $this->dynValues['easycredit'] = $data;
    }

    /**
     * @return mixed
     */
    public function validatePayment()
    {
        $oSession      = $this->getSession();
        $ecReturnValue = OxidFacade::getRequestParameter('ec_rv');
        
        if (!($sPaymentId = OxidFacade::getRequestParameter('paymentid'))) {
            $sPaymentId = $oSession->getVariable('paymentid');
        }
        
        if($sPaymentId == 'easycredit' && !$ecReturnValue) {
            $ecProcess = EasyCredit::getInstance()->getProcess();
            
            if(null != $ecProcess->getProcessData()->getTbaId()) {
                $term = $ecProcess->getProcessData()->getTerm();
                $ecProcess->getProcessData()->initEmpty();
                $ecProcess->getProcessData()->setTerm($term);
                $ecProcess->getProcessData()->save();
            }
            
            $ecProcess->prefillProcessData(
                $ecProcess->getProcessData(),
                $this->getSession()->getUser(),
                $this->getSession()->getBasket()
                );
            $ecProcess->getProcessData()->save();
            
            $callbackUrls = new ecCallbackUrls();
            $callbackUrls->setUrlSucceeded(OxidFacade::getShopUrl() . 'index.php?cl=payment&paymentid=easycredit&fnc=validatePayment&ec_rv=ack');
            $callbackUrls->setUrlCancelled(OxidFacade::getShopUrl() . 'index.php?cl=payment');
            $callbackUrls->setUrlDenied(OxidFacade::getShopUrl() . 'index.php?cl=payment&paymentid=easycredit&fnc=validatePayment&ec_rv=dec');
            $ecProcess->getProcessData()->setCallbackUrls($callbackUrls);

            $shopParams = new TechnicalShopParams();
            $shopParams->setShopPlatformManufacturer('Oxid eSales - ' . OxidFacade::getVersion());
            $shopParams->setShopPlatformModuleVersion('2.0.0');
            
            $initialized = $ecProcess->initialize(ProcessInitialize::INTEGRATION_TYPE_PAYMENT_PAGE, $shopParams);
            
            if ($initialized) {
                $redirect = sprintf(ecConfig::PAYMENT_PAGE_URL, $ecProcess->getProcessData()->getTbaId());
                header('Location: '.$redirect);
            } else {
                $oSession->setVariable('payerror', -334);
                $oSession->deleteVariable('paymentid');
                $oSession->setVariable('_selected_paymentid', $sPaymentId);
                
                return;
            }
        } else {
            return parent::validatePayment();
        }
    }
    
    protected function checkPaymentRequirements()
    {
        $orderTotal = $this->getOrderTotal();

        if ($this->bestRate === null) {
            $modelCalculation = $this->ecProcess->getBestModelCalculation();
            $this->bestRate = $modelCalculation->getAmountOfRate();
        }

        if (isset($this->paymentList['easycredit'])
            && (!\EasyCredit\Config::isValidOrderAmount($orderTotal)
                || $this->bestRate === null
                || !$this->doesBillingMatchDelivery()
                || !$this->isValidCountryCode()
            )
        ) {
            unset($this->paymentList['easycredit']);
        }
    }

    /**
     * @return string
     */
    protected function getOrderTotal()
    {
        $oxBasket = $this->getSession()->getBasket();
        $oxBasket->calculateBasket(true);
        $fTotalSum = OxidFacade::fRound($oxBasket->getPrice()->getBruttoPrice());

        return $fTotalSum;
    }
    
    /**
     * @return boolean
     */
    protected function doesBillingMatchDelivery() {
        $ecProcess      = EasyCredit::getInstance()->getProcess();
        $billingAdress  = $ecProcess->getBillingAddress($this->getSession()->getUser());
        $deliverAddress = $ecProcess->getDeliveryAddress($this->getSession()->getUser());
        
        if ($billingAdress->getStreet() != $deliverAddress->getStreet()
            || $billingAdress->getAddressAdditional() != $deliverAddress->getAddressAdditional()
            || $billingAdress->getZip() != $deliverAddress->getZip()
            || $billingAdress->getCity() != $deliverAddress->getCity()
            || $billingAdress->getCountryCode() != $deliverAddress->getCountryCode()) {
                return false;
        } else {
            return true; 
        }
    }
    
    /**
     * @return boolean
     */
    protected function isValidCountryCode() {
        $ecProcess      = EasyCredit::getInstance()->getProcess();
        $billingAdress  = $ecProcess->getBillingAddress($this->getSession()->getUser());
        
        if ($billingAdress->getCountryCode() == 'DE') {
            return true;
        } else {
            return false;
        }
    }
}
