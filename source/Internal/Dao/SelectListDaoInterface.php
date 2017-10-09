<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 26.09.17
 * Time: 11:53
 */

namespace OxidEsales\EshopCommunity\Internal\Dao;

use OxidEsales\EshopCommunity\Internal\DataObject\SelectList;

interface SelectListDaoInterface
{

    /**
     * @param string $articleId
     *
     * @return SelectList
     */
    public function getSelectListForArticle($articleId);
}