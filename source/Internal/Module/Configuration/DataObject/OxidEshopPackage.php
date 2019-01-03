<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject;

/**
 * @internal
 */
class OxidEshopPackage
{
    /** Name of directory to be excluded for VCS */
    const BLACKLIST_VCS_DIRECTORY = '.git';

    /** Name of ignore files to be excluded for VCS */
    const BLACKLIST_VCS_IGNORE_FILE = '.gitignore';

    /** Used to install third party integrations. */
    const EXTRA_PARAMETER_KEY_TARGET = 'target-directory';

    /** Used to determine third party package internal source path. */
    const EXTRA_PARAMETER_KEY_SOURCE = 'source-directory';

    /** Glob expression to filter all files, might be used to filter whole directory. */
    const BLACKLIST_ALL_FILES = '**/*';

    /** List of glob expressions used to blacklist files being copied. */
    const EXTRA_PARAMETER_FILTER_BLACKLIST = 'blacklist-filter';

    /** Glob filter expression to exclude VCS files */
    const BLACKLIST_VCS_DIRECTORY_FILTER = OxidEshopPackage::BLACKLIST_VCS_DIRECTORY . DIRECTORY_SEPARATOR . OxidEshopPackage::BLACKLIST_ALL_FILES;

    /** First level key of the section extra in composer.json */
    const EXTRA_PARAMETER_KEY_ROOT = 'oxideshop';

    /** @var string $name */
    private $name = '';

    /** @var array $extra */
    private $extraParameters = [];

    /**
     * @return array
     */
    public function getExtraParameters(): array
    {
        return $this->extraParameters;
    }

    /**
     * @param array $extra
     */
    public function setExtraParameters(array $extra)
    {
        $this->extraParameters = $extra;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
