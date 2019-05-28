<?php


namespace OxidEsales\EshopCommunity\Internal\Common\DataMapper;


use Doctrine\DBAL\Query\QueryBuilder;
use OxidEsales\EshopCommunity\Internal\Common\DataObject\DynamicDataObjectTrait;

trait DynamicDataMapperTrait
{
    private $dynamicProperties = [];

    public function addProperty($name, $type)
    {
        $this->dynamicProperties[$name] = $type;
    }

    public function getDynamicProperties()
    {
        return $this->dynamicProperties;
    }

    public function mapDynamicProperties($object, $data)
    {
        foreach ($this->dynamicProperties as $propertyName => $propertyType)
        {
            if (array_key_exists(strtoupper($propertyName), $data)) {
                $value = $data[strtoupper($propertyName)];
                $type = $this->dynamicProperties[$propertyName];
                /** @var DynamicDataObjectTrait $object */
                $object->setDynamicPropertyValue($propertyName, $this->castValue($value, $type));
            }
        }
        return $object;
    }

    private function castValue($value, $type) {
        if ($value === null) {
            return null;
        }
        switch ($type){
            case 'int':    return (int) $value;
            case 'integer': return (int) $value;
            case 'bool': return (bool) $value;
            case 'boolean': return (bool) $value;
            case 'float': return (float) $value;
            case 'double': return (float) $value;
            case 'real': return (float) $value;
            case 'string': return (string) $value;
            case 'timestamp': return (string) $value;
            default: throw new \Exception("Type $type not known for casting db values.");
        }
    }

    public function getDataDynamicProperties($object)
    {
        $result = [];
        foreach ($this->dynamicProperties as $propertyName => $propertyType)
        {
            $result[strtoupper($propertyName)] = $object->getDynamicPropertyValue($propertyName);
        }
        return $result;
    }

    public function setDynamicProperties(QueryBuilder $queryBuilder)
    {
        foreach ($this->dynamicProperties as $propertyName => $propertyType) {
            $queryBuilder->set(strtoupper($propertyName), ':' . strtoupper($propertyName));
        }
        return $queryBuilder;
    }

}