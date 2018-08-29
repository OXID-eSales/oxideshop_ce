<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Component;

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

        $myConfig = $this->getConfig();

        // is shop active?
        $oShop = $myConfig->getActiveShop();
        $sActiveField = 'oxshops__oxactive';
        $sClassName = $myConfig->getActiveView()->getClassName();

        if (!$oShop->$sActiveField->value && 'oxstart' != $sClassName && !$this->isAdmin()) {
            // redirect to offline if there is no active shop
            \OxidEsales\Eshop\Core\Registry::getUtils()->redirectOffline();
        }

        return $oShop;
    }
}
