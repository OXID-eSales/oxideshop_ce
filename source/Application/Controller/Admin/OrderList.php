<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

/**
 * Admin order list manager.
 * Performs collection and managing (such as filtering or deleting) function.
 * Admin Menu: Orders -> Display Orders.
 */
class OrderList extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
{
    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'oxorder';

    /**
     * Enable/disable sorting by DESC (SQL) (defaultfalse - disable).
     *
     * @var bool
     */
    protected $_blDesc = true;

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSortField = "oxorderdate";

    /**
     * Executes parent method parent::render() and returns name of template
     * file "order_list".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $folders = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('aOrderfolder');
        $folder = Registry::getRequest()->getRequestEscapedParameter("folder");
        // first display new orders
        if (!$folder && is_array($folders)) {
            $names = array_keys($folders);
            $folder = $names[0];
        }

        $search = ['oxorderarticles' => 'ARTID', 'oxpayments' => 'PAYMENT'];
        $searchQuery = Registry::getRequest()->getRequestEscapedParameter("addsearch");
        $searchField = Registry::getRequest()->getRequestEscapedParameter("addsearchfld");

        $this->_aViewData["folder"] = $folder ? $folder : -1;
        $this->_aViewData["addsearchfld"] = $searchField ? $searchField : -1;
        $this->_aViewData["asearch"] = $search;
        $this->_aViewData["addsearch"] = $searchQuery;
        $this->_aViewData["afolder"] = $folders;

        return "order_list";
    }

    /**
     * Cancels order and its order articles
     * Calls init() to reload list items after cancellation.
     */
    public function cancelOrder()
    {
        $order = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
        if ($order->load($this->getEditObjectId())) {
            $order->cancelOrder();
        }

        $this->resetContentCache();

        $this->init();
    }

    /**
     * Returns sorting fields array
     *
     * @return array
     */
    public function getListSorting()
    {
        $sorting = parent::getListSorting();
        if (isset($sorting["oxorder"]["oxbilllname"])) {
            $this->_blDesc = false;
        }

        return $sorting;
    }

    /**
     * Adding folder check
     *
     * @param array  $whereQuery SQL condition array
     * @param string $fullQuery  SQL query string
     *
     * @return string
     */
    protected function prepareWhereQuery($whereQuery, $fullQuery)
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $query = parent::prepareWhereQuery($whereQuery, $fullQuery);
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        $folders = $config->getConfigParam('aOrderfolder');
        $folder = Registry::getRequest()->getRequestEscapedParameter('folder');
        // Searching for empty oxfolder fields
        if ($folder && $folder != '-1') {
            $query .= " and ( oxorder.oxfolder = " . $database->quote($folder) . " )";
        } elseif (!$folder && is_array($folders)) {
            $folderNames = array_keys($folders);
            $query .= " and ( oxorder.oxfolder = " . $database->quote($folderNames[0]) . " )";
        }

        return $query;
    }

    /**
     * Builds and returns SQL query string. Adds additional order check.
     *
     * @param object $listObject list main object
     *
     * @return string
     */
    protected function buildSelectString($listObject = null)
    {
        $query = parent::buildSelectString($listObject);
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $searchQuery = Registry::getRequest()->getRequestEscapedParameter('addsearch');
        $searchQuery = trim($searchQuery);
        $searchField = Registry::getRequest()->getRequestEscapedParameter('addsearchfld');

        if ($searchQuery) {
            switch ($searchField) {
                case 'oxorderarticles':
                    $queryPart = "oxorder left join oxorderarticles on oxorderarticles.oxorderid=oxorder.oxid where ( oxorderarticles.oxartnum like " . $database->quote("%{$searchQuery}%") . " or oxorderarticles.oxtitle like " . $database->quote("%{$searchQuery}%") . " ) and ";
                    break;
                case 'oxpayments':
                    $queryPart = "oxorder left join oxpayments on oxpayments.oxid=oxorder.oxpaymenttype where oxpayments.oxdesc like " . $database->quote("%{$searchQuery}%") . " and ";
                    break;
                default:
                    $queryPart = "oxorder where oxorder.oxpaid like " . $database->quote("%{$searchQuery}%") . " and ";
                    break;
            }
            $query = str_replace('oxorder where', $queryPart, $query);
        }

        return $query;
    }
}
