<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

/**
 * Class manages manufacturer assignment to articles
 */
class ManufacturerMainAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    /**
     * If true extended column selection will be build
     *
     * @var bool
     */
    protected $_blAllowExtColumns = true;

    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = [
        // field , table, visible, multilanguage, id
        'container1' => [
            ['oxartnum', 'oxarticles', 1, 0, 0],
            ['oxtitle', 'oxarticles', 1, 1, 0],
            ['oxean', 'oxarticles', 1, 0, 0],
            ['oxmpn', 'oxarticles', 0, 0, 0],
            ['oxprice', 'oxarticles', 0, 0, 0],
            ['oxstock', 'oxarticles', 0, 0, 0],
            ['oxid', 'oxarticles', 0, 0, 1]
        ],
        'container2' => [
            ['oxartnum', 'oxarticles', 1, 0, 0],
            ['oxtitle', 'oxarticles', 1, 1, 0],
            ['oxean', 'oxarticles', 1, 0, 0],
            ['oxmpn', 'oxarticles', 0, 0, 0],
            ['oxprice', 'oxarticles', 0, 0, 0],
            ['oxstock', 'oxarticles', 0, 0, 0],
            ['oxid', 'oxarticles', 0, 0, 1]
        ]
    ];

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function getQuery() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();

        // looking for table/view
        $articlesViewName = $this->getViewName('oxarticles');
        $objectToCategoryViewName = $this->getViewName('oxobject2category');
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $manufacturerId = Registry::getRequest()->getRequestEscapedParameter('oxid');
        $syncedManufacturerId = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

        // Manufacturer selected or not ?
        if (!$manufacturerId) {
            // performance
            $query = ' from ' . $articlesViewName . ' where ' . $articlesViewName . '.oxshopid="' . $config->getShopId() . '" and 1 ';
            $query .= $config->getConfigParam('blVariantsSelection') ? '' : " and $articlesViewName.oxparentid = '' and $articlesViewName.oxmanufacturerid != " . $database->quote($syncedManufacturerId);
        } elseif ($syncedManufacturerId && $syncedManufacturerId != $manufacturerId) {
            // selected category ?
            $query = " from $objectToCategoryViewName left join $articlesViewName on ";
            $query .= $config->getConfigParam('blVariantsSelection') ? " ( $articlesViewName.oxid = $objectToCategoryViewName.oxobjectid or $articlesViewName.oxparentid = $objectToCategoryViewName.oxobjectid )" : " $articlesViewName.oxid = $objectToCategoryViewName.oxobjectid ";
            $query .= 'where ' . $articlesViewName . '.oxshopid="' . $config->getShopId() . '" and ' . $objectToCategoryViewName . '.oxcatnid = ' . $database->quote($manufacturerId) . ' and ' . $articlesViewName . '.oxmanufacturerid != ' . $database->quote($syncedManufacturerId);
            $query .= $config->getConfigParam('blVariantsSelection') ? '' : " and $articlesViewName.oxparentid = '' ";
        } else {
            $query = " from $articlesViewName where $articlesViewName.oxmanufacturerid = " . $database->quote($manufacturerId);
            $query .= $config->getConfigParam('blVariantsSelection') ? '' : " and $articlesViewName.oxparentid = '' ";
        }

        return $query;
    }

    /**
     * Adds filter SQL to current query
     *
     * @param string $query query to add filter condition
     *
     * @return string
     */
    protected function addFilter($query) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        $articleViewName = $this->getViewName('oxarticles');
        $query = parent::addFilter($query);

        // display variants or not ?
        $query .= $config->getConfigParam('blVariantsSelection') ? ' group by ' . $articleViewName . '.oxid ' : '';

        return $query;
    }

    /**
     * Removes article from Manufacturer config
     */
    public function removeManufacturer()
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        $articleIds = $this->getActionIds('oxarticles.oxid');
        $manufacturerId = Registry::getRequest()->getRequestEscapedParameter('oxid');

        if (Registry::getRequest()->getRequestEscapedParameter("all")) {
            $articleViewTable = $this->getViewName('oxarticles');
            $articleIds = $this->getAll($this->addFilter("select $articleViewTable.oxid " . $this->getQuery()));
        }

        if (is_array($articleIds) && !empty($articleIds)) {
            $query = $this->formManufacturerRemovalQuery($articleIds);
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($query);

            $this->resetCounter("manufacturerArticle", $manufacturerId);
        }
    }

    /**
     * Forms and returns query for manufacturers removal.
     *
     * @param array $articlesToRemove Ids of manufacturers which should be removed.
     *
     * @return string
     */
    protected function formManufacturerRemovalQuery($articlesToRemove)
    {
        return "
          UPDATE oxarticles
          SET oxmanufacturerid = null
          WHERE oxid IN ( " . implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($articlesToRemove)) . ") ";
    }

    /**
     * Adds article to Manufacturer config
     */
    public function addManufacturer()
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();

        $articleIds = $this->getActionIds('oxarticles.oxid');
        $manufacturerId = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

        if (Registry::getRequest()->getRequestEscapedParameter('all')) {
            $articleViewName = $this->getViewName('oxarticles');
            $articleIds = $this->getAll($this->addFilter("select $articleViewName.oxid " . $this->getQuery()));
        }

        if ($manufacturerId && $manufacturerId != "-1" && is_array($articleIds)) {
            $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

            $query = $this->formArticleToManufacturerAdditionQuery($manufacturerId, $articleIds);
            $database->execute($query);
            $this->resetCounter("manufacturerArticle", $manufacturerId);
        }
    }

    /**
     * Forms and returns query for articles addition to manufacturer.
     *
     * @param string $manufacturerId Manufacturer id.
     * @param array  $articlesToAdd  Array of article ids to be added to manufacturer.
     *
     * @return string
     */
    protected function formArticleToManufacturerAdditionQuery($manufacturerId, $articlesToAdd)
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        return "
            UPDATE oxarticles
            SET oxmanufacturerid = " . $database->quote($manufacturerId) . "
            WHERE oxid IN ( " . implode(", ", $database->quoteArray($articlesToAdd)) . " )";
    }
}
