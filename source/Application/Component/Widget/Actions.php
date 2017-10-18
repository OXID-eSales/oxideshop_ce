<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Component\Widget;

/**
 * Actions widget.
 * Access actions in tpl.
 */
class Actions extends \OxidEsales\Eshop\Application\Component\Widget\WidgetController
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
            $artList = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
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
        $action   = oxNew(\OxidEsales\Eshop\Application\Model\Actions::class);
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
