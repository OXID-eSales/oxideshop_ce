<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin Contents manager.
 * Collects Content base information (Description), there is ability to filter
 * them by Description or delete them.
 * Admin Menu: Customerinformations -> Content.
 */
class ContentList extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
{
    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'oxcontent';

    /**
     * Type of list.
     *
     * @var string
     */
    protected $_sListType = 'oxcontentlist';

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'content_list.tpl';

    /**
     * Executes parent method parent::render() and returns current class template
     * name.
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $sFolder = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('folder');
        $sFolder = $sFolder ?: -1;

        $this->_aViewData['folder'] = $sFolder;
        $this->_aViewData['afolder'] = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('aCMSfolder');

        return $this->_sThisTemplate;
    }

    /**
     * Adding folder check and empty folder field check.
     *
     * @param array  $aWhere  SQL condition array
     * @param string $sqlFull SQL query string
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "prepareWhereQuery" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _prepareWhereQuery($aWhere, $sqlFull)
    {
        $sQ = parent::_prepareWhereQuery($aWhere, $sqlFull);
        $sFolder = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('folder');
        $sViewName = getviewName('oxcontents');

        //searchong for empty oxfolder fields
        if ('CMSFOLDER_NONE' === $sFolder || 'CMSFOLDER_NONE_RR' === $sFolder) {
            $sQ .= " and {$sViewName}.oxfolder = '' ";
        } elseif ($sFolder && '-1' !== $sFolder) {
            $sFolder = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($sFolder);
            $sQ .= " and {$sViewName}.oxfolder = {$sFolder}";
        }

        return $sQ;
    }
}
