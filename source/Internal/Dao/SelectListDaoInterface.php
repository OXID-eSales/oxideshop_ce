<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 26.09.17
 * Time: 11:53
 */

namespace OxidEsales\EshopCommunity\Internal\Dao;

interface SelectListDaoInterface
{

    public function getSelectListForArticle($articleId);
}