<?php


namespace OxidEsales\EshopCommunity\Internal\Common\DataObject;

trait DynamicDataObjectTrait
{

    private $dynamicPropertyValues = [];

    public function configureDynamicProperty($name)
    {
        if (! array_key_exists($name, $this->dynamicPropertyValues)) {
            $this->dynamicPropertyValues[$name] = null;
        }
    }

    public function setDynamicPropertyValue($name, $value)
    {
        if (! array_key_exists($name, $this->dynamicPropertyValues)) {
            throw new \Exception("Object " . get_class($this) . " has no property named '$name'");
        }

        $this->dynamicPropertyValues[$name] = $value;
    }

    public function getDynamicPropertyValue($name)
    {
        if (! array_key_exists($name, $this->dynamicPropertyValues)) {
            throw new \Exception("Object " . get_class($this) . " has no property named '$name'");
        }

        return $this->dynamicPropertyValues[$name];
    }

    public function __call($name, $arguments)
    {
        if (substr( $name, 0, 3 ) === "get") {
            return $this->executeGet($this->extractPropertyName($name));
        }
        if (substr( $name, 0, 3 ) === "set") {
            $this->executeSet($this->extractPropertyName($name), $arguments[0]);
            return;
        }
        throw new \Exception("Object " . get_class($this) . " has no method named '$name'");
    }

    private function extractPropertyName($getterSetterName)
    {
        return lcfirst(substr($getterSetterName, 3, strlen($getterSetterName) - 3));
    }

    private function executeGet($property)
    {
        return $this->getDynamicPropertyValue($property);
    }

    private function executeSet($property, $value)
    {
        $this->setDynamicPropertyValue($property, $value);
    }

}