<?php


namespace OxidEsales\EshopCommunity\Internal\Common\Dao;

use OxidEsales\EshopCommunity\Internal\Common\DataObject\DynamicDataObject;

/**
 * Trait DynamicDataObjectDaoTrait
 *
 * This trait manages dynamic properties on data access objects. It can be
 * included directly into some DAO class; or it can be used by subclassing
 * the DynamicDataObjectDAO class (which just includes this trait).
 *
 * The main purpose is to add new properties via the addProperty() method.
 * This is done by the DataObjectExtension, which reads information in the
 * services.yaml files of modules and then sets this information on the
 * objects. This trait expects the DAO to have a property called mapper,
 * that in itself uses the DynamicDataMapperTrait.
 *
 * Each property has a type. Allowed types are int, integer, bool, boolean,
 * float, double, real, string and timestamp.
 *
 * Additionally to this handling of dynamic properties, this trait also
 * works as an object factory for the dynamic object. This is done via the
 * create() method. For this to work the DAO needs to know the DataObject
 * class it neads to create. This class needs to be set using addObjectClass().
 *
 *
 * @package OxidEsales\EshopCommunity\Internal\Common\Dao
 */
trait DynamicDataObjectDaoTrait
{
    private $objectClass = null;

    public function addObjectClass(string $objectClass)
    {
        if (! is_subclass_of($objectClass, DynamicDataObject::class)) {
            throw new \Exception("Object class needs to be an instance of DynamicDataObject!");
        }
        $this->objectClass = $objectClass;
    }

    public function addProperty(string $name, string $type):void
    {
        $this->mapper->addProperty($name, $type);
    }

    public function create()
    {
        if ($this->objectClass === null)
        {
            throw new \Exception("Object type is not defined for DAO " . get_class($this));
        }

        /** @var DynamicDataObject $object */
        $object = new $this->objectClass();

        foreach ($this->mapper->getDynamicProperties() as $propertyName => $propertyType)
        {
            $object->configureDynamicProperty($propertyName);
        }

        return $object;

    }

    public function isDynamicDataObject($class) {

         foreach(class_uses($class) as $trait) {
             if ($trait == DynamicDataObject::class) {
                 return true;
             }
             $parentClass = get_parent_class($class);
             if ($parentClass === false) {
                 return false;
             }
             return $this->isDynamicDataObject($parentClass);
         }
         
         return false;
    }

}