<?php

namespace OxidEsales\EshopCommunity\Internal\Common\Dao;

interface DynamicDataObjectDaoInterface
{
    public function addObjectClass(string $objectClass);

    public function addProperty($name, $type);

    public function create();

}