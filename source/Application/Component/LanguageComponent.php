<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Component;

/**
 * Shop language manager.
 * Performs language manager function: changes template settings, modifies URL's.
 *
 * @subpackage oxcmp
 */
class LanguageComponent extends \OxidEsales\Eshop\Core\Controller\BaseController implements ViewDataKeyProviderInterface
{
    /**
     * Marking object as component
     *
     * @var bool
     */
    protected $_blIsComponent = true;

    public function getViewDataKey(): string
    {
        return 'oxcmp_lang';
    }

    /**
     * Executes parent::render() and returns array with languages.
     *
     * @return array $this->aLanguages languages
     */
    public function render()
    {
        parent::render();

        // Performance
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('bl_perfLoadLanguages')) {
            $aLanguages = \OxidEsales\Eshop\Core\Registry::getLang()->getLanguageArray(null, true, true);
            reset($aLanguages);
            foreach ($aLanguages as $oVal) {
                $oVal->link = \OxidEsales\Eshop\Core\Registry::getConfig()->getTopActiveView()->getLink($oVal->id);
            }

            return $aLanguages;
        }
    }
}
