<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 27.09.17
 * Time: 09:21
 */

namespace OxidEsales\EshopCommunity\Internal\DataObject;


class SelectList
{
    /** @var SelectListItem[][] $selections  */
    private $selections = [];

    public function __construct($selections)
    {
        $this->selections = $selections;
    }

    public function modifyPriceForSelection($price, $selectionIndexes) {

        for($i = 0; $i < sizeof($selectionIndexes); $i++) {
            $selection = $this->selections[$i];
            $index = $selectionIndexes[$i];
            $selectionItem = $selection[$index];
            $price = $selectionItem->modifyPrice($price);
        }

        return $price;

    }

}