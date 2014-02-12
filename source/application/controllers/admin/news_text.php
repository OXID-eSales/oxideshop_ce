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
 * Admin Menu: Customer News -> News -> Text.
 * @package admin
 */
class News_Text extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), creates oxnews object and
     * passes news text to smarty. Returns name of template file "news_text.tpl".
     *
     * @return string
     */
    public function render()
    {   $myConfig = $this->getConfig();

        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $oNews = oxNew( "oxnews" );
            $iNewsLang = oxConfig::getParameter("newslang");

            if (!isset($iNewsLang))
                $iNewsLang = $this->_iEditLang;

            $this->_aViewData["newslang"] = $iNewsLang;
            $oNews->loadInLang( $iNewsLang, $soxId );

            foreach ( oxRegistry::getLang()->getLanguageNames() as $id => $language) {
                $oLang= new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }


            $this->_aViewData["edit"] =  $oNews;


        }
        $this->_aViewData["editor"] = $this->_generateTextEditor( "100%", 255, $oNews, "oxnews__oxlongdesc", "news.tpl.css");

        return "news_text.tpl";
    }

    /**
     * Saves news text.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $myConfig  = $this->getConfig();

        $soxId = $this->getEditObjectId();
        $aParams = oxConfig::getParameter( "editval");

        $oNews = oxNew( "oxnews" );

        $iNewsLang = oxConfig::getParameter("newslang");

        if (!isset($iNewsLang))
            $iNewsLang = $this->_iEditLang;

        if ( $soxId != "-1")
            $oNews->loadInLang( $iNewsLang, $soxId );
        else
            $aParams['oxnews__oxid'] = null;



        //$aParams = $oNews->ConvertNameArray2Idx( $aParams);

        $oNews->setLanguage(0);
        $oNews->assign( $aParams);
        $oNews->setLanguage($iNewsLang);

        $oNews->save();
        // set oxid if inserted
        $this->setEditObjectId( $oNews->getId() );
    }

}
