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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Shows bigger tag cloud
 */
class Tags extends oxUBase
{
    /**
     * Class template
     *
     * @var string
     */
    protected $_sThisTemplate = "page/tags/tags.tpl";

    /**
     * If tags are ON - returns parent::render() value, else - displays 404
     * page, as tags are off
     *
     * @return string
     */
    public function render()
    {
        // if tags are off - showing 404 page
        if ( !$this->showTags()  ) {
            error_404_handler();
        }
        return parent::render();
    }

    /**
     * Returns tag cloud manager class
     *
     * @return oxTagCloud
     */
    public function getTagCloudManager()
    {
        $oTagList = oxNew( "oxtaglist" );
        $oTagCloud = oxNew( "oxTagCloud" );
        $oTagCloud->setTagList($oTagList);
        $oTagCloud->setExtendedMode( true );
        return $oTagCloud;
    }

    /**
     * Returns SEO suffix for page title
     *
     * @return string
     */
    public function getTitleSuffix()
    {
    }

    /**
     * Returns title page suffix used in template
     *
     * @return string
     */
    public function getTitlePageSuffix()
    {
        if ( ( $iPage = $this->getActPage() ) ) {
            return oxRegistry::getLang()->translateString( 'PAGE' )." ". ( $iPage + 1 );
        }
    }

    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        $aPaths = array();
        $aCatPath = array();

        $aCatPath['title'] = oxRegistry::getLang()->translateString( 'TAGS', oxRegistry::getLang()->getBaseLanguage(), false );
        $aCatPath['link']  = $this->getLink();
        $aPaths[] = $aCatPath;

        return $aPaths;
    }

}
