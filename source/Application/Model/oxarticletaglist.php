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

/**
 * Class dedicated to article tags handling.
 * Is responsible for saving, returning and adding tags for given article.
 */
class oxArticleTagList extends oxI18n implements oxITagList
{
    /**
     * Tags.
     *
     * @var string
     */
    protected $_oTagSet = null;

    /**
     * Instantiates oxTagSet object.
     */
    public function __construct($config)
    {
        parent::__construct($config);
        $this->_oTagSet = oxNew('oxTagSet');
    }

    /**
     * Sets article id.
     *
     * @param string $articleId Article id
     */
    public function setArticleId($articleId)
    {
        $this->setId($articleId);
    }

    /**
     * Returns current article id.
     *
     * @return string
     */
    public function getArticleId()
    {
        return $this->getId();
    }

    /**
     * Returns cache id.
     *
     * @return string
     */
    public function getCacheId()
    {
        return null;
    }

    /**
     * Loads article tags from DB. Returns true on success.
     *
     * @param string $articleId article id
     *
     * @return bool
     */
    public function load($articleId)
    {
        $this->setArticleId($articleId);
        $database = oxDb::getDb();
        $viewName = getViewName('oxartextends', $this->getLanguage());
        $query = "select oxtags from {$viewName} where oxid = " . $database->quote($articleId);

        $this->set('');
        // adding tags to list. Tags does not need to be checked again, but dashes needs to be removed
        $tags = explode($this->get()->getSeparator(), $database->getOne($query));
        foreach ($tags as $tag) {
            $oxTag = oxNew('oxtag');
            $oxTag->set($tag, false);
            $oxTag->removeUnderscores();
            $this->addTag($oxTag);
        }

        return $this->_isLoaded = true;
    }

    /**
     * Loads article tags list.
     *
     * @param string $articleId article id
     *
     * @return bool
     */
    public function loadList($articleId = null)
    {
        if ($articleId === null && ($articleId = $this->getArticleId()) === null) {
            return false;
        }

        return $this->load($articleId);
    }

    /**
     * Saves article tags to DB. Returns true on success.
     *
     * @return bool
     */
    public function save()
    {
        if (!$this->canSave()) {
            return false;
        }

        if (!$this->getArticleId()) {
            return false;
        }
        $tagSet = $this->get();
        foreach ($tagSet as $tag) {
            $tag->addUnderscores();
        }
        $tags = oxDb::getInstance()->escapeString($tagSet);
        $database = oxDb::getDb();

        $table = getLangTableName('oxartextends', $this->getLanguage());
        $languageSuffix = oxRegistry::getLang()->getLanguageTag($this->getLanguage());

        $query = "insert into {$table} (oxid, oxtags$languageSuffix) value (" . $database->quote($this->getArticleId()) . ", '{$tags}')
               on duplicate key update oxtags$languageSuffix = '{$tags}'";

        if ($database->execute($query)) {
            $this->executeDependencyEvent();

            return true;
        }

        return false;
    }


    /**
     * Saves article tags.
     *
     * @param string $tag article tag
     *
     * @return bool
     */
    public function set($tag)
    {
        return $this->_oTagSet->set($tag);
    }

    /**
     * Returns article tags set object.
     *
     * @return object;
     */
    public function get()
    {
        return $this->_oTagSet;
    }

    /**
     * Returns article tags array.
     *
     * @return object;
     */
    public function getArray()
    {
        return $this->_oTagSet->get();
    }

    /**
     * Adds tag to list.
     *
     * @param string $tag tag as string or as oxTag object
     *
     * @return bool
     */
    public function addTag($tag)
    {
        return $this->_oTagSet->addTag($tag);
    }

    /**
     * Returns standard product Tag URL.
     *
     * @param string $tag tag
     *
     * @return string
     */
    public function getStdTagLink($tag)
    {
        $stdTagLink = $this->config->getShopHomeURL($this->getLanguage(), false);

        return $stdTagLink . "cl=details&amp;anid=" . $this->getId() . "&amp;listtype=tag&amp;searchtag=" . rawurlencode($tag);
    }

    /**
     * Execute cache dependencies.
     */
    public function executeDependencyEvent()
    {
        $this->_updateTagDependency();
    }

    /**
     * Execute cache dependencies.
     */
    protected function _updateTagDependency()
    {
        // reset tags cloud cache
        $tagList = oxNew("oxTagList");
        $tagList->setLanguage($this->getLanguage());
        $tagCloud = oxNew("oxTagCloud");
        $tagCloud->setTagList($tagList);
        $tagCloud->resetCache();
    }

    /**
     * Should article tags be saved.
     * Method is used to overwrite.
     *
     * @return bool
     */
    protected function canSave()
    {
        return true;
    }
}
