<?php declare(use_strict=1);

namespace OxidEsales\EshopCommunity\Internal\Common\Dao;

interface DynamicDataObjectDaoInterface
{
    /**
     * Set the class type this dao is handling. Normally used
     * in constructor, but could be used in the DI container
     * if needed.
     *
     * @param string $objectClass
     */
    public function addObjectClass(string $objectClass): void;

    public function addProperty(string $name, string $type): void;

    /**
     * Creates an instance of the object class set by addObjectClass()
     *
     * @return mixed
     */
    public function create();
}