<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\ConfigFile;

interface ConfigFileDaoInterface
{
    /**
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
