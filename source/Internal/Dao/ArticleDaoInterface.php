<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 20.07.17
 * Time: 15:57
 */

namespace OxidEsales\EshopCommunity\Internal\Dao;


interface ArticleDaoInterface extends BaseDaoInterface
{

    public function getIsActiveSqlSnippet($forceTableUsage);

    public function getIsActiveSqlSnippetForTable($tableName);

    public function getStockCheckQuerySnippet($forceTableUsage);

}