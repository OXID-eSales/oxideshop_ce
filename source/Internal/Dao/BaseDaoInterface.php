<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 20.07.17
 * Time: 15:57
 */

namespace OxidEsales\EshopCommunity\Internal\Dao;


interface BaseDaoInterface
{

    public function getViewName($forceTableUsage);

    public function getViewNameForTable($tablename, $forceTableUsage);

    public function getActiveTimeRangeSnippetForTable($tableName);

}
