<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\OnlineInfo;

use OxidEsales\Eshop\Core\Curl;

final class CurlSpy extends Curl
{
    private string $logPath;

    public function __construct(string $logPath)
    {
        $this->logPath = $logPath;
    }

    /** @inheritDoc */
    public function getStatusCode()
    {
        return 200;
    }

    /** @inheritDoc */
    public function execute()
    {
        return true;
    }

    /** @inheritDoc */
    public function setParameters($parameters)
    {
        file_put_contents($this->logPath, $parameters['xmlRequest']);
        parent::setParameters($parameters);
    }
}
