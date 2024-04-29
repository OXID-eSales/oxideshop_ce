<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Multilanguage;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Core\UtilsObject;
use oxRegistry;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/Helpers/LanguageMainHelper.php';

abstract class MultilanguageTestCase extends TestCase
{
    protected $originalLanguageArray = null;
    protected $originalBaseLanguageId = null;
    protected $languageMain = null;

    /**
     * Fixture setUp.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->originalLanguageArray = $this->getLanguageMain()->getLanguages();
        $this->originalBaseLanguageId = oxRegistry::getLang()->getBaseLanguage();
    }

    /**
    * Fixture tearDown.
    */
    protected function tearDown(): void
    {
        oxRegistry::getLang()->setBaseLanguage($this->originalBaseLanguageId);
        $this->storeLanguageConfiguration($this->originalLanguageArray);
        $this->updateViews();

        parent::tearDown();
    }

    /**
     * Test helper for test preparation.
     * Add given count of new languages.
     *
     * @param int $count
     *
     * @return int
     */
    protected function prepare(int $count = 9)
    {
        $languageId = 0;
        for ($i = 0; $i < $count; $i++) {
            $languageName = chr(97 + $i) . chr(97 + $i);
            $languageId = $this->insertLanguage($languageName);
        }
        //we need a fresh instance of language object in registry,
        //otherwise stale data is used for language abbreviations.
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Language::class, null);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\TableViewNameGenerator::class, null);

        $this->updateViews();

        return $languageId;
    }

    /**
     * Test helper to insert a new language with given id.
     *
     * @param int $languageId
     *
     * @return integer
     */
    protected function insertLanguage($languageId)
    {
        $languages = $this->getLanguageMain()->getLanguages();
        $baseId = $this->getLanguageMain()->getAvailableLangBaseId();
        $sort = $baseId * 100;

        $languages['params'][$languageId] = array('baseId' => $baseId,
                                                  'active' => 1,
                                                  'sort'   => $sort);

        $languages['lang'][$languageId] = $languageId;
        $languages['urls'][$baseId]     = '';
        $languages['sslUrls'][$baseId]  = '';
        $this->getLanguageMain()->setLanguageData($languages);

        $this->storeLanguageConfiguration($languages);

        if (!$this->getLanguageMain()->checkMultilangFieldsExistsInDb($languageId)) {
            $this->getLanguageMain()->addNewMultilangFieldsToDb();
        }

        return $baseId;
    }

    /**
     * Test helper for saving language configuration.
     *
     * @param array $languages
     */
    protected function storeLanguageConfiguration($languages)
    {
        Registry::getConfig()->saveShopConfVar('aarr', 'aLanguageParams', $languages['params']);
        Registry::getConfig()->saveShopConfVar('aarr', 'aLanguages', $languages['lang']);
        Registry::getConfig()->saveShopConfVar('arr', 'aLanguageURLs', $languages['urls']);
        Registry::getConfig()->saveShopConfVar('arr', 'aLanguageSSLURLs', $languages['sslUrls']);
    }

    /**
     * Test helper to trigger view update.
     */
    protected function updateViews()
    {
        $oMeta = oxNew('oxDbMetaDataHandler');
        $oMeta->updateViews();
    }

    /**
     * Getter for LanguageMainHelper proxy class.
     *
     * @return object
     */
    protected function getLanguageMain()
    {
        if (is_null($this->languageMain)) {
            $this->languageMain = $this->getProxyClass('LanguageMainHelper');
            $this->languageMain->render();
        }
        return $this->languageMain;
    }

    public function getProxyClassName($superClassName)
    {
        if (strpos($superClassName,'\\')===false) {
            $superClassName = strtolower($superClassName);
        }
        $superClassName = \OxidEsales\Eshop\Core\Registry::get(UtilsObject::class)->getClassName($superClassName);
        $escapedSuperClassName = str_replace('\\', '_', $superClassName);
        $proxyClassName = "{$escapedSuperClassName}Proxy";

        if (!class_exists($proxyClassName, false)) {
            $class = "
                class $proxyClassName extends $superClassName
                {
                    public function __call(\$function, \$args)
                    {
                        \$function = str_replace('UNIT', '_', \$function);
                        if(method_exists(\$this,\$function)){
                            return call_user_func_array(array(&\$this, \$function),  \$args);
                        }else{
                            throw new Exception('Method '.\$function.' in class '.get_class(\$this).' does not exist');
                        }
                    }
                    public function setNonPublicVar(\$name, \$value)
                    {
                        \$this->\$name = \$value;
                    }
                    public function getNonPublicVar(\$name)
                    {
                        return \$this->\$name;
                    }
                }";
            eval($class);
        }

        return $proxyClassName;
    }

    public function getProxyClass($superClassName, array $params = null)
    {
        $proxyClassName = $this->getProxyClassName($superClassName);
        if (!empty($params)) {
            // Create an instance using Reflection, because constructor has parameters
            $class = new ReflectionClass($proxyClassName);
            $instance = $class->newInstanceArgs($params);
        } else {
            $instance = new $proxyClassName();
        }

        return $instance;
    }
}
