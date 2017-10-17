<?php

namespace SenByte\EasyCredit\Application\Core;

use OxidEsales\Eshop\Core\Registry;
use SenByte\EasyCredit\Application\Core\EasyCreditProcess;
use EasyCredit\Log\Logger;
use EasyCredit\Http\Adapter\Curl;
use EasyCredit\Http\Request;
use EasyCredit\Api\ApiClient;

require_once __DIR__.'/../../lib/autoload.php';

class EasyCredit
{
    /**
     * @var $this
     */
    public static $instance;

    /**
     * @var OxidEasycreditProcess
     */
    protected $easycreditProcess;

    public function __construct()
    {
        $processData = new \EasyCredit\Transfer\ProcessData();

        $logLevel = OxidFacade::getShopConfVar('EASYCREDIT_LOG_LEVEL');
        $logger = new Logger(
            new \EasyCredit\Log\Handler\FileHandler(
                Registry::getConfig()->getConfigParam('sShopDir') . 'log/easyCredit.log'
            ),
            strtolower('debug')
        );

        $requestAdapter = new Curl();
        $requestAdapter->setLogger($logger);

        if (OxidFacade::getShopConfVar('EASYCREDIT_PROXY_STATUS') != '0') {
            $requestAdapter->setProxyEnabled(true);
            $requestAdapter->setProxyHost(OxidFacade::getShopConfVar('EASYCREDIT_PROXY_HOST'));
            $requestAdapter->setProxyPort(OxidFacade::getShopConfVar('EASYCREDIT_<PROXY_PORT'));

            if (trim(OxidFacade::getShopConfVar('EASYCREDIT_PROXY_USERNAME')) != ""
                && trim(OxidFacade::getShopConfVar('EASYCREDIT_PROXY_PASSWORD')) != ""
            ) {
                $requestAdapter->setProxyUsername(OxidFacade::getShopConfVar('EASYCREDIT_PROXY_USERNAME'));
                $requestAdapter->setProxyPassword(OxidFacade::getShopConfVar('EASYCREDIT_PROXY_PASSWORD'));
            }
        }

        $request = new Request(
            \EasyCredit\Config::EASYCREDIT_API_HOSTNAME, \EasyCredit\Config::EASYCREDIT_API_PORT, $requestAdapter
        );

        $apiClient = new ApiClient(
            OxidFacade::getShopConfVar('EASYCREDIT_SHOP_ID'),
            OxidFacade::getShopConfVar('EASYCREDIT_SHOP_TOKEN'),
            $request,
            new \EasyCredit\Api\DataMapper()
        );

        $this->easycreditProcess = EasyCreditProcess::createInstance($apiClient);
    }

    /**
     * @return Easycredit
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return \EasyCredit\Process\Process|OxidEasycreditProcess
     */
    public function getProcess()
    {
        return $this->easycreditProcess;
    }


}
