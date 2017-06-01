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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

use OxidEsales\Eshop\Core\Registry;

/**
 * Class test_module_controller_routing_MyModuleController
 */
class test_module_controller_routing_MyModuleController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * Current view template
     *
     * @var string
     */
    protected $_sThisTemplate = 'test_module_controller_routing.tpl';

    /**
     * Message from request
     */
    protected $message = '';

    /**
     * Rendering method.
     *
     * @return mixed
     */
    public function render()
    {
        $template = parent::render();

        return $template;
    }

    /**
     * Display message.
     */
    public function displayMessage()
    {
        $this->_aViewData['the_module_message'] =  $this->getMessage();
        $this->render();
    }

    /**
     * Template variable getter. Returns entered message
     *
     * @return object
     */
    public function getMessage()
    {
        $this->message = (string) Registry::getConfig()->getRequestParameter('mymodule_message');

        return $this->message;
    }
}
