<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Class manages article select lists sorting.
 */
class AttributeOrderAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    /**
     * Columns array.
     *
     * @var array
     */
    protected $_aColumns = [
        'container1' => [
            ['oxtitle', 'oxattribute', 1, 1, 0],
            ['oxsort', 'oxcategory2attribute', 1, 0, 0],
            ['oxid', 'oxcategory2attribute', 0, 0, 1],
        ],
    ];

    /**
     * Returns SQL query for data to fetc.
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getQuery" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getQuery()
    {
        $sSelTable = $this->_getViewName('oxattribute');
        $sArtId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');

        return " from $sSelTable left join oxcategory2attribute on oxcategory2attribute.oxattrid = $sSelTable.oxid " .
                 'where oxobjectid = ' . \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($sArtId) . ' ';
    }

    /**
     * Returns SQL query addon for sorting.
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getSorting" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getSorting()
    {
        return 'order by oxcategory2attribute.oxsort ';
    }

    /**
     * Applies sorting for selection lists.
     */
    public function setSorting(): void
    {
        $sSelId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');
        $sSelect = 'select * from oxcategory2attribute where oxobjectid = :oxobjectid order by oxsort';

        $oList = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
        $oList->init('oxbase', 'oxcategory2attribute');
        $oList->selectString($sSelect, [
            ':oxobjectid' => $sSelId,
        ]);

        // fixing indexes
        $iSelCnt = 0;
        $aIdx2Id = [];
        foreach ($oList as $sKey => $oSel) {
            if ($oSel->oxcategory2attribute__oxsort->value !== $iSelCnt) {
                $oSel->oxcategory2attribute__oxsort->setValue($iSelCnt);
                // saving new index
                $oSel->save();
            }
            $aIdx2Id[$iSelCnt] = $sKey;
            ++$iSelCnt;
        }

        if (false !== ($iKey = array_search(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('sortoxid'), $aIdx2Id, true))) {
            $iDir = 'up' === \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('direction') ? $iKey - 1 : $iKey + 1;
            if (isset($aIdx2Id[$iDir])) {
                // exchanging indexes
                $oDir1 = $oList->offsetGet($aIdx2Id[$iDir]);
                $oDir2 = $oList->offsetGet($aIdx2Id[$iKey]);

                $iCopy = $oDir1->oxcategory2attribute__oxsort->value;
                $oDir1->oxcategory2attribute__oxsort->setValue($oDir2->oxcategory2attribute__oxsort->value);
                $oDir2->oxcategory2attribute__oxsort->setValue($iCopy);
                $oDir1->save();
                $oDir2->save();
            }
        }

        $sQAdd = $this->_getQuery();

        $sQ = 'select ' . $this->_getQueryCols() . $sQAdd;
        $sCountQ = 'select count( * ) ' . $sQAdd;

        $this->_outputResponse($this->_getData($sCountQ, $sQ));
    }
}
