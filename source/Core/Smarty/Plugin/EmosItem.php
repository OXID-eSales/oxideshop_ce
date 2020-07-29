<?php

/**
 * EMOS PHP Bib 2
 *
 * Copyright (c) 2004 - 2007 ECONDA GmbH Karlsruhe
 * All rights reserved.
 *
 * ECONDA GmbH
 * Haid-und-Neu-Str. 7
 * 76131 Karlsruhe
 * Tel. +49 (721) 6630350
 * Fax +49 (721) 66303510
 * info@econda.de
 * www.econda.de
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 * Neither the name of the ECONDA GmbH nor the names of its contributors may
 * be used to endorse or promote products derived from this software without
 * specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

namespace OxidEsales\EshopCommunity\Core\Smarty\Plugin;

/**
 * A Class to hold products as well a basket items
 * If you want to track a product view, set the quantity to 1.
 * For "real" basket items, the quantity should be given in your
 * shopping systems basket/shopping cart.
 *
 * Purpose of this class:
 * This class provides a common subset of features for most shopping systems
 * products or basket/cart items. So all you have to do is to convert your
 * products/articles/basket items/cart items to a EMOS_Items. And finally use
 * the functionaltiy of the EMOS class.
 * So for each shopping system we only have to do the conversion of the cart/basket
 * and items and we can (hopefully) keep the rest of code.
 *
 * Shopping carts:
 *  A shopping cart / basket is a simple Array[] of EMOS items.
 *  Convert your cart to a Array of EMOS_Items and your job is nearly done.
 *
 * @deprecated since v6.5.6 (2020-07-29); moved the functionality to 'OXID personalization' module
 */
class EmosItem
{
    /**
     * unique Identifier of a product e.g. article number
     *
     * @var string
     */
    public $productId = "NULL";

    /**
     * the name of a product
     *
     * @var string
     */
    public $productName = "NULL";

    /**
     * the price of the product, it is your choice wether its gross or net
     *
     * @var string
     */
    public $price = "NULL";

    /**
     * the product group for this product, this is a drill down dimension
     * or tree-like structure
     * so you might want to use it like this:
     * productgroup/subgroup/subgroup/product
     *
     * @var string
     */
    public $productGroup = "NULL";

    /**
     * the quantity / number of products viewed/bought etc..
     *
     * @var string
     */
    public $quantity = "NULL";

    /**
     * variant of the product e.g. size, color, brand ....
     * remember to keep the order of theses variants allways the same
     * decide which variant is which feature and stick to it
     *
     * @var string
     */
    public $variant1 = "NULL";
    public $variant2 = "NULL";
    public $variant3 = "NULL";
}
