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

namespace OxidEsales\EshopCommunity\Core\Module;

use \oxDb;

/**
 * Provides a way to get content from module template block file.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ModuleTemplateBlockRepository
{
    /**
     * Return how many blocks of provided module overrides any template for active shop.
     *
     * @param array  $modulesId list of modules to check if their template blocks overrides some shop block.
     * @param string $shopId    shop id to check if some module block overrides some template blocks in this Shop.
     *
     * @return string count of blocks for Shop=$shopId from modules=$modulesId.
     */
    public function getBlocksCount($modulesId, $shopId)
    {
        $db = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $modulesIdQuery = implode(", ", oxDb::getDb()->quoteArray($modulesId));
        $sql = "select COUNT(*)
                            from oxtplblocks
                            where oxactive=1
                                and oxshopid = ?
                                and oxmodule in ( " . $modulesIdQuery . " )";

        return $db->getOne($sql, array($shopId));
    }

    /**
     * Get modules template blocks information filtered by provided parameters.
     *
     * @param string $shopTemplateName shop template file name.
     * @param array  $activeModulesId  list of modules to get information about.
     * @param string $shopId           in which Shop modules must be active.
     * @param array  $themesId         list of themes to get information about.
     *
     * @return array
     */
    public function getBlocks($shopTemplateName, $activeModulesId, $shopId, $themesId = [])
    {
        $modulesId = implode(", ", oxDb::getDb()->quoteArray($activeModulesId));

        $activeThemesIdQuery = $this->formActiveThemesIdQuery($themesId);
        $sql = "select *
                    from oxtplblocks
                    where oxactive=1
                        and oxshopid=?
                        and oxtemplate=?
                        and oxmodule in ( " . $modulesId . " )
                        and oxtheme in (" . $activeThemesIdQuery . ")
                        order by oxpos asc, oxtheme asc, oxid asc";
        $db = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);

        return $db->getAll($sql, [$shopId, $shopTemplateName]);
    }

    /**
     * To form sql query part for active themes.
     *
     * @param array $activeThemeIds
     *
     * @return string
     */
    private function formActiveThemesIdQuery($activeThemeIds = [])
    {
        $defaultThemeIndicator = '';
        array_unshift($activeThemeIds, $defaultThemeIndicator);

        return implode(', ', oxDb::getDb()->quoteArray($activeThemeIds));
    }
}
