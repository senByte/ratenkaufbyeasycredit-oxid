<?php

namespace SenByte\EasyCredit\Application\Controllers;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use SenByte\EasyCredit\Application\Core\EasyCredit;
use SenByte\EasyCredit\Application\Core\OxidFacade;

/**
 * @author info@senbyte.com
 * @copyright 2017 senByte UG
 */

class EasyCreditUpdate extends FrontendController
{

    /**
     * @return string
     */
    public function render()
    {
        parent::render();
        $this->getConfig()->setConfigParam( 'iDebug', 0 );
        
        $easycreditInstance = EasyCredit::getInstance();
        $process = $easycreditInstance->getProcess();

        $data = array(
            'installment' => OxidFacade::getRequestParameter('installment'),
            'consent' => OxidFacade::getRequestParameter('consent'),
        );

        $process->updateProcessData($data);
        
        header("Content-type: application/json");
        
        echo json_encode($data);        
        exit();
    }
}
