<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\BackwardsCompatibility;

class ForwardCompatibleTypeHintWithCommunityNamespace_1_Test extends \PHPUnit\Framework\TestCase
{

    /**
     * Test the backwards compatibility with camel cased type hints
     */
    public function testForwardCompatibleTypeHintWithCommunityNamespace()
    {
        $object = oxNew(\oxArticle::class);
        /**
         * @param \OxidEsales\EshopCommunity\Application\Model\Article $object
         */
        $functionWithTypeHint = function (\OxidEsales\EshopCommunity\Application\Model\Article $object) {
            /** If the function was called successfully, the test would have passed */
            $this->assertTrue(true);
        };
        /** The function call would produce a catchable fatal error, if the type hint is not correct */
        $functionWithTypeHint($object);
    }
}
