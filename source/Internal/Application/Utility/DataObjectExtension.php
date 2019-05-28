<?php


namespace OxidEsales\EshopCommunity\Internal\Application\Utility;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class DataObjectExtension extends Extension implements ExtensionInterface, CompilerPassInterface
{

    private $propertiesConfig;

    public function load(array $configs, ContainerBuilder $container)
    {
        $this->propertiesConfig = $configs;
    }

    public function getAlias()
    {
        return 'oxid_dataobject_extension';
    }

    private function getDaoKeys() {

        if ($this->propertiesConfig == null) {
            return [];
        }
        $daosAssociativeArray = [];
        foreach ($this->propertiesConfig as $configArray)
            foreach ($configArray as $propertiesArray) {
                $daosAssociativeArray[$propertiesArray['daokey']] = "does not matter";
        }
        return array_keys($daosAssociativeArray);
    }

    private function getAdditionalProperties(string $daoKey) {

        $additionalProperties = [];
        foreach ($this->propertiesConfig as $configArray)
            foreach ($configArray as $propertiesArray) {
                if ($propertiesArray['daokey'] != $daoKey) {
                    continue;
                }
                foreach ($propertiesArray as $column => $type) {
                    if ($column == 'daokey') {
                        continue;
                    }
                    $additionalProperties[$column] = $type;
                }
        }
        return $additionalProperties;
    }

    public function process(ContainerBuilder $container)
    {
        foreach ($this->getDaoKeys() as $daoKey) {
            $daoDefinition = $container->getDefinition($daoKey);
            foreach ($this->getAdditionalProperties($daoKey) as $propertyName => $type) {
                $daoDefinition->addMethodCall('addProperty', [$propertyName, $type]);
            }
        }
    }
}