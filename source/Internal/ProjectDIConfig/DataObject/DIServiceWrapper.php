<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\ProjectDIConfig\DataObject;

use OxidEsales\EshopCommunity\Internal\Application\Events\ShopAwareInterface;

/**
 * @internal
 */
class DIServiceWrapper
{

    const CALLS_SECTION = 'calls';

    const SET_ACTIVE_SHOPS_METHOD = 'setActiveShops';
    const SET_ACTIVE_PARAMETERS = [[]];
    const SET_CONTEXT_METHOD = 'setContext';
    const SET_CONTEXT_PARAMETER = '@OxidEsales\EshopCommunity\Internal\Utility\ContextInterface';

    /** @var  string $key */
    private $key;

    /** @var  array $serviceArray */
    private $serviceArray;

    /**
     * DIServiceWrapper constructor.
     *
     * @param string $key
     * @param array  $serviceArray
     */
    public function __construct(string $key, array $serviceArray)
    {
        $this->key = $key;
        $this->serviceArray = $serviceArray;
    }

    /**
     * @return array
     */
    public function getServiceAsArray(): array
    {
        return $this->serviceArray;
    }

    /**
     * @return bool
     */
    public function isShopAware(): bool
    {
        if (!$this->hasClass()) {
            return false;
        }

        $class = $this->getClass();
        $interfaces = class_implements($class);

        return in_array(ShopAwareInterface::class, $interfaces);
    }

    /**
     * @param array $shops
     * @return array
     */
    public function addActiveShops(array $shops)
    {
        $this->addShopAwareCallsIfMissing();
        $setActiveShopsCall = $this->getCall($this::SET_ACTIVE_SHOPS_METHOD);
        $currentlyActiveShops = $setActiveShopsCall->getParameter(0);
        $newActiveShops = array_merge($currentlyActiveShops, $shops);
        $setActiveShopsCall->setParameter(0, $newActiveShops);
        $this->updateCall($setActiveShopsCall);
        return $newActiveShops;
    }

    /**
     * @param array $shops
     * @return array
     */
    public function removeActiveShops(array $shops)
    {
        $setActiveShopsCall = $this->getCall($this::SET_ACTIVE_SHOPS_METHOD);
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

    /**
     * @return bool
     */
    public function hasActiveShops()
    {
        $this->addShopAwareCallsIfMissing();
        $setActiveShopsCall = $this->getCall($this::SET_ACTIVE_SHOPS_METHOD);
        $currentlyActiveShops = $setActiveShopsCall->getParameter(0);
        return count($currentlyActiveShops) > 0;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     *
     */
    private function addShopAwareCallsIfMissing()
    {
        if (!$this->hasCall($this::SET_ACTIVE_SHOPS_METHOD)) {
            $setActiveShopCall = new DICallWrapper();
            $setActiveShopCall->setMethodName($this::SET_ACTIVE_SHOPS_METHOD);
            $setActiveShopCall->setParameter(0, []);
            $this->addCall($setActiveShopCall);
        }
        if (!$this->hasCall($this::SET_CONTEXT_METHOD)) {
            $setContextCall = new DICallWrapper();
            $setContextCall->setMethodName($this::SET_CONTEXT_METHOD);
            $setContextCall->setParameter(0, $this::SET_CONTEXT_PARAMETER);
            $this->addCall($setContextCall);
        }
    }

    /**
     * @return array
     */
    private function getCalls(): array
    {
        if (!key_exists($this::CALLS_SECTION, $this->serviceArray)) {
            return [];
        }
        $calls = [];
        foreach ($this->serviceArray[$this::CALLS_SECTION] as $callArray) {
            $calls[] = new DICallWrapper($callArray);
        }

        return $calls;
    }

    /**
     * @param string $methodName
     *
     * @return bool
     */
    private function hasCall(string $methodName)
    {
        foreach ($this->getCalls() as $call) {
            if ($call->getMethodName() == $methodName) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param DICallWrapper $call
     */
    private function addCall(DICallWrapper $call)
    {
        if (!key_exists($this::CALLS_SECTION, $this->serviceArray)) {
            $this->serviceArray[$this::CALLS_SECTION] = [];
        }
        $this->serviceArray[$this::CALLS_SECTION][] = $call->getCallAsArray();
    }

    /**
     * @param DICallWrapper $call
     *
     * @throws \Exception
     * @return void
     */
    private function updateCall(DICallWrapper $call)
    {
        for ($i = 0; $i < count($this->serviceArray[$this::CALLS_SECTION]); $i++) {
            $existingCall = new DICallWrapper($this->serviceArray[$this::CALLS_SECTION][$i]);
            if ($existingCall->getMethodName() == $call->getMethodName()) {
                $this->serviceArray[$this::CALLS_SECTION][$i] = $call->getCallAsArray();
                return;
            }
        }
        throw new \Exception("Did not find call to update");
    }


    /**
     * @param string $methodName
     *
     * @return DICallWrapper
     * @throws \Exception
     */
    private function getCall(string $methodName): DICallWrapper
    {

        if (key_exists($this::CALLS_SECTION, $this->serviceArray)) {
            foreach ($this->serviceArray[$this::CALLS_SECTION] as $callArray) {
                $call = new DICallWrapper($callArray);
                if ($call->getMethodName() == $methodName) {
                    return $call;
                }
            }
        }
        throw new \Exception("Call $methodName does not exist");
    }


    /**
     * @return string
     */
    private function getClass(): string
    {
        return $this->serviceArray['class'];
    }

    /**
     * @return bool
     */
    private function hasClass(): bool
    {
        return key_exists('class', $this->serviceArray);
    }
}
