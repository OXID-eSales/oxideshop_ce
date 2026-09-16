<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminListController;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\View\ThemeViewServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ThemeList extends AdminListController
{
    private ThemeViewServiceInterface $themeViewService;
    private ContextInterface $context;

    public function __construct()
    {
        $this->themeViewService = ContainerFacade::get(ThemeViewServiceInterface::class);
        $this->context = ContainerFacade::get(ContextInterface::class);

        parent::__construct();
    }

    public function render(): string
    {
        parent::render();

        $this->_aViewData['mylist'] = $this->themeViewService->getThemes($this->context->getCurrentShopId());

        return 'theme_list';
    }
}
