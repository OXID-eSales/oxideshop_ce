<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 11.07.17
 * Time: 13:40
 */

namespace OxidEsales\EshopCommunity\Internal\Dao;

use Doctrine\DBAL\Connection;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Internal\Utility\OxidLegacyServiceInterface;

class ArticleDao extends BaseDao implements ArticleDaoInterface
{

    public function __construct(Connection $connection,
                                ContextInterface $context,
                                OxidLegacyServiceInterface $legacyService)
    {
        parent::__construct('oxarticles', $connection, $context, $legacyService);
    }

    public function getIsActiveSqlSnippet($forceTableUsage)
    {

        return $this->getIsActiveSqlSnippetForTable($this->getViewName($forceTableUsage));
    }

    public function getIsActiveSqlSnippetForTable($tableName)
    {

        $snippet = " $tableName.oxactive = 1  and $tableName.oxhidden = 0 ";

        if ($this->context->useTimeCheck()) {
            $snippet = " (  $snippet or " . $this->getActiveTimeRangeSnippetForTable($tableName) . ') ';
        }

        return $snippet;
    }

    public function getStockCheckQuerySnippet($forceTableUsage)
    {
        if (!$this->context->useStock()) {
            return '';
        }

        $viewName = $this->getViewName($forceTableUsage);

        $baseSnippet = " and ( $viewName.oxstockflag != 2 or ( $viewName.oxstock + " .
                       "$viewName.oxvarstock ) > 0  ) ";

        if ($this->context->isVariantParentBuyable()) {
            return $baseSnippet;
        }

        $tableAlias = 'art';

        // TODO: This active sub snippet is incomplete, it should be
        // constructed using getIsActiveSqlSnippetForTable, but I won't fix now
        // this for backward compatibility reasons
        $activeSubSnippet = " and $tableAlias.oxactive = 1";
        if ($this->context->useTimeCheck()) {
            $activeSubSnippet = " and  (  $tableAlias.oxactive = 1 or " .
                                $this->getActiveTimeRangeSnippetForTable($tableAlias) . ') ';
        }

        return " $baseSnippet and IF( $viewName.oxvarcount = 0, 1, " .
               "( select 1 from $viewName as $tableAlias where $tableAlias.oxparentid=$viewName.oxid" .
               "$activeSubSnippet and ( $tableAlias.oxstockflag != 2 or $tableAlias.oxstock > 0 ) limit 1 ) ) ";
    }

}