<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\ListModel;
use OxidEsales\Eshop\Core\Registry;

/**
 * Class controls article assignment to accessories
 */
class ArticleAccessoriesAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    /**
     * If true extended column selection will be build
     *
     * @var bool
     */
    protected $_blAllowExtColumns = true;

    /**
     * Container ID
     *
     * @var string
     */
    private $containerId;

    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = ['container1' => [ // field , table,         visible, multilanguage, ident
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
                                ['oxsort', 'oxaccessoire2article', 1, 1, 0],
                                ['oxean', 'oxarticles', 1, 0, 0],
                                ['oxmpn', 'oxarticles', 0, 0, 0],
                                ['oxprice', 'oxarticles', 0, 0, 0],
                                ['oxstock', 'oxarticles', 0, 0, 0],
                                ['oxid', 'oxaccessoire2article', 0, 0, 1]
                            ]
    ];

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     * @throws DatabaseConnectionException
     */
    protected function _getQuery()
    {
        $myConfig = Registry::getConfig();
        $oxidId = Registry::getConfig()->getRequestEscapedParameter('oxid');
        $synchId = Registry::getConfig()->getRequestEscapedParameter('synchoxid');
        $db = DatabaseProvider::getDb();
        $this->containerId = Registry::getConfig()->getRequestEscapedParameter('cmpid');

        $articleTable = $this->_getViewName('oxarticles');
        $object2categoryTable = $this->_getViewName('oxobject2category');

        // category selected or not ?
        if (!$oxidId) {
            $outputQuery = " from {$articleTable} where 1 ";
            $outputQuery .= $myConfig->getConfigParam('blVariantsSelection') ? '' : " and {$articleTable}.oxparentid = '' ";
        } else {
            // selected category ?
            if ($synchId && $oxidId != $synchId) {
                $blVariantsSelectionParameter = $myConfig->getConfigParam('blVariantsSelection');
                $trueResponse = " ( {$articleTable}.oxid=$object2categoryTable.oxobjectid " .
                                "or {$articleTable}.oxparentid=$object2categoryTable.oxobjectid )";
                $failResponse = " {$articleTable}.oxid=$object2categoryTable.oxobjectid ";
                $variantSelectionSql = $blVariantsSelectionParameter ? $trueResponse : $failResponse;

                $outputQuery = " from $object2categoryTable left join {$articleTable} on {$variantSelectionSql}" .
                               " where $object2categoryTable.oxcatnid = " . $db->quote($oxidId) . " ";
            } else {
                $outputQuery = " from oxaccessoire2article left join {$articleTable} " .
                               "on oxaccessoire2article.oxobjectid={$articleTable}.oxid " .
                               " where oxaccessoire2article.oxarticlenid = " . $db->quote($oxidId) . " ";
            }
        }

        if ($synchId && $synchId != $oxidId) {
            // performance
            $subSelect = ' select oxaccessoire2article.oxobjectid from oxaccessoire2article ';
            $subSelect .= " where oxaccessoire2article.oxarticlenid = " . $db->quote($synchId) . " ";
            $outputQuery .= " and {$articleTable}.oxid not in ( $subSelect )";
        }

        // skipping self from list
        $sId = ($synchId) ? $synchId : $oxidId;
        $outputQuery .= " and {$articleTable}.oxid != " . $db->quote($sId) . " ";

        // creating AJAX component
        return $outputQuery;
    }


    /**
     * overide default sorting and replace it with OXSORT field
     *
     * @return string
     */
    protected function _getSorting()
    {
        if ($this->containerId == 'container2') {
            return ' order by _2,_0';
        } else {
            return ' order by _' . $this->_getSortCol() . ' ' . $this->_getSortDir() . ' ';
        }
    }

    /**
     * Removing article form accessories article list
     */
    public function removeArticleAcc()
    {
        $aChosenArt = $this->_getActionIds('oxaccessoire2article.oxid');
        // removing all
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all')) {
            $sQ = $this->_addFilter("delete oxaccessoire2article.* " . $this->_getQuery());
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        } elseif (is_array($aChosenArt)) {
            $sChosenArticles = implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aChosenArt));
            $sQ = "delete from oxaccessoire2article where oxaccessoire2article.oxid in ({$sChosenArticles}) ";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        }
    }

    /**
     * Adding article to accessories article list
     */
    public function addArticleAcc()
    {
        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $aChosenArt = $this->_getActionIds('oxarticles.oxid');
        $soxId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('synchoxid');

        // adding
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all')) {
            $sArtTable = $this->_getViewName('oxarticles');
            $aChosenArt = $this->_getAll(parent::_addFilter("select $sArtTable.oxid " . $this->_getQuery()));
        }

        if ($oArticle->load($soxId) && $soxId && $soxId != "-1" && is_array($aChosenArt)) {
            foreach ($aChosenArt as $sChosenArt) {
                $oNewGroup = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
                $oNewGroup->init("oxaccessoire2article");
                $oNewGroup->oxaccessoire2article__oxobjectid = new \OxidEsales\Eshop\Core\Field($sChosenArt);
                $oNewGroup->oxaccessoire2article__oxarticlenid = new \OxidEsales\Eshop\Core\Field($oArticle->oxarticles__oxid->value);
                $oNewGroup->oxaccessoire2article__oxsort = new \OxidEsales\Eshop\Core\Field(0);
                $oNewGroup->save();
            }

            $this->onArticleAccessoryRelationChange($oArticle);
        }
    }

    /**
     * Method is used to bind to accessory addition to article action.
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $article
     */
    protected function onArticleAccessoryRelationChange($article)
    {
    }


    /**
     * Applies sorting for Accessories list
     */
    public function sortAccessoriesList()
    {
        $oxidRelationId = Registry::getConfig()->getRequestEscapedParameter('oxid');
        $selectedIdForSort = Registry::getConfig()->getRequestEscapedParameter('sortoxid');
        $sortDirection = Registry::getConfig()->getRequestEscapedParameter('direction');

        $accessoriesList = oxNew(ListModel::class);
        $accessoriesList->init("oxbase", "oxaccessoire2article");
        $sortQuery = "select * from  oxaccessoire2article where OXARTICLENID = :OXARTICLENID order by oxsort,oxid";
        $accessoriesList->selectString($sortQuery, [
            ':OXARTICLENID' => $oxidRelationId
        ]);


        $rebuildList = $this->rebuildAccessoriesSortIndexes($accessoriesList);

        if (($selectedPosition = array_search($selectedIdForSort, $rebuildList)) !== false) {
            $selectedSortRecord = $accessoriesList->offsetGet($rebuildList[$selectedPosition]);
            $currentPosition = $selectedSortRecord->oxaccessoire2article__oxsort->value;

            // get current selected row sort position

            if (($sortDirection == 'up' && $currentPosition > 0) || ($sortDirection == 'down' && $currentPosition < count($rebuildList) - 1)) {
                $newPosition = ($sortDirection == 'up') ? ($currentPosition - 1) : ($currentPosition + 1);

                // exchanging indexes
                $currentRecord = $accessoriesList->offsetGet($rebuildList[$currentPosition]);
                $newRecord = $accessoriesList->offsetGet($rebuildList[$newPosition]);

                $currentRecord->oxaccessoire2article__oxsort = new Field($newPosition);
                $newRecord->oxaccessoire2article__oxsort = new Field($currentPosition);
                $currentRecord->save();
                $newRecord->save();
            }
        }

        $outputQuery = $this->_getQuery();

        $normalQuery = 'select ' . $this->_getQueryCols() . $outputQuery;
        $countQuery = 'select count( * ) ' . $outputQuery;

        $this->_outputResponse($this->_getData($countQuery, $normalQuery));
    }


    /**
     * rebuild Accessories sort indexes
     *
     * @param ListModel $inputList
     *
     * @return array
     */
    private function rebuildAccessoriesSortIndexes(ListModel $inputList): array
    {
        $counter = 0;
        $outputList = [];
        foreach ($inputList as $key => $value) {
            if (isset($value->oxaccessoire2article__oxsort)) {
                if ($value->oxaccessoire2article__oxsort->value != $counter) {
                    $value->oxaccessoire2article__oxsort = new Field($counter);
                    $value->save();
                }
            }
            $outputList[$counter] = $key;
            $counter++;
        }

        return $outputList;
    }
}
