<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao\ProductMediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataMapper\ViewDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

class ArticlePictures extends AdminDetailsController
{
    public function render()
    {
        parent::render();

        $this->_aViewData['productImages'] = ContainerFacade::get(ViewDataMapperInterface::class)
            ->toData(
                ContainerFacade::get(ProductMediaDaoInterface::class)
                    ->getAll(
                        Id::fromUid(
                            $this->getEditObjectId()
                        )
                    )
            );

        return 'article_pictures';
    }
}
