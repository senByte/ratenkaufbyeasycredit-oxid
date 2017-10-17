<?php

namespace SenByte\EasyCredit\Application\Core;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Application\Model\Basket;
use EasyCredit\Transfer\ProcessData;

class EasyCreditProcess extends \EasyCredit\Process\Process
{
    /**
     * @var SessionHandler
     */
    protected $saveHandler;

    /**
     * @param \EasyCredit\Api\ApiClient $apiClient
     * @param ProcessData $processData
     */
    public function __construct(\EasyCredit\Api\ApiClient $apiClient, $processData)
    {
        $this->saveHandler = new \EasyCredit\SaveHandler\SessionHandler();
        parent::__construct($apiClient, $processData);
    }

    /**
     * @return string
     */
    public function getOrderTotal()
    {
        $oxBasket = OxidFacade::getBasket();
        $fTotalSum = OxidFacade::fRound($oxBasket->getPrice()->getBruttoPrice());

        return number_format($fTotalSum, 2, '.', '');
    }

    /**
     * @param User $oxUser
     * @param Basket $oxBasket
     * @return boolean
     */
    public function checkBasketContent(User $oxUser, Basket $oxBasket)
    {
        $processDataCheck = clone $this->processData;

        $this->prefillProcessData($this->processData, $oxUser, $oxBasket);
        
        return ($processDataCheck->generateHash() === $this->getProcessData()->generateHash());
    }

    /**
     * @param \EasyCredit\Transfer\ProcessData $processData
     * @param User $oxUser
     * @param Basket $oxBasket
     */
    public function prefillProcessData(
        \EasyCredit\Transfer\ProcessData $processData,
        User $oxUser,
        Basket $oxBasket
    ) {
        if ($oxUser->oxuser__oxsal->value == "MR") {
            $salutation = \EasyCredit\Transfer\PersonData::SALUTATION_MR;
        } else {
            $salutation = \EasyCredit\Transfer\PersonData::SALUTATION_MRS;
        }

        $personData = $processData->getCustomer()->getPersonData();
        $personData->setFirstName(
                $oxUser->oxuser__oxfname->value
            );
        $personData->setLastName(
                $oxUser->oxuser__oxlname->value
            );
        $personData->setSalutation($salutation);
        
        if ($this->verifyDate($oxUser->oxuser__oxbirthdate->value)) {
            $personData->setBirthDate(new \DateTime($oxUser->oxuser__oxbirthdate->value));
        }

        $riskRelatedInfo = new \EasyCredit\Transfer\RiskRelatedInfo();
        $riskRelatedInfo->setNegativePaymentInformation(
            \EasyCredit\Transfer\RiskRelatedInfo::NEGATIVE_PAYMENT_INFORMATION_NO_INFORMATION
        );
        $riskRelatedInfo->setOrderCount(count($oxUser->getOrders()));

        if (strstr($oxUser->oxuser__oxregister->value, '0000-00-00')) {
            $riskRelatedInfo->setCustomerRegistrated(false);
        } else {
            $riskRelatedInfo->setCustomerRegistrated(true);
            $customerRegistratedAt = new \DateTime($oxUser->oxuser__oxregister->value);
            if ($customerRegistratedAt->diff(new \DateTime())->y < 100) {
                $riskRelatedInfo->setCustomerRegistrationDate($customerRegistratedAt);
            }
        }
        $riskRelatedInfo->setRiskItemInCart(false);

        $cartInfos = new \EasyCredit\Transfer\CartInfoCollection();
        
        foreach ($oxBasket->getContents() as $product) {
            $cartInfo = new \EasyCredit\Transfer\CartInfo();
            $cartInfo->setName(
                    $product->getTitle()
                );
            $cartInfo->setQuantity($product->getAmount());
            $cartInfo->setPrice($product->getUnitPrice()->getBruttoPrice());

            if ($product->getArticle()->getCategory()) {
                $cartInfo->setCategory(
                    $product->getArticle()->getCategory()->oxcategories__oxtitle->value
                    );
            }

            if ($product->getArticle()->oxarticles__oxean !== null
                && !empty($product->getArticle()->oxarticles__oxean->value)
            ) {
                $articleIdCollection = new \EasyCredit\Transfer\ArticleIdCollection();

                $articleId = new \EasyCredit\Transfer\ArticleId();
                $articleId->setId($product->getArticle()->oxarticles__oxean->value);
                $articleId->setType(\EasyCredit\Transfer\ArticleId::TYPE_EAN);

                $articleIdCollection->addItem($articleId);
                $cartInfo->setArticleId($articleIdCollection);
            }
            $cartInfos->addItem($cartInfo);
        }
        $riskRelatedInfo->setCartItemsCount($oxBasket->getItemsCount());

        $customer = $processData->getCustomer();
        $customer->setPersonData($personData);
        $customer->getContact()->setEmail(
                $oxUser->oxuser__oxusername->value
            );

        $processData->setProducts($cartInfos);
        $processData->setRiskInfo($riskRelatedInfo);
        $processData->setBillingAddress($this->getBillingAddress($oxUser));
        $processData->setDeliveryAddress($this->getDeliveryAddress($oxUser));
        $processData->setCustomer($customer);
        $processData->setOrderTotal($oxBasket->getPrice()->getBruttoPrice());
    }

