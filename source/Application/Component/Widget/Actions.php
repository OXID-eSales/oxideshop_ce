<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Application\Component\Widget;

/**
 * Actions widget.
 * Access actions in tpl.
 */
class Actions extends \oxWidget
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'widget/product/action.tpl';

    /**
     * Are actions on
     *
     * @var bool
     */
    protected $_blLoadActions = null;

    /**
     * Returns article list with action articles
     *
     * @return object
     */
    public function getAction()
    {
        $actionId = $this->getViewParameter('action');
        if ($actionId && $this->_getLoadActionsParam()) {
            $artList = oxNew('oxarticlelist');
            $artList->loadActionArticles($actionId);
            if ($artList->count()) {
                return $artList;
            }
        }
    }

    /**
     * Returns if actions are ON
     *
     * @return string
     */
    protected function _getLoadActionsParam()
    {
        $this->_blLoadActions = $this->getConfig()->getConfigParam('bl_perfLoadAktion');

        return $this->_blLoadActions;
    }

    /**
     * Returns action name
     *
     * @return string
     */
    public function getActionName()
    {
        $actionId = $this->getViewParameter('action');
        $action   = oxNew('oxactions');
        if ($action->load($actionId)) {
            return $action->oxactions__oxtitle->value;
        }
    }

    /**
     * Returns products list type
     *
     * @return string
     */
    public function getListType()
    {
        return $this->getViewParameter('listtype');
    }
}
