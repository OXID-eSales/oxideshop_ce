<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   views
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: details.php 42113 2012-02-09 15:05:26Z linas.kukulskis $
 */

/**
 * Special page for Credits
 * @package main
 */
class Credits extends oxUBase
{
    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'page/info/credits.tpl';

    /**
     * Github api url
     * @var string
     */
    protected $_sGithubUrl = 'https://api.github.com/repos/OXID-eSales/oxideshop_ce/contributors';

    /**
     * Returns credits cms page
     *
     * @return object
     */
    public function getCreditsCmsPage()
    {
        $oContent = oxNew( 'oxcontent' );
        if ( $oContent->loadByIdent( 'oxcredits' ) )
        {
            return $oContent;
        }
    }

    /**
     * Returns contributors from github api
     *
     * @return object
     */
    public function getGithubContributors()
    {
        $oCurl = curl_init();
        curl_setopt($oCurl, CURLOPT_URL, $this->_sGithubUrl);
        curl_setopt($oCurl, CURLOPT_USERAGENT, "OXID eShop");
        curl_setopt($oCurl, CURLOPT_HEADER, 0);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 10);
        $sOutput = curl_exec($oCurl);
        curl_close($oCurl);
        if(!empty($sOutput))
        {
            return json_decode($sOutput);
        }
        return false;
    }
}