<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

class TestModuleTenPrice extends TestModuleTenPrice_parent
{
    protected $originalPrice = null;

    /**
     * Triple the original price
     *
     * @return double
     */
    public function getPrice()
    {
        $this->originalPrice = parent::getPrice();

        return $this->originalPrice * 3;
    }
}
