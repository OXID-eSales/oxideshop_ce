<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Component;

use OxidEsales\Eshop\Core\Registry;

/**
 * Translarent shop manager (executed automatically), sets
 * registration information and current shop object.
 *
 * @subpackage oxcmp
 */
class ShopComponent extends \OxidEsales\Eshop\Core\Controller\BaseController implements ViewDataKeyProviderInterface
{
    /**
     * Marking object as component
     *
     * @var bool
     */
    protected $_blIsComponent = true;

    public function getViewDataKey(): string
    {
        return 'oxcmp_shop';
    }

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
            // redirect to offline if there is no active shop
            Registry::getUtils()->showOfflinePage();
        }

        return $oShop;
    }
}
