<?php

namespace SenByte\EasyCredit\Application\Core;

use SenByte\EasyCredit\Application\Core\EasyCreditProcess;

/**
 * @author info@senbyte.com
 * @copyright 2017 senByte UG
 */

class EasyCreditViewConfig extends EasyCreditViewConfig_parent
{
    protected $_isEasycreditEnabled = null;

    /**
     * @return string
     */
    public function getEasycreditLogo()
    {
        return "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAG8AAAAYCAYAAAD04qMZAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA/9pVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTM4IDc5LjE1OTgyNCwgMjAxNi8wOS8xNC0wMTowOTowMSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgeG1wTU06T3JpZ2luYWxEb2N1bWVudElEPSJ1dWlkOjlFM0U1QzlBOEM4MURCMTE4NzM0REI1OEZEREU0QkE3IiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjAwQjk1QkRGREVGMjExRTY5RDkwODdDM0EyRUIwQ0RCIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjAwQjk1QkRFREVGMjExRTY5RDkwODdDM0EyRUIwQ0RCIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIElsbHVzdHJhdG9yIENDIDIwMTcgKE1hY2ludG9zaCkiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo2NzUzYTk0NS1jNGRkLTQ2NmItOWMyMy00MTZiYmEzNzgzY2IiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5k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";
    }

    /**
     * @return bool
     */
    public function isEasycreditEnabled()
    {
        if ($this->_isEasycreditEnabled === null) {
            $module = oxNew('oxModule');
            $module->load('easycredit');
            if ($module->isActive()) {
                $this->_isEasycreditEnabled = true;
            } else {
                $this->_isEasycreditEnabled = false;
            }
        }

        return $this->_isEasycreditEnabled;
    }

    /**
     * @return null|string
     */
    public function getRepaymentPlanText()
    {
        try {
            return EasyCreditProcess::getInstance()->getFinancingDetails()->getRepaymentPlanText();
        } catch (\Exception $e) {
        }

        return null;
    }

    /**
     * @return null|string
     */
    public function getTechnicalTbaId()
    {
        try {
            return EasyCreditProcess::getInstance()->getProcessData()->getTechnicalTbaId();
        } catch (\Exception $e) {
        }

        return null;
    }

    /**
     * @return null|string
     */
    public function getTbaId()
    {
        try {
            return EasyCreditProcess::getInstance()->getProcessData()->getTbaId();
        } catch (\Exception $e) {
        }

        return null;
    }
    
    /**
     *
     * @return string|NULL
     */
    public function getContractURL() {
        try {
            return OxidEasycreditProcess::getInstance()->getCommonProcessData()->getCommonProcessData()->getContractInfoURL();
        } catch (\Exception $e) {
        }
    
        return null;
    }

}
