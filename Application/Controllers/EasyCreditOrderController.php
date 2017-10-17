<?php

namespace SenByte\EasyCredit\Application\Controllers;

/**
 * extends Oxid Order Controller
 * removes session variable when order page is rendered
 * 
 * @author info@senbyte.com
 * @copyright 2017 senByte UG
 */

class EasyCreditOrderController extends EasyCreditOrderController_parent
{

    /**
     * @return string
     */
    public function render()
    {
        $templateFile = parent::render();
        $this->getSession()->deleteVariable('ec_rv');

        return $templateFile;
    }
}
