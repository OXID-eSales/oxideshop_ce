<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\TableViewNameGenerator;

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
    protected $_sThisTemplate = 'actions_list';

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
     * Calls parent::render() and returns name of template to render
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        // passing display type back to view
        $this->_aViewData["displaytype"] = Registry::getRequest()->getRequestEscapedParameter("displaytype");

        return $this->_sThisTemplate;
    }

    /**
     * Adds active promotion check
     *
     * @param array  $aWhere  SQL condition array
     * @param string $sqlFull SQL query string
     *
     * @return $sQ
     */
    protected function prepareWhereQuery($aWhere, $sqlFull)
    {
        $sQ = parent::prepareWhereQuery($aWhere, $sqlFull);
        $sDisplayType = (int) Registry::getRequest()->getRequestEscapedParameter('displaytype');
        $tableViewNameGenerator = new TableViewNameGenerator();
        $sTable = $tableViewNameGenerator->getViewName("oxactions");

        // searching for empty oxfolder fields
        if ($sDisplayType) {
            $sNow = date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime());

            switch ($sDisplayType) {
                case 1: // active
                    $sQ .= " and {$sTable}.oxactivefrom < '{$sNow}' and {$sTable}.oxactiveto > '{$sNow}' ";
                    break;
                case 2: // upcoming
                    $sQ .= " and {$sTable}.oxactivefrom > '{$sNow}' ";
                    break;
                case 3: // expired
                    $sQ .= " and {$sTable}.oxactiveto < '{$sNow}' and {$sTable}.oxactiveto != '0000-00-00 00:00:00' ";
                    break;
            }
        }

        return $sQ;
    }
}
