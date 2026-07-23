<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\Session;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
use OxidEsales\EshopCommunity\Internal\Framework\Session\SessionInterface;

class SessionAdapter implements SessionInterface
{
    public function get(string $name, mixed $default = null): mixed
    {
        if (!$this->has($name)) {
            return $default;
        }

        return $this->session()->getVariable($name);
    }

    public function has(string $name): bool
    {
        return $this->session()->hasVariable($name);
    }

    public function set(string $name, mixed $value): void
    {
        $this->session()->setVariable($name, $value);
    }

    public function remove(string $name): mixed
    {
        if (!$this->has($name)) {
            return null;
        }

        $value = $this->get($name);
        $this->session()->deleteVariable($name);

        return $value;
    }

    private function session(): Session
    {
        $session = Registry::getSession();
        if (!$session->isSessionStarted()) {
            $session->start();
        }

        return $session;
    }
}
