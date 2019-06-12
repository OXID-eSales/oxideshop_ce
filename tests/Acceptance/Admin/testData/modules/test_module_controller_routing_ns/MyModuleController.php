<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\test_module_controller_routing_ns;

use OxidEsales\Eshop\Core\Registry;

class MyModuleController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * Current view template
     *
     * @var string
     */
    protected $_sThisTemplate = 'test_module_controller_routing_ns.tpl';

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
