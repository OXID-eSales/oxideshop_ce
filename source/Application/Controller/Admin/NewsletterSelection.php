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

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxDb;

/**
 * Newsletter user group selection manager.
 * Adds/removes chosen user group to/from newsletter mailing.
 * Admin Menu: Customer Info -> Newsletter -> Selection.
 */
class NewsletterSelection extends \oxAdminDetails
{

    /**
     * Amount of users assigned to active newsletter receiver group
     *
     * @var int
     */
    protected $_iUserCount = null;

    /**
     * Executes parent method parent::render(), creates oxlist object and
     * collects user groups information, passes it's data to Smarty engine
     * and returns name of template file "newsletter_selection.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oNewsletter = oxNew("oxnewsletter");
            if ($oNewsletter->load($soxId)) {
                $this->_aViewData["edit"] = $oNewsletter;

                if (oxRegistry::getConfig()->getRequestParameter("aoc")) {
                    $oNewsletterSelectionAjax = oxNew('newsletter_selection_ajax');
                    $this->_aViewData['oxajax'] = $oNewsletterSelectionAjax->getColumns();

                    return "popups/newsletter_selection.tpl";
                }
            }
        }

        return "newsletter_selection.tpl";
    }

    /**
     * Returns count of users assigned to active newsletter receiver group
     *
     * @return int
     */
    public function getUserCount()
    {
        if ($this->_iUserCount === null) {
            $this->_iUserCount = 0;

            // load object
            $oNewsletter = oxNew("oxnewsletter");
            if ($oNewsletter->load($this->getEditObjectId())) {
                // get nr. of users in these groups
                // we do not use lists here as we dont need this overhead right now
                $oDB = oxDb::getDb();
                $blSep = false;
                $sSelectGroups = " ( oxobject2group.oxgroupsid in ( ";

                // remove already added groups
                foreach ($oNewsletter->getGroups() as $oInGroup) {
                    if ($blSep) {
                        $sSelectGroups .= ",";
                    }
                    $sSelectGroups .= $oDB->quote($oInGroup->oxgroups__oxid->value);
                    $blSep = true;
                }

                $sSelectGroups .= " ) ) ";

                // no group selected
                if (!$blSep) {
                    $sSelectGroups = " oxobject2group.oxobjectid is null ";
                }
                $sShopId = $this->getConfig()->getShopID();
                $sQ = "select count(*) from ( select oxnewssubscribed.oxemail as _icnt from oxnewssubscribed left join
                   oxobject2group on oxobject2group.oxobjectid = oxnewssubscribed.oxuserid
                   where ( oxobject2group.oxshopid = '{$sShopId}'
                   or oxobject2group.oxshopid is null ) and {$sSelectGroups} and
                   oxnewssubscribed.oxdboptin = 1 and ( not ( oxnewssubscribed.oxemailfailed = '1') )
                   and (not(oxnewssubscribed.oxemailfailed = '1')) and oxnewssubscribed.oxshopid = '{$sShopId}'
                   group by oxnewssubscribed.oxemail ) as _tmp";

                // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
                $this->_iUserCount = oxDb::getMaster()->getOne($sQ);
            }
        }

        return $this->_iUserCount;
    }

    /**
     * Saves newsletter selection changes.
     */
    public function save()
    {
        $soxId = $this->getEditObjectId();
        $aParams = oxRegistry::getConfig()->getRequestParameter("editval");
        $aParams['oxnewsletter__oxshopid'] = $this->getConfig()->getShopId();

        $oNewsletter = oxNew("oxNewsLetter");
        if ($soxId != "-1") {
            $oNewsletter->load($soxId);
        } else {
            $aParams['oxnewsletter__oxid'] = null;
        }

        $oNewsletter->assign($aParams);
        $oNewsletter->save();

        // set oxid if inserted
        $this->setEditObjectId($oNewsletter->getId());
    }
}
