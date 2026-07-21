<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Component;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Http\OfflinePageResponse;
use OxidEsales\EshopCommunity\Internal\Framework\Http\ResponseReadyEvent;
use oxRegistry;

/**
 * Translarent shop manager (executed automatically), sets
 * registration information and current shop object.
 *
 * @subpackage oxcmp
 */
class ShopComponent extends \OxidEsales\Eshop\Core\Controller\BaseController
{
    /**
     * Marking object as component
     *
     * @var bool
     */
    protected $_blIsComponent = true;

    /**
     * Executes parent::render() and returns active shop object.
     *
     * @return  object  $this->oActShop active shop object
     */
    public function render()
    {
        parent::render();

        $myConfig = Registry::getConfig();

        // is shop active?
        $oShop = $myConfig->getActiveShop();
        $sActiveField = 'oxshops__oxactive';
        $sClassName = $myConfig->getActiveView()->getClassKey();

        if (!$oShop->$sActiveField->value && 'oxstart' != $sClassName && !$this->isAdmin()) {
            ContainerFacade::dispatch(new ResponseReadyEvent(new OfflinePageResponse()));
        }

        return $oShop;
    }
}
