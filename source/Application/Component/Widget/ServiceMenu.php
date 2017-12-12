<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Component\Widget;

/**
 * Recomendation list.
 * Forms recomendation list.
 */
class ServiceMenu extends \OxidEsales\Eshop\Application\Component\Widget\WidgetController
{
    /**
     * Names of components (classes) that are initiated and executed
     * before any other regular operation.
     * User component used in template.
     *
     * @var array
     */
    protected $_aComponentNames = ['oxcmp_user' => 1];

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'widget/header/servicemenu.tpl';

    /**
     * Template variable getter. Returns article list count in comparison.
     *
     * @deprecated since v6.1.0 (2017-12-12); Use OxidEsales\Eshop\Application\Controller\FrontendController::getCompareItemsCnt().
     *
     * @return integer
     */
    public function getCompareItemsCnt()
    {
        return parent::getCompareItemsCnt();
    }

    /**
     * Template variable getter. Returns comparison article list.
     *
     * @param bool $blJson return json encoded array
     *
     * @return array
     */
    public function getCompareItems($blJson = false)
    {
        $oCompare = oxNew(\OxidEsales\Eshop\Application\Controller\CompareController::class);
        $aCompareItems = $oCompare->getCompareItems();

        if ($blJson) {
            $aCompareItems = json_encode($aCompareItems);
        }

        return $aCompareItems;
    }
}
