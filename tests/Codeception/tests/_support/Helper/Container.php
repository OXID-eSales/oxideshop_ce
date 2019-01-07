<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace Helper;


class Container
{
    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @param string $id
     * @param array  $parameters
     *
     * @return mixed|null|object
     */
    public function get($id, $parameters)
    {
        if (!$this->has($id)) {
            throw new \Exception('Method does not exists: '.$this->getMethodNameFromId($id));
        }
        return $this->resolve($id, $parameters);
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function has($id)
    {
        return method_exists($this, $this->getMethodNameFromId($id));
    }

    /**
     * @param $id
     *
     * @return string
     */
    private function getMethodNameFromId($id)
    {
        return 'make'.str_replace('\\', '', $id);
    }

    /**
     * @param string $id
     * @param array  $parameters
     *
     * @return mixed
     */
    private function resolve($id, $parameters)
    {
        $methodName = $this->getMethodNameFromId($id);
        return call_user_func_array([$this, $methodName], $parameters);
    }

    public function makePageBasket($tester)
    {
        return new \OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Basket($tester);
    }

    public function makePageProductDetails($tester)
    {
        return new \OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\ProductDetails($tester);
    }
}