<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin actionss manager.
 * Sets list template, list object class ('oxactions') and default sorting
 * field ('oxactions.oxtitle').
 * Admin Menu: Manage Products -> Actions.
 */
class ActionsList extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'actions_list.tpl';

    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'oxactions';

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSortField = 'oxtitle';

    /**
     * Calls parent::render() and returns name of template to render.
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        // passing display type back to view
        $this->_aViewData['displaytype'] = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('displaytype');

        return $this->_sThisTemplate;
    }

    /**
     * Adds active promotion check.
     *
     * @param array  $aWhere  SQL condition array
     * @param string $sqlFull SQL query string
     *
     * @return
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "prepareWhereQuery" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _prepareWhereQuery($aWhere, $sqlFull)
    {
        $sQ = parent::_prepareWhereQuery($aWhere, $sqlFull);
        $sDisplayType = (int)\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('displaytype');
        $sTable = getViewName('oxactions');

        // searching for empty oxfolder fields
        if ($sDisplayType) {
            $sNow = date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime());

            switch ($sDisplayType) {
                // active
                case 1:
                    $sQ .= " and {$sTable}.oxactivefrom < '{$sNow}' and {$sTable}.oxactiveto > '{$sNow}' ";
                    break;
                // upcoming
                case 2:
                    $sQ .= " and {$sTable}.oxactivefrom > '{$sNow}' ";
                    break;
                // expired
                case 3:
                    $sQ .= " and {$sTable}.oxactiveto < '{$sNow}' and {$sTable}.oxactiveto != '0000-00-00 00:00:00' ";
                    break;
            }
        }

        return $sQ;
    }
}
