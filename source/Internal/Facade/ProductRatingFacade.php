<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Facade;

use OxidEsales\EshopCommunity\Internal\Service\ProductRatingServiceInterface;

/**
 * Class ProductRatingFacade
 * @internal
 * @package OxidEsales\EshopCommunity\Internal\Facade
 */
class ProductRatingFacade implements ProductRatingFacadeInterface
{
    /**
     * @var ProductRatingServiceInterface
     */
    private $productRatingService;

    /**
     * ProductRatingFacade constructor.
     * @param ProductRatingServiceInterface $productRatingService
     */
    public function __construct(ProductRatingServiceInterface $productRatingService)
    {
        $this->productRatingService = $productRatingService;
    }

    /**
     * @param string $productId
     */
    public function updateProductRating($productId)
    {
        $this->productRatingService->updateProductRating($productId);
    }
}
