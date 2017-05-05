<?php

namespace OxidEsales\EshopCommunity\Application\Model\Contract;

interface ReadOnlyArticleInterface
{
    public function getPrice();
    public function getSalePrice();
    public function getTitle();
    public function getShortDescription();
    public function getVariations();
}