    /**
     * @param User $oxUser
     * @return \EasyCredit\Transfer\BillingAddress
     */
    public function getBillingAddress(User $oxUser)
    {
        $country = oxNew('oxcountry');
        $country->load($oxUser->oxuser__oxcountryid->value);

        $billingAddress = new \EasyCredit\Transfer\BillingAddress();
        $billingAddress->setStreet(
                $oxUser->oxuser__oxstreet->value.' '.$oxUser->oxuser__oxstreetnr->value
            );
        $billingAddress->setCity(
                $oxUser->oxuser__oxcity->value
            );
        $billingAddress->setZip(
                $oxUser->oxuser__oxzip->value
            );
        $billingAddress->setCountryCode(
                $country->oxcountry__oxisoalpha2->value
            );

        return $billingAddress;
    }

    /**
     * @return \EasyCredit\Transfer\DeliveryAddress
     */
    public function getDeliveryAddress(User $oxUser)
    {
        
        if ($oxUser->getSelectedAddressId() === null) {
            $country = oxNew('oxcountry');
            $country->load($oxUser->oxuser__oxcountryid->value);

            $deliveryAddress = new \EasyCredit\Transfer\DeliveryAddress();
            $deliveryAddress->setStreet(
                    $oxUser->oxuser__oxstreet->value.' '.$oxUser->oxuser__oxstreetnr->value
                );
            $deliveryAddress->setCity(
                    $oxUser->oxuser__oxcity->value
                );
            $deliveryAddress->setZip(
                    $oxUser->oxuser__oxzip->value
                );
            $deliveryAddress->setCountryCode(
                    $country->oxcountry__oxisoalpha2->value
                );
            $deliveryAddress->setFirstName(
                    $oxUser->oxuser__oxfname->value
                );
            $deliveryAddress->setLastName(
                    $oxUser->oxuser__oxlname->value
                );

            return $deliveryAddress;
        }
        $address = oxNew('oxaddress');
        $address->load($oxUser->getSelectedAddressId());
        
        $country = oxNew('oxcountry');
        $country->load($address->oxaddress__oxcountryid->value);

        $deliveryAddress = new \EasyCredit\Transfer\DeliveryAddress();
        $deliveryAddress->setStreet(
                $address->oxaddress__oxstreet->value.' '.$address->oxaddress__oxstreetnr->value
            );
        $deliveryAddress->setCity(
                $address->oxaddress__oxcity->value
            );
        $deliveryAddress->setZip(
                $address->oxaddress__oxzip->value
            );
        $deliveryAddress->setCountryCode(
                $country->oxcountry__oxisoalpha2->value
            );
        $deliveryAddress->setFirstName(
                $oxUser->oxuser__oxfname->value
            );
        $deliveryAddress->setLastName(
                $oxUser->oxuser__oxlname->value
            );

        return $deliveryAddress;
    }

    /**
     * @param array $data
     */
    public function updateProcessData(array $data)
    {
        if (isset($data['consent'])) {
            $this->processData->getAgreement()->setDataProcessing(($data['consent'] == '1'));
        }

        if (isset($data['installment'])) {
            $this->processData->setTerm((int)$data['installment']);
        }

        $this->processData->save();
        
        return $data;
    }

    /**
     * @param float|null $amount
     * @return \EasyCredit\Transfer\ModelCalculation
     */
    public function getModelCalculation($amount = null)
    {
        if ($amount === null) {
            $amount = $this->getOrderTotal();
        }

        return parent::getModelCalculation($amount);
    }

    /**
     * @param float|null $amount
     * @return \EasyCredit\Transfer\ModelCalculationShort
     */
    public function getBestModelCalculation($amount = null)
    {
        if ($amount === null) {
            $amount = $this->getOrderTotal();
        }

        return parent::getBestModelCalculation($amount);
    }
    
    /**
     * @param string $date
     * @return boolean
     */
    protected function verifyDate($date)
    {
        $dateTime = \DateTime::createFromFormat('Y-m-d', $date);
        $errors = \DateTime::getLastErrors();
        if (!empty($errors['warning_count'])) {
            return false;
        }
        return $dateTime !== false;
    }
    
}
