<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminListController;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\View\ThemeViewServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ThemeList extends AdminListController
{
    public function __construct(
        private readonly ThemeViewServiceInterface $themeViewService,
        private readonly ContextInterface $context,
    ) {
        parent::__construct();
    }

    public function render(): string
    {
        parent::render();

        $this->_aViewData['mylist'] = $this->themeViewService->getThemes($this->context->getCurrentShopId());

        return 'theme_list';
    }
}
