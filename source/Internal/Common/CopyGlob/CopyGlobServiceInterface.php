<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Common\CopyGlob;

/**
 * Interface CopyGlobServiceInterface
 *
 * @internal
 *
 * @package OxidEsales\EshopCommunity\Internal\Module\Setup\Install
 */
interface CopyGlobServiceInterface
{
    /**
     * @param string $sourcePath
     * @param string $destinationPath
     * @param array  $globExpressionList
     */
    public function copy(string $sourcePath, string $destinationPath, array $globExpressionList = []);
}