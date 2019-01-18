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
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version       OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\oxid\test11\Application\Controller;

class Test11AjaxController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * @inheritdoc
     */
    public function render()
    {
        parent::render();

        if (\OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\Request::class)->getRequestParameter("aoc")) {
            $ajax = oxNew(\OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\oxid\test11\Application\Controller\Test11AjaxControllerAjax::class);
            $this->_aViewData['oxajax_result'] = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('testModule11AjaxCalledSuccessfully');
            $this->_aViewData['oxajax'] = $ajax->getFeedback();

            return "test_11_popup.tpl";
        }

        return "test_11_ajax_controller.tpl";
    }
}
