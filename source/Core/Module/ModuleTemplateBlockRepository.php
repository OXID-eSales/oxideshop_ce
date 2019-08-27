<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Module;

/**
 * Provides a way to get content from module template block file.
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
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
        $db = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
        $modulesIdQuery = implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($modulesId));
        $sql = "select COUNT(*)
                            from oxtplblocks
                            where oxactive = :oxactive
                                and oxshopid = :oxshopid
                                and oxmodule in ( " . $modulesIdQuery . " )";

        return $db->getOne($sql, [
            ':oxactive' => '1',
            ':oxshopid' => $shopId
        ]);
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
        $modulesId = implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($activeModulesId));

        $activeThemesIdQuery = $this->formActiveThemesIdQuery($themesId);
        $sql = "select *
                    from oxtplblocks
                    where oxactive=1
                        and oxshopid= :oxshopid
                        and oxtemplate= :oxtemplate
                        and oxmodule in ( " . $modulesId . " )
                        and oxtheme in (" . $activeThemesIdQuery . ")
                        order by oxpos asc, oxtheme asc, oxid asc";
        $db = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);

        return $db->getAll($sql, [
            ':oxshopid' => $shopId,
            ':oxtemplate' => $shopTemplateName
        ]);
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

        return implode(', ', \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($activeThemeIds));
    }
}
