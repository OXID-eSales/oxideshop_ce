<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\ConfigFile;

/**
 * @deprecated since version 8.0 you can use container parameters
 */
interface ConfigFileDaoInterface
{
    /**
     * @param string $placeholderName
     * @param string $value
     * @throws ConfigFileNotFoundException
     * @throws FileNotEditableException
     */
    public function replacePlaceholder(string $placeholderName, string $value): void;

    /**
     * @throws ConfigFileNotFoundException
     * @throws FileNotEditableException
     */
    public function checkIsEditable(): void;
}
