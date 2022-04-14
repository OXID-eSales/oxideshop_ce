<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject;

use OxidEsales\EshopCommunity\Internal\Framework\Event\ShopAwareInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Exception\MissingUpdateCallException;

class DIServiceWrapper
{
    private const CLASS_SECTION = 'class';
    private const CALLS_SECTION = 'calls';
    private const SET_ACTIVE_SHOPS_METHOD = 'setActiveShops';
    private const SET_CONTEXT_METHOD = 'setContext';
    public const SET_CONTEXT_PARAMETER = '@OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface';

    /** @var  string $class */
    private $class;

    /** @var  array $calls */
    private $calls = [];

    public function __construct(
        private string $id,
        private array $serviceArguments
    ) {
        $this->setClass();
        $this->setCalls();
    }

    private function setClass(): void
    {
        if (isset($this->serviceArguments[self::CLASS_SECTION])) {
            $this->class = $this->serviceArguments[self::CLASS_SECTION];
        } elseif ($this->checkIfIdIsResolvableClassName()) {
            $this->class = $this->id;
        }
    }

    private function checkIfIdIsResolvableClassName(): bool
    {
        return class_exists($this->id);
    }

    private function setCalls(): void
    {
        if (array_key_exists(self::CALLS_SECTION, $this->serviceArguments)) {
            $this->calls = $this->serviceArguments[self::CALLS_SECTION];
        }
    }

    public function getServiceAsArray(): array
    {
        $this->updateCalls();
        return $this->serviceArguments;
    }

    private function updateCalls(): void
    {
        if (!empty($this->calls)) {
            $this->serviceArguments[self::CALLS_SECTION] = $this->calls;
        }
    }

    public function isShopAware(): bool
    {
        if (!$this->hasClass()) {
            return false;
        }
        return in_array(ShopAwareInterface::class, class_implements($this->getClass()), true);
    }

    public function addActiveShops(array $shops): array
    {
        $this->addShopAwareCallsIfMissing();
        $setActiveShopsCall = $this->getCall(self::SET_ACTIVE_SHOPS_METHOD);
        $currentlyActiveShops = $setActiveShopsCall->getParameter(0);
        $newActiveShops = array_merge($currentlyActiveShops, $shops);
        $setActiveShopsCall->setParameter(0, $newActiveShops);
        $this->updateCall($setActiveShopsCall);
        return $newActiveShops;
    }

    public function removeActiveShops(array $shops): array
    {
        $setActiveShopsCall = $this->getCall(self::SET_ACTIVE_SHOPS_METHOD);
        $currentlyActiveShops = $setActiveShopsCall->getParameter(0);
        $newActiveShops = [];
        foreach ($currentlyActiveShops as $shopId) {
            if (array_search($shopId, $shops) === false) {
                $newActiveShops[] = $shopId;
            }
        }
        $setActiveShopsCall->setParameter(0, $newActiveShops);
        $this->updateCall($setActiveShopsCall);

        return $newActiveShops;
    }

    public function hasActiveShops(): bool
    {
        $this->addShopAwareCallsIfMissing();
        $setActiveShopsCall = $this->getCall(self::SET_ACTIVE_SHOPS_METHOD);
        $currentlyActiveShops = $setActiveShopsCall->getParameter(0);
        return count($currentlyActiveShops) > 0;
    }

    public function getKey(): string
    {
        return $this->id;
    }

    /**
     * Check if the class for the service definition exists.
     * If no class is defined, it also returns true.
     */
    public function checkClassExists(): bool
    {
        if (! $this->hasClass()) {
            return true;
        }
        return class_exists($this->getClass());
    }

    private function addShopAwareCallsIfMissing(): void
    {
        if (!$this->hasCall(self::SET_ACTIVE_SHOPS_METHOD)) {
            $setActiveShopCall = new DICallWrapper();
            $setActiveShopCall->setMethodName(self::SET_ACTIVE_SHOPS_METHOD);
            $setActiveShopCall->setParameter(0, []);
            $this->addCall($setActiveShopCall);
        }
        if (!$this->hasCall(self::SET_CONTEXT_METHOD)) {
            $setContextCall = new DICallWrapper();
            $setContextCall->setMethodName(self::SET_CONTEXT_METHOD);
            $setContextCall->setParameter(0, self::SET_CONTEXT_PARAMETER);
            $this->addCall($setContextCall);
        }
    }

    private function getWrappedCalls(): array
    {
        $wrappedCalls = [];
        foreach ($this->calls as $callArray) {
            $wrappedCalls[] = new DICallWrapper($callArray);
        }

        return $wrappedCalls;
    }

    private function hasCall(string $methodName): bool
    {
        foreach ($this->getWrappedCalls() as $call) {
            if ($call->getMethodName() === $methodName) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param DICallWrapper $call
     */
    private function addCall(DICallWrapper $call): void
    {
        $this->calls[] = $call->getCallAsArray();
    }

    /**
     * @throws MissingUpdateCallException
     */
    private function updateCall(DICallWrapper $call): void
    {
        $callsCount = count($this->calls);

        for ($i = 0; $i < $callsCount; $i++) {
            $existingCall = new DICallWrapper($this->calls[$i]);
            if ($existingCall->getMethodName() === $call->getMethodName()) {
                $this->calls[$i] = $call->getCallAsArray();
                return;
            }
        }
        throw new MissingUpdateCallException();
    }


    /**
     * @throws MissingUpdateCallException
     */
    private function getCall(string $methodName): DICallWrapper
    {
        foreach ($this->calls as $callArray) {
            $call = new DICallWrapper($callArray);
            if ($call->getMethodName() === $methodName) {
                return $call;
            }
        }
        throw new MissingUpdateCallException();
    }

    private function getClass(): string
    {
        return $this->class;
    }

    private function hasClass(): bool
    {
        return isset($this->class);
    }
}
