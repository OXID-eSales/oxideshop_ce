# Change Log for OXID eShop Community Edition Core Component

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [7.0.0] - Unreleased

### Added
- Event:
  - `Internal\Framework\Module\Configuration\Event\ModuleConfigurationChangedEvent`

### Fixed
- Invalidate module cache on module settings/configuration change
- Fix deprecation warnings

### Removed
- Backwards compatibility name support for 'attribute' controller. Please use the full namespace. [PR-907](https://github.com/OXID-eSales/oxideshop_ce/pull/907)

## [7.0.0-rc.4] - 2022-11-23

### Added
- Event:
  - `Internal\Framework\Module\Configuration\Event\ModuleClassExtensionChainChangedEvent`

### Fixed
- Fix prepared statements in GUI Setup
- Fix deprecation warnings
- Fix notices during FormatDateLogic modifier handling of empty values
- Fix notices in ``Order::getBillCountry`` and ``Order::getDelCountry`` methods [PR-906](https://github.com/OXID-eSales/oxideshop_ce/pull/906)
- Fix warning during language sorting [PR-904](https://github.com/OXID-eSales/oxideshop_ce/pull/904)

## [7.0.0-rc.3] - 2022-11-04

### Added
- Configuration parameter `oxid_esales.templating.engine_autoescapes_html` that delegates HTML-escaping to templating engine
  (when set to true, `Core\Field` will contain non-escaped HTML special characters)
- Methods:
  - `Internal\Transition\Utility\BasicContextInterface::getTemplateCacheDirectory()`

### Removed
- RSS functionality
- Support for Smarty-specific keys in `metadata.php` moved to the `oxid-esales/smarty-component`:
  - `blocks`
  - `smartyPluginDirectories`
  - `templates`
- Google Webfonts usage in offline.html [PR-900](https://github.com/OXID-eSales/oxideshop_ce/pull/900)
- Services:
  - `oxid_esales.command.apply_modules_configuration_command`
  - `oxid_esales.templating.template.loader`
- Commands:
  - `ApplyModulesConfigurationCommand` moved to another repository
- Interfaces:
  - `Internal\Framework\Module\Configuration\Cache\ShopConfigurationCacheInterface`
  - `Internal\Framework\Module\Configuration\Dao\ShopConfigurationExtenderInterface`
  - `Internal\Framework\Module\Configuration\Dao\ShopEnvironmentConfigurationDaoInterface`
  - `Internal\Framework\Module\Configuration\DataMapper\ProjectConfigurationDataMapperInterface`
  - `Internal\Framework\Module\Configuration\DataMapper\ShopConfigurationDataMapperInterface`
  - `Internal\Framework\Module\Setup\Handler\ModuleConfigurationHandlerInterface`
  - `Internal\Framework\Module\Setup\Service\ModuleConfigurationHandlingServiceInterface`
  - `Internal\Framework\Smarty\Module\TemplateExtension\TemplateBlockLoaderBridgeInterface`
  - `Internal\Framework\Templating\Loader\TemplateLoaderInterface`
  - `Internal\Framework\Event\ShopAwareInterface`
  - `Internal\Framework\Module\Setup\Service\ModuleServicesActivationServiceInterface`
- Classes:
  - `Application\Model\SmartyRenderer`
  - `Core\Module\ModuleSmartyPluginDirectoryRepository`
  - `Core\Module\ModuleTemplateBlockRepository`
  - `Core\Smarty\Plugin\EmosAdapter`
  - `Core\Smarty\Plugin\EmosItem`
  - `Core\Smarty\Plugin\Emos`
  - `Internal\Framework\Module\Configuration\Cache\ClassPropertyShopConfigurationCache`
  - `Internal\Framework\Module\Configuration\Dao\ShopEnvironmentConfigurationDao`
  - `Internal\Framework\Module\Configuration\Dao\ShopEnvironmentConfigurationExtender`
  - `Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\SmartyPluginDirectoriesDataMapper`
  - `Internal\Framework\Module\Configuration\DataMapper\ProjectConfigurationDataMapper`
  - `Internal\Framework\Module\Configuration\DataMapper\ShopConfigurationDataMapper`
  - `Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\SmartyPluginDirectory`
  - `Internal\Framework\Module\Setup\Exception\ModuleSetupException`
  - `Internal\Framework\Smarty\Module\TemplateExtension\TemplateBlockLoaderBridgeInterface`
  - `Internal\Framework\Smarty\Module\TemplateExtension\TemplateBlockLoaderBridge`
  - `Internal\Framework\Console\AbstractShopAwareCommand` use `Symfony\Component\Console\Command\Command`
  - `Internal\Framework\DIContainer\DataObject\DIServiceWrapper`
  - `Internal\Framework\DIContainer\DataObject\DICallWrapper`
  - `Internal\Framework\Event\ShopAwareEventDispatcher` use `Symfony\Component\EventDispatcher\EventDispatcher`
  - `Internal\Framework\Event\ShopAwareServiceTrait`
  - `Internal\Framework\Event\AbstractShopAwareEventSubscriber`
  - `Internal\Framework\Module\Setup\Service\ModuleServicesActivationService`
- Methods:
  - `Application\Controller\Admin\AdminDetailsController::processEditValue()`
  - `Core\Email::_getSmarty()`
  - `Core\Field::`
    - `convertToFormattedDbDate()`
    - `convertToPseudoHtml()`
  - `Core\Module\Module::getSmartyPluginDirectories()`
  - `Core\SystemRequirements::getMissingTemplateBlocks`
  - `Core\Utils::resetTemplateCache()`
  - `Core\UtilsView::_fillCommonSmartyProperties()`
  - `Core\UtilsView::_smartyCompileCheck()`
  - `Core\UtilsView::_smartyDefaultTemplateHandler()`
  - `Core\UtilsView::addActiveThemeId()`
  - `Core\UtilsView::getShopSmartyPluginDirectories()`
  - `Core\UtilsView::getSmarty()`
  - `Core\UtilsView::getSmartyDir()`
  - `Core\UtilsView::getSmartyPluginDirectories()`
  - `Core\UtilsView::getTemplateBlocks()`
  - `Core\UtilsView::getTemplateCompileId()`
  - `Core\UtilsView::getTemplateDirs()`
  - `Core\UtilsView::parseThroughSmarty()`
  - `Core\UtilsView::setTemplateDir()`
  - `oxfunctions::ox_get_secure()`
  - `oxfunctions::ox_get_template()`
  - `oxfunctions::ox_get_timestamp()`
  - `oxfunctions::ox_get_trusted()`
  - `Internal\Framework\DIContainer\DataObject\DIConfigWrapper::hasService()`
  - `Internal\Framework\DIContainer\DataObject\DIConfigWrapper::getService()`
  - `Internal\Framework\DIContainer\DataObject\DIConfigWrapper::addOrUpdateService()`
  - `Internal\Framework\DIContainer\DataObject\DIConfigWrapper::checkServices()`
  - `Internal\Framework\DIContainer\DataObject\DIConfigWrapper::checkServiceClassesCanBeLoaded()`
  - `Internal\Framework\DIContainer\DataObject\DIConfigWrapper::getServices()`
- Properties:
  - `Application\Controller\Admin\AdminDetailsController::$_oEditor`
  - `Core\UtilsView::$_aTemplateDir`

### Fixed
- Partly revert `Core\Autoload\ModuleAutoload`
- PHP-warning if varselid is not an array [PR-897](https://github.com/OXID-eSales/oxideshop_ce/pull/897)

### Changed
- Shop configuration files structure
- Change `BaseModel::assignRecord()` function visibility form public to protected
- Only module DI services, event subscribers, commands etc. of the modules which are active in the current shop will be loaded,
  no need to implement `ShopAwareInterface` or extend `ShopAwareEventDispatcher`, `AbstractShopAwareCommand`.

### Deprecated
- Methods
  - `Internal\Framework\Templating\TemplateRendererBridgeInterface::setEngine()`
  - `Internal\Framework\Templating\TemplateRendererBridgeInterface::getEngine()`

## [7.0.0-rc.2] - 2022-08-15

### Added
- Twig templates multi inheritance for modules
- Shop configuration to define loading order for module templates

### Changed
- Update Symfony components to v6
- Cache storage format in `Internal\Framework\Module\Cache\FilesystemModuleCache` to `JSON`
- Show 404 error but not redirect to index on accessing not existing product [PR-871](https://github.com/OXID-eSales/oxideshop_ce/pull/871)
- Switched to default PDO result set types when using PHP8.1
- System requirements:
- PHP must have access to a secure source of randomness [See more](https://www.php.net/manual/en/function.random-bytes.php). 
- Respond with 404 error code on controller or method miss [PR-715](https://github.com/OXID-eSales/oxideshop_ce/pull/715)
- Change type of default value for iIndex parameter in ``OxidProfessionalServices\Bergspezl\Model\Article::getZoomPictureUrl`` [PR-893](https://github.com/OXID-eSales/oxideshop_ce/pull/893)
- Switched to templating-engine agnostic names in Controller templates (e.g. `Controller::$_sThisTemplate = 'page/content'` instead of `'page/content.tpl'`)
- Don't store module controllers in database
- Store information about active modules state in the module configuration (yml files), not in the database (`activeModules` config option is completely removed)
- Read module class extensions chain direct from the shop configuration (yml files). Don't store active module chain in the database (`aModules` config option is completely removed)
- Don't store module settings in database
- Change OXID eShop Community Edition license

### Fixed
- Ensure \OxidEsales\EshopCommunity\Application\Model\NewsSubscribed::getOptInStatus int result type
- Increase the size of OXCONTENT fields in oxcontents table [#0006831](https://bugs.oxid-esales.com/view.php?id=6831)
- Change broken "Requirements" links to current shop documentation [PR-877](https://github.com/OXID-eSales/oxideshop_ce/pull/877)
- Fix static cache variable usage in `Model\Article::getCategory` [PR-803](https://github.com/OXID-eSales/oxideshop_ce/pull/803)
- Fix not initialized category case possible in `Model\Article::getCategory` [PR-803](https://github.com/OXID-eSales/oxideshop_ce/pull/803)
- Performance improved on possible payment list by removing unnecessary table join [PR-895](https://github.com/OXID-eSales/oxideshop_ce/pull/895)
- Increased the size of smtp password input field [PR-898](https://github.com/OXID-eSales/oxideshop_ce/pull/898)

### Removed
- PHP v7 support
- Composer v1 support
- Support for NAME constants in Event classes
- Module re-activation on module settings change
- Interfaces:
  - `Internal\Framework\Templating\Resolver\TemplateNameResolverInterface`
- Classes:
  - `Core\PasswordSaltGenerator`
  - `Internal\Transition\Utility\FallbackTokenGenerator`
  - `OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler\ModulePathHandler`
  - `OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler\ModuleVersionHandler`
  - `OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler\ShopConfigurationClassExtensionsHandler`
  - `OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler\ShopConfigurationEventsHandler`
  - `OxidEsales\EshopCommunity\Core\Module\ModuleCache`
  - `OxidEsales\EshopCommunity\Core\Module\ModuleInstaller`
  - `Internal\Framework\Templating\Resolver\LegacyTemplateNameResolver`
  - `OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ClassExtensionChainBridgeInterface`
  - `OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ClassExtensionChainBridge`
  - `OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ClassExtensionChainServiceInterface`
  - `OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ClassExtensionChainService`
  - `OxidEsales\Eshop\Core\Routing\ModuleControllerMapProvider`
  - `OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler\ControllersModuleSettingHandler`
  - `OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\SettingDao`
  - `OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\SettingDaoBridge`
  - `OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler\SettingModuleSettingHandler`
- Methods:
  - `Application\Model\User::getUtilsObjectInstance()`
  - `Internal\Framework\Templating\TemplateEngineInterface::getDefaultFileExtension()`
  - `Internal\Framework\Templating\Loader\TemplateLoaderInteface::getPath()`
  - `Application\Model\UserPayment`
    - `load()`
    - `insert()`
  - `OxidEsales\EshopCommunity\Core\Email::sendBackupMail()`
  - `OxidEsales\EshopCommunity\Core\Email::addAttachment()`
  - `OxidEsales\EshopCommunity\Core\Email::addEmbeddedImage()`
  - `OxidEsales\EshopCommunity\Core\Email::getAttachments()`
  - `OxidEsales\EshopCommunity\Core\Email::clearAttachments()`
  - `OxidEsales\EshopCommunity\Core\Email::$_aAttachments`
  - `OxidEsales\EshopCommunity\Core\Module\Module`
    - `loadByDir()`
    - `getIdFromExtension()`
  - `OxidEsales\EshopCommunity\Core\Module\ModuleChainsGenerator`
    - `cleanModuleFromClassChain()`
  - `OxidEsales\EshopCommunity\Core\Module\ModuleList`
    - `getList`
    - `getModulesWithExtendedClass`
    - `getActiveModuleInfo`
    - `getDisabledModuleInfo`
    - `getModuleVersions`
    - `getModules`
    - `getDisabledModules`
    - `getModulePaths`
    - `getModuleEvents`
    - `extractModulePaths`
    - `diffModuleArrays`
    - `buildModuleChains`
    - `getModuleConfigParametersByKey`
    - `getModulesFromDir`
    - `getModuleIds`
    - `sortModules`
    - `isVendorDir`
  - `OxidEsales\EshopCommunity\Core\Config`
    - `getModulesWithExtendedClass`
  - `OxidEsales\EshopCommunity\Core\UtilsView`
    - `getActiveModuleInfo`
  - `OxidEsales\EshopCommunity\Core\ViewConfig`
    - `isModuleActive`
  - `OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface::setActive`
  - `OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface::setDeactivated`
  - `OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration::isConfigured`
  - `OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration::setConfigured`
- Services:
  - `utility.context.log_file_path`
  - `utility.context.log_level:`
  - `utility.context.admin_log_file_path`
  - `oxid_esales.module.setup.path_module_setting_handler`
  - `oxid_esales.module.setup.version_module_setting_handler`
  - `oxid_esales.module.setup.shop_configuration_class_extension_handler`
  - `oxid_esales.module.setup.events_module_setting_handler`
  - `oxid_esales.module.setup.class_extension_chain_service`
  - `oxid_esales.module.setup.controllers_module_setting_handler`
  - `OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\SettingDaoInterface`
  - `OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\SettingDaoBridgeInterface`
  - `oxid_esales.module.setup.shop_module_setting_module_setting_handler`
- Config options:
  - `aModuleExtensions`
  - `aModuleVersions`
  - `aModulePaths`
  - `aModuleEvents`
  - `aModuleControllers`
  - `activeModules`
  - `aModules`
- Constants:
  - `OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting`
    - `MODULE_CLASS_EXTENSIONS`
    - `MODULE_VERSIONS`
    - `MODULE_PATHS`
    - `MODULE_EVENTS`
    - `MODULE_CONTROLLERS`
    - `ACTIVE_MODULES`
    - `MODULE_CLASS_EXTENSIONS_CHAIN`
  - `OxidEsales\EshopCommunity\Core\Module\ModuleList`
    - `MODULE_KEY_PATHS`
    - `MODULE_KEY_EVENTS`
    - `MODULE_KEY_VERSIONS`
    - `MODULE_KEY_TEMPLATES`
    - `MODULE_KEY_EXTENSIONS`
    - `MODULE_KEY_CONTROLLERS`
  - `OxidEsales\EshopCommunity\Core\Config::OXMODULE_MODULE_PREFIX`

### Added
- WebP image format support with an option to convert other format images automatically

### Deprecated
- `OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleSettingBridgeInterface`
- `OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleSettingBridge`

## [7.0.0-rc.1] - 2021-07-07

### Added

- Support for MySQL v8.0
- Console Shop installer commands:
  - `oe:setup:shop`
  - `oe:setup:demodata`
  - `oe:admin:create-user`
  - `oe:license:add`
  - `oe:license:clear`
- Console module installer commands:
  - `oe:module:install`
  - `oe:module:uninstall`
- Export for newsletter recipients list
- Saving tracking URL per Shipping Method
- Interface for file upload management `Internal\Framework\FileSystem\ImageHandlerInterface`
- Psalm integration [PR-766](https://github.com/OXID-eSales/oxideshop_ce/pull/766)
- Support for single language map file [PR-449](https://github.com/OXID-eSales/oxideshop_ce/pull/449)
- Performance improvement in `Core\Field` [PR-771](https://github.com/OXID-eSales/oxideshop_ce/pull/771)
- Logging for:
  - Permission check in Setup [PR-764](https://github.com/OXID-eSales/oxideshop_ce/pull/764)
  - Shop validation [PR-733](https://github.com/OXID-eSales/oxideshop_ce/pull/733)

### Changed

- Update symfony components to version 5
- Storage of module source files:
  - don't copy files to the `source/modules` directory
  - copy assets to the shop `out` directory
  - change translations loading source for themes
  - use relative path for module template file path in `metadata.php`
  - use new module configuration parameter `moduleSource`
- Rename deprecated methods prefixed with a single underscore
- Extend `oxNew` signature for static analysis [PR-744](https://github.com/OXID-eSales/oxideshop_ce/pull/744)
- Change default value for `oxpublic` field in `oxuserbaskets`
- Optimize configuration loading [PR-787](https://github.com/OXID-eSales/oxideshop_ce/pull/787)
- Update a list of bots (`aRobots` array in config) [PR-853](https://github.com/OXID-eSales/oxideshop_ce/pull/853)
- Generation of currency URL [PR-750](https://github.com/OXID-eSales/oxideshop_ce/pull/750)
- `autocomplete` for SMTP fields in admin template [PR-794](https://github.com/OXID-eSales/oxideshop_ce/pull/794)
- Move functionality to `Utility` classes:
  - hash service to `Internal\Utility\Hash`
  - email validation service to `Internal\Utility\Email\EmailValidatorServiceInterface`
- Behaviour of `Core\UtilsFile::processFiles()` with enabled configuration for alternative image URL
- Database fields:
  - `oxvalue` field in `oxconfig` table changed from `blob` to `text`
  - `oxvalue` field in `oxuserpayments` table changed from `blob` to `text`
- Templates and blocks:
  - `source/Application/views/admin/tpl/shop_license.tpl`
  - `source/Application/views/admin/tpl/shop_main.tpl` [PR-730](https://github.com/OXID-eSales/oxideshop_ce/pull/730):
    - `admin_shop_main_leftform`
    - `admin_shop_main_rightform`

### Removed

- Support:
  - MySQL v5.5, v5.6
  - Database encoding
  - Metadata versions 1, 1.1, 1.2 [see list](#660---2020-11-10)
  - Module `source-directory` and `target-directory` in `composer.json` [see list](#670---2021-03-25)
  - `blacklist-filter` for composer type `oxideshop-module` [see list](#660---2020-11-10)
  - Two stars `**` in composer `blacklist-filter`
  - `Path` parameter in `moduleConfiguration` [see list](#670---2021-03-25)
  - `UNIT...` prefixes in tested method calls

- Feature:
  - Credit card [see list](#651---2020-02-25)
  - Suggest (Recommend Product) [see list](#654---2020-04-21)
  - News [see list](#656---2020-07-16)
  - LDAP login

- Functionality:
  - Newsletter email management [see list](#690---unreleased)
  - Betanote [see list](#654---2020-04-21)
  - Console commands for module configuration management:
    - `oe:module:install-configuration`
    - `oe:module:uninstall-configuration`
  - PHP version checker
  - MySQL version check in Setup
  - Resetting the PHP `error_reporting()` level in
    the `ShopControl` [PR-728](https://github.com/OXID-eSales/oxideshop_ce/pull/728)
  - Smarty plugin `assign_adv` with corresponding `TemplateLogic` service
  - Usage of concatenation in translation files [PR-729](https://github.com/OXID-eSales/oxideshop_ce/pull/729)
  - Version information in copyright string [PR-813](https://github.com/OXID-eSales/oxideshop_ce/pull/813)
  - Old update procedure related check [PR-829](https://github.com/OXID-eSales/oxideshop_ce/pull/829)
    
- Class:
  - `Application\Controller\Admin\DynEconda`
  - `Application\Controller\Admin\DynamicInterface`
  - `Application\Controller\Admin\DynamicScreenController`
  - `Application\Controller\Admin\DynamicScreenList`
  - `Application\Controller\Admin\DynamicScreenLocal`
  - `Application\Model\FileCheckerResult`
  - `Application\Model\FileCheckerResult`
  - `Application\Model\FileChecker`
  - `Application\Model\FileChecker`
  - `Conf`
  - `Core\Contract\IModuleValidator`
  - `Core\Edition\EditionPathProvider`
  - `Core\Edition\EditionRootPathProvider`
  - `Core\Edition\EditionSelector`
  - `Core\Exception\LanguageException`
  - `Core\Module\ModuleExtensionsCleaner`
  - `Core\Module\ModuleMetadataValidator`
  - `Core\Module\ModuleSmartyPluginDirectoryValidator`
  - `Core\Module\ModuleTemplateBlockContentReader`
  - `Core\Module\ModuleTemplateBlockPathFormatter`
  - `Core\Module\ModuleTemplatePathCalculator`
  - `Core\Module\ModuleTranslationPathFinder`
  - `Core\Module\ModuleValidatorFactory`
  - `Core\Routing\Module\ClassProviderStorage`
  - `Internal\Framework\Module\Setup\Handler\TemplatesModuleSettingHandler`
  - `Internal\Transition\Adapter\TemplateLogic\AssignAdvancedLogic`

- Method:
  - `Application\Component\UserComponent::_changeUser_noRedirect()`
  - `Application\Component\Widget::getCompareItemsCnt()`
  - `Application\Controller\Admin\AdminController::_getShopVersionNr()`
  - `Application\Controller\Admin\AdminController::getServiceUrl()`
  - `Application\Controller\Admin\AdminDetailsController::_generateTextEditor()`
  - `Application\Controller\Admin\AdminDetailsController::_getPlainEditor()`
  - `Application\Controller\Admin\DiagnosticsMain::_checkOxidFiles()`
  - `Application\Controller\Admin\DiagnosticsMain::_getFileCheckReport()`
  - `Application\Controller\Admin\DiagnosticsMain::_getFilesToCheck()`
  - `Application\Controller\Admin\ModuleConfiguration::_getModuleForConfigVars()`
  - `Application\Controller\Admin\ModuleConfiguration::_loadMetadataConfVars()`
  - `Application\Controller\Admin\NavigationTree::_addDynLinks()`
  - `Application\Controller\Admin\NavigationTree::_checkDynFile()`
  - `Application\Controller\Admin\NavigationTree::_getDynMenuLang()`
  - `Application\Controller\Admin\NavigationTree::_getDynMenuUrl()`
  - `Application\Controller\Admin\OrderList::storno()`
  - `Application\Controller\Admin\ShopRdfa::submitUrl()`
  - `Application\Model::_getAmountPriceList()`
  - `Application\Model\Article::_assignPersistentParam()`
  - `Application\Model\Article::getPersParams()`
  - `Application\Model\Basket::_addedNewItem()`
  - `Application\Model\Basket::_canSaveBasket()`
  - `Application\Model\Diagnostics::getFileCheckerExtensionList()`
  - `Application\Model\Diagnostics::getFileCheckerPathList()`
  - `Application\Model\Diagnostics::setFileCheckerExtensionList()`
  - `Application\Model\Diagnostics::setFileCheckerPathList()`
  - `Application\Model\Order::getFormattedeliveryCost()`
  - `Application\Model\User::_getShopSelect()`
  - `Application\Model\User::_ldapLogin()`
  - `Application\Model\UserPayment::getPaymentKey()`
  - `Application\Model\Voucher::_getCategoryDiscoutValue()`
  - `Application\Model\Voucher::_getGenericDiscoutValue()`
  - `Application\Model\Voucher::_getProductDiscoutValue()`
  - `Core\Base::getConfig()`
  - `Core\Base::setConfig()`
  - `Core\Config::getActiveViewsNames()`
  - `Core\Config::getDecodeValueQuery()`
  - `Core\Config::getEdition()`
  - `Core\Config::getModuleTemplatePathCalculator()`
  - `Core\Config::getModulesDir()`
  - `Core\Config::getRequestEscapedParameter()`
  - `Core\Config::getRequestParameter()`
  - `Core\Config::getRequestRawParameter()`
  - `Core\Config::getRevision()`
  - `Core\Config::getVersion()`
  - `Core\Config::parseModuleChains()`
  - `Core\Controller\BaseController::getClassName()`
  - `Core\Controller\BaseController::getRevision()`
  - `Core\Controller\BaseController::setClassName()`
  - `Core\Email::getConfig()`
  - `Core\Email::setConfig()`
  - `Core\Exception\ExceptionHandler::displayOfflinePage()`
  - `Core\Exception\ExceptionHandler::getLogFileName()`
  - `Core\Exception\ExceptionHandler::setIDebug()`
  - `Core\Exception\ExceptionHandler::setLogFileName()`
  - `Core\Exception\ExceptionHandler::writeExceptionToLog()`
  - `Core\Exception\StandardException::debugOut()`
  - `Core\Exception\StandardException::getLogFileName()`
  - `Core\Exception\StandardException::setLogFileName()`
  - `Core\Language::_appendModuleLangFiles()`
  - `Core\Language::_getActiveModuleInfo()`
  - `Core\Language::_getDisabledModuleInfo()`
  - `Core\Language::getModuleTranslationPathFinder()`
  - `Core\Module::getConfigBlDoNotDisableModuleOnError()`
  - `Core\Module\Module::getMetaDataVersion()`
  - `Core\Module\Module::getMetadataPath()`
  - `Core\Module\Module::getModuleFullPath()`
  - `Core\Module\Module::getModulePath()`
  - `Core\Module\ModuleChainsGenerator::disableModule()`
  - `Core\Module\ModuleChainsGenerator::filterInactiveExtensions()`
  - `Core\Module\ModuleChainsGenerator::getDisabledModuleIds()`
  - `Core\Module\ModuleChainsGenerator::getModuleDirectoryByModuleId()`
  - `Core\Module\ModuleList::getModuleTemplates()`
  - `Core\Module\ModuleSmartyPluginDirectories::add()`
  - `Core\Module\ModuleSmartyPluginDirectories::getWithRelativePath()`
  - `Core\Module\ModuleSmartyPluginDirectories::remove()`
  - `Core\OnlineCaller::_castExceptionAndWriteToLog()`
  - `Core\OnlineModuleVersionNotifier::_getModules()`
  - `Core\ShopControl::_getFrontendStartController()`
  - `Core\ShopControl::_getStartController()`
  - `Core\ShopControl::_stopMonitor()`
  - `Core\SystemEventHandler::getConfig()`
  - `Core\SystemRequirements::checkMysqlVersion()`
  - `Core\SystemRequirements::checkPhpVersion()`
  - `Core\SystemRequirements::getConfig()`
  - `Core\Utils::getRemoteCachePath()`
  - `Core\Utils::logger()`
  - `Core\Utils::redirectOffline()`
  - `Core\Utils::writeToLog()`
  - `Core\UtilsObject::getModuleVar()`
  - `Core\UtilsObject::getShopId()`
  - `Core\UtilsObject::resetModuleVars()`
  - `Core\UtilsObject::setModuleVar()`
  - `Core\UtilsView::_getTemplateBlock()`
  - `Core\ViewConfig::getConfig()`
  - `Core\ViewConfig::getServiceUrl()`
  - `Core\ViewConfig::getSession()`
  - `Core\ViewConfig::getSessionId()`
  - `bootstrap.php::writeToLog()`
  - `cmpart()`
  - `getDb()`
  - `getRequestUrl()`
  - `getSession()`
  - `getStr()`
  - `overridablefunctions.php::getViewName()`
  - `setSession()`

- Property:
  - `Application\Controller\Admin\AdminController::$_sShopVersion`
  - `Application\Controller\Admin\NavigationTree::$_sDynIncludeUrl`
  - `Application\Model\Article::$_aPersistParam`
  - `Application\Model\Delivery::_isForArticle`
  - `Application\Model\Diagnostics::$_aFileCheckerExtensionList`
  - `Application\Model\Diagnostics::$_aFileCheckerPathList`
  - `Application\Model\OrderArticle::$_aOrderCache`
  - `Application\Model\UserPayment::$_sPaymentKey`
  - `Core\Base::$_oConfig`
  - `Core\Base::$_oRights`
  - `Core\Base::$_oSession`
  - `Core\Config::$sConfigKey`
  - `Core\Email::$SMTP_PORT`
  - `Core\Email::$Version`
  - `Core\Email::$_oConfig`
  - `Core\Exception\ExceptionHandler::$_sFileName`
  - `Core\Exception\StandardException::$_sFileName`
  - `Core\Language::$_aActiveModuleInfo`
  - `Core\Language::$_aDisabledModuleInfo`
  - `Core\Language::$moduleTranslationPathFinder`
  - `Core\Session::$_blStarted`
  - `Core\ShopControl::$_blHandlerSet`
  - `Core\WidgetControl::$_blHandlerSet`

- Constant:
  - `Application\Controller\Admin\ShopController::SHOP_ID`
  - `Core\Config::DEFAULT_CONFIG_KEY`
  - `Internal\Framework\Config\DataObject\ShopConfigurationSetting::MODULE_TEMPLATES`

- Configuration parameter:
  - `blDoNotDisableModuleOnError`
  - `iUtfMode`
  - `sConfigKey`
  - `sOXIDPHP`

- Template:
  - `source/Application/views/admin/tpl/dyn_econda.tpl`
  - `source/Application/views/admin/tpl/dynscreen.tpl`
  - `source/Application/views/admin/tpl/dynscreen_list.tpl`
  - `source/Application/views/admin/tpl/dynscreen_local.tpl`
  - `source/Application/views/admin/tpl/version_checker_result.tpl`

- Language constant:
  - `DYNSCREEN_LIST_SERVICE`
  - `DYNSCREEN_LOCAL_TEXT`
  - `DYNSCREEN_LOCAL_TITLE`
  - `DYNSCREEN_TITLE`
  - `DYN_ECONDA_ACTIVE`
  - `DYN_ECONDA_ATTENTION`
  - `DYN_ECONDA_COPY_FILE`
  - `ERROR_MESSAGE_CONNECTION_NOLDAPBIND`
  - `ERROR_MESSAGE_CONNECTION_NOLDAP`
  - `LOAD_DYN_CONTENT_NOTICE`
  - `MOD_PHP_VERSION`
  - `NAVIGATION_NEWVERSIONAVAILABLE`
  - `NEWSLETTER_SELECTION_SENDNEWS`
  - `NEWSLETTER_SELECTION_USEDGROUP`
  - `NEWSLETTER_SEND_SEND1`
  - `NEWSLETTER_SEND_SEND2`
  - `NEWSLETTER_SEND_TITLE`
  - `SHOP_SYSTEM_LDAP`
  - `SYSREQ_MYSQL_VERSION`
  - `SYSREQ_PHP_VERSION`
  - `TOOLTIPS_NEWNEWSLETTER`
  - `USER_MAIN_LDAP`
  - `mxdynscreenlocal`

- Data in `initial_data.sql`:
  - `admin-user` entry
  - `theme:flow` default values

### Fixed

- Throw exception in `getLanguageAbbr` method if no abbreviation is available by specific
  id [PR-802](https://github.com/OXID-eSales/oxideshop_ce/pull/802)
- Checking if multilanguage base table from configuration exists, before trying to generate its
  views [PR-754](https://github.com/OXID-eSales/oxideshop_ce/pull/754)
- Fix not working actions and promotions [#0005526](https://bugs.oxid-esales.com/view.php?id=5526)
- Refactor calls to deprecated `getStr` [PR-758](https://github.com/OXID-eSales/oxideshop_ce/pull/758)
- Fixe usages of deprecated methods `getConfig`
  and `getSession` [PR-721](https://github.com/OXID-eSales/oxideshop_ce/pull/721)
- Improve `oxseo::OXOBJECTID` index to fit current
  queries [PR-466](https://github.com/OXID-eSales/oxideshop_ce/pull/466)
- Replace BC classes with namespaced ones [PR-772](https://github.com/OXID-eSales/oxideshop_ce/pull/772)
- Ensure `out/pictures/generated` directory exists [PR-789](https://github.com/OXID-eSales/oxideshop_ce/pull/789)
- Improve gitignore
  - [PR-808](https://github.com/OXID-eSales/oxideshop_ce/pull/808)
  - [PR-827](https://github.com/OXID-eSales/oxideshop_ce/pull/827)
- Fix special chars escape problem
  in `simplexml::addChild` [PR-793](https://github.com/OXID-eSales/oxideshop_ce/pull/793)
- Fix new version check url protocol [PR-852](https://github.com/OXID-eSales/oxideshop_ce/pull/852)
- Add timestamp for CSS and JS files included from
  module [#0005746](https://bugs.oxid-esales.com/view.php?id=5746) [PR-493](https://github.com/OXID-eSales/oxideshop_ce/pull/493)
- Improve various docs, variable and other coding style problems:
  - [PR-741](https://github.com/OXID-eSales/oxideshop_ce/pull/741)
  - [PR-748](https://github.com/OXID-eSales/oxideshop_ce/pull/748)
  - [PR-756](https://github.com/OXID-eSales/oxideshop_ce/pull/756)
  - [PR-761](https://github.com/OXID-eSales/oxideshop_ce/pull/761)
  - [PR-765](https://github.com/OXID-eSales/oxideshop_ce/pull/765)
  - [PR-773](https://github.com/OXID-eSales/oxideshop_ce/pull/773)
  - [PR-774](https://github.com/OXID-eSales/oxideshop_ce/pull/774)
  - [PR-775](https://github.com/OXID-eSales/oxideshop_ce/pull/775)
  - [PR-776](https://github.com/OXID-eSales/oxideshop_ce/pull/776)
  - [PR-777](https://github.com/OXID-eSales/oxideshop_ce/pull/777)
  - [PR-778](https://github.com/OXID-eSales/oxideshop_ce/pull/778)
  - [PR-779](https://github.com/OXID-eSales/oxideshop_ce/pull/779)
  - [PR-780](https://github.com/OXID-eSales/oxideshop_ce/pull/780)
  - [PR-790](https://github.com/OXID-eSales/oxideshop_ce/pull/790)
  - [PR-809](https://github.com/OXID-eSales/oxideshop_ce/pull/809)
  - [PR-823](https://github.com/OXID-eSales/oxideshop_ce/pull/823)
  - [PR-824](https://github.com/OXID-eSales/oxideshop_ce/pull/824)
  - [PR-825](https://github.com/OXID-eSales/oxideshop_ce/pull/825)
  - [PR-834](https://github.com/OXID-eSales/oxideshop_ce/pull/834)
  - [PR-842](https://github.com/OXID-eSales/oxideshop_ce/pull/842)

## [6.13.0] - 2022-12-01

### Deprecated
- RSS functionality
- Interfaces:
  - `Internal\Framework\Module\Setup\Handler\ModuleConfigurationHandlerInterface`
  - `Internal\Framework\Module\Setup\Service\ModuleConfigurationHandlingServiceInterface`
  - `Internal\Framework\Module\Setup\Bridge\ClassExtensionChainBridgeInterface`
  - `Internal\Framework\Module\Setup\Service\ClassExtensionChainServiceInterface`
  - `Internal\Framework\Event\ShopAwareInterface`
  - `Internal\Framework\Module\Setup\Service\ModuleServicesActivationServiceInterface`
  - `Internal\Framework\Templating\Loader\TemplateLoaderInterface`
- Classes:
  - `Core\Module\ModuleSmartyPluginDirectoryRepository`
  - `Core\Module\ModuleTemplateBlockRepository`
  - `Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\SmartyPluginDirectoriesDataMapper`
  - `Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\SmartyPluginDirectory`
  - `Internal\Framework\Module\Setup\Bridge\ClassExtensionChainBridge`
  - `Internal\Framework\Module\Setup\Service\ClassExtensionChainService`
  - `Internal\Framework\Console\AbstractShopAwareCommand`
  - `Internal\Framework\DIContainer\DataObject\DIServiceWrapper`
  - `Internal\Framework\DIContainer\DataObject\DICallWrapper`
  - `Internal\Framework\Event\ShopAwareEventDispatcher`
  - `Internal\Framework\Event\ShopAwareServiceTrait`
  - `Internal\Framework\Event\AbstractShopAwareEventSubscriber`
  - `Internal\Framework\Module\Setup\Service\ModuleServicesActivationService`
- Services:
    - `oxid_esales.module.setup.class_extension_chain_service`
    - `oxid_esales.module.setup.events_module_setting_handler`
    - `oxid_esales.module.setup.path_module_setting_handler`
    - `oxid_esales.module.setup.shop_configuration_class_extension_handler`
    - `oxid_esales.module.setup.version_module_setting_handler`
    - `oxid_esales.templating.template.loader`
- Constants:
  - `Internal\Framework\Config\DataObject\ShopConfigurationSetting::`
    - `MODULE_CLASS_EXTENSIONS`
    - `MODULE_VERSIONS`
    - `MODULE_PATHS`
    - `MODULE_EVENTS`
    - `MODULE_CONTROLLERS`
    - `ACTIVE_MODULES`
    - `MODULE_SMARTY_PLUGIN_DIRECTORIES`
    - `MODULE_CLASS_EXTENSIONS_CHAIN`
- Config options:
  - `aModuleExtensions`
  - `aModuleVersions`
  - `aModulePaths`
  - `aModuleEvents`
  - `activeModules`
  - `aModules`
  - `aModuleControllers`
- Methods:
  - `Application\Controller\Admin\AdminDetailsController::processEditValue()`
  - `Application\Model\Actions::getLongDesc()`
  - `Application\Model\Article::getLongDesc()`
  - `Application\Model\Category::getLongDesc()`
  - `Core\Field::`
    - `convertToFormattedDbDate()`
    - `convertToPseudoHtml()`
  - `Core\Module\Module:getSmartyPluginDirectories()`
  - `Core\SystemRequirements::getMissingTemplateBlocks()`
  - `Core\Utils::resetTemplateCache()`
  - `Core\UtilsView::`
    - `addActiveThemeId()`
    - `getRenderedContent()`
    - `getSmartyDir()`
    - `getTemplateBlocks()`
    - `getTemplateCompileId()`
    - `getTemplateDirs()`
    - `setTemplateDir()`
  - `Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration::`
    - `isConfigured()`
    - `setConfigured()`
  - `Internal\Framework\Module\State\ModuleStateServiceInterface::`
    - `setActive()`
    - `setDeactivated()`
  - `Internal\Framework\DIContainer\DataObject\DIConfigWrapper::`
    - `hasService()`
    - `getService()`
    - `addOrUpdateService()`
    - `checkServices()`
    - `checkServiceClassesCanBeLoaded()`
    - `getServices()`
  - `Internal\Framework\Templating\Loader\TemplateLoaderInterface::exists()`
- Properties:
  - `Application\Controller\Admin\AdminDetailsController::$_oEditor`
  - `Core\UtilsView::$_aTemplateDir`

### Fixed
- Error in chrome accessing navigation admin frame in javascript via top.navigation
- Deleting Categories with more than one SEO Entry [#0007362](https://bugs.oxid-esales.com/view.php?id=7362)

### Changed
- Avoid using Google Fonts API in offline page

## [6.12.0] - 2022-08-15

### Changed
- Change OXID eShop Community Edition license

### Deprecated
- Classes:
    - `OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\SettingDao`
    - `OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\SettingDaoBridge`
    - `OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler\SettingModuleSettingHandler`
- Services:
    - `OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\SettingDaoInterface`
    - `OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\SettingDaoBridgeInterface`
    - `oxid_esales.module.setup.shop_module_setting_module_setting_handler`

### Fixed
- PHP 8.1 added to php version validation as supported [PR-899](https://github.com/OXID-eSales/oxideshop_ce/pull/899)

## [6.11.0] - 2022-07-20

### Added
- PHP 8.1 support
- Interfaces:
  - `Internal\Domain\Authentication\Generator\RandomTokenGeneratorInterface`
  - `Internal\Domain\Authentication\Bridge\RandomTokenGeneratorBridgeInterface`
  - `Internal\Framework\SystemRequirements\SystemSecurityCheckerInterface`
- Console command for clearing the cache - `oe:cache:clear`
- Method ` OxidEsales\EshopCommunity\Application\Model\User::sendRegistrationEmail()`

### Changed
- Updated Symfony components
- Vat code checking service url protocol changed to https [PR-890](https://github.com/OXID-eSales/oxideshop_ce/pull/890)

### Deprecated
- Support for NAME constants in Event classes (`*Event::NAME`)
- Validation for `metaDataFilePath` in metadata
- Methods:
  - `Application\Model\User::getUtilsObjectInstance()`
  - `OxidEsales\EshopCommunity\Core\Email::sendBackupMail()`
  - `OxidEsales\EshopCommunity\Core\Email::addAttachment()`
  - `OxidEsales\EshopCommunity\Core\Email::addEmbeddedImage()`
  - `OxidEsales\EshopCommunity\Core\Email::getAttachments()`
  - `OxidEsales\EshopCommunity\Core\Email::clearAttachments()`
  - `Internal\Framework\Templating\Loader\TemplateLoaderInterface::getPath()`
  - `Internal\Framework\Templating\TemplateEngineInterface::getDefaultFileExtension()`
- Classes:
  - `Internal\Framework\Templating\Resolver\LegacyTemplateNameResolver`
  - `Internal\Framework\Templating\Resolver\TemplateNameResolver`
- Services:
  - `utility.context.admin_log_file_path`
  - `utility.context.log_file_path`
  - `utility.context.log_level`
  - `oxid_esales.module.metadata.datamapper.metadatamapper`
- Interfaces:
  - `Internal\Framework\Module\MetaData\Validator\MetaDataSchemaValidatorInterface`
  - `Internal\Framework\Module\MetaData\Validator\SettingValidatorInterface`
  - `Internal\Framework\Module\MetaData\Dao\MetaDataSchemataProviderInterface`
  - `Internal\Framework\Templating\Resolver\TemplateNameResolverInterface`
- Properties:
  - `OxidEsales\EshopCommunity\Core\Email::$_aAttachments`

### Fixed
- Rare fatal appearing in modified but not recalculated baskets [PR-891](https://github.com/OXID-eSales/oxideshop_ce/pull/891)

## [6.10.3] - 2022-07-18

### Fixed
- Deadlock in oxseo table when deleting categories in backend [#0006762](https://bugs.oxid-esales.com/view.php?id=6762)

## [6.10.2] - 2022-05-17

### Fixed
- Error in coupon series during misconfiguration of value [PR-887](https://github.com/OXID-eSales/oxideshop_ce/pull/887)
- Error in multishop case, triggering return of wrong values from cookies [#0007306](https://bugs.oxid-esales.com/view.php?id=7306) [PR-888](https://github.com/OXID-eSales/oxideshop_ce/pull/888)
- Extend default VAT check service response errors list [#0007308](https://bugs.oxid-esales.com/view.php?id=7308) [PR-889](https://github.com/OXID-eSales/oxideshop_ce/pull/889)
- Fix cascading for deletion in Manufacturer- and Vendor-Model [#0006880](https://bugs.oxid-esales.com/view.php?id=6880)
- Add CSRF check to user creation [#0007059](https://bugs.oxid-esales.com/view.php?id=7059)
- Error in module chain generation on installing legacy module after module with namespaces
- Allow OXID-eSales plugins by default.

## [6.10.1] - 2022-02-02

### Fixed
- Updated doc links to the latest release [PR-877](https://github.com/OXID-eSales/oxideshop_ce/pull/877)
- Selection window to add an item to a discount is empty [#0007204](https://bugs.oxid-esales.com/view.php?id=7204)
- Improved reset password token generation algorithm [#0006394](https://bugs.oxid-esales.com/view.php?id=6394)

## [6.10.0] - 2021-12-02

### Added
- Configuration option `deactivateSmartyForCmsContent` to prevent Smarty from processing content added via CMS
- Method `OxidEsales\EshopCommunity\Core\Model\BaseModel::getRawFieldData()`
- Allows throwing own exception messages in admin login [PR-882](https://github.com/OXID-eSales/oxideshop_ce/pull/882)

### Changed
- Update `symfony/expression-language` component
- Execution of `smarty_function_oxeval` will be prevented with `deactivateSmartyForCmsContent` switched on
- Visibility of `OxidEsales\EshopCommunity\Core\Email::getRenderer` changed from private to protected [PR-846](https://github.com/OXID-eSales/oxideshop_ce/pull/846)

### Fixed
- Text message on `Payment Methods > RDFa` tab
- Docblock and other coding style fixes:
    - [PR-876](https://github.com/OXID-eSales/oxideshop_ce/pull/876)
    - [PR-885](https://github.com/OXID-eSales/oxideshop_ce/pull/885)
- Fix db fetchmode in SeoEncoder::loadFromDb [PR-879](https://github.com/OXID-eSales/oxideshop_ce/pull/879)
- Fix admin login box position and sizing [PR-880](https://github.com/OXID-eSales/oxideshop_ce/pull/880)
- Improve utf8 email handling [#0007275](https://bugs.oxid-esales.com/view.php?id=7275)
- Module configuration can't process theme-specific template extensions

### Removed
- Support for PHP 7.3

### Deprecated
- `Core\Model\BaseModel` methods:
  - `assignRecord()`
  - `getRecordByQuery()`

## [6.9.1] - 2022-05-17

### Fixed
- Text message on `Payment Methods > RDFa` tab
- Docblock and other coding style fixes:
    - [PR-876](https://github.com/OXID-eSales/oxideshop_ce/pull/876)
- Allow OXID-eSales plugins by default.

## [6.9.0] - 2021-07-27

### Added
- Added Northern Ireland due to Brexit regulations [PR-872](https://github.com/OXID-eSales/oxideshop_ce/pull/872)

### Fixed
- Use not extended OxidEsales\Eshop\Core\Module\Module in module chain generator error case. [PR-863](https://github.com/OXID-eSales/oxideshop_ce/pull/863)
- Remove duplicated/redundant error message in case of InputException in UserComponent. [PR-713](https://github.com/OXID-eSales/oxideshop_ce/pull/713)
- Docblock in SystemInfoController. [PR-864](https://github.com/OXID-eSales/oxideshop_ce/pull/864)
- Typo in Model/ActionList::fetchExistsActivePromotion method SQL. [PR-867](https://github.com/OXID-eSales/oxideshop_ce/pull/867)
- SettingChangedEvent dispatch moved to module setting dao, so its triggered during module settings change in admin. [PR-860](https://github.com/OXID-eSales/oxideshop_ce/pull/860)
- SQL performance issue in oxDeliverySetList::_getFilterSelect [#0006247](https://bugs.oxid-esales.com/view.php?id=6247) [PR-865](https://github.com/OXID-eSales/oxideshop_ce/pull/865)
- Fix css style of password type fields in admin [#0007249](https://bugs.oxid-esales.com/view.php?id=7249) [PR-873](https://github.com/OXID-eSales/oxideshop_ce/pull/873)
- Fix password checking script in admin module settings [#0007249](https://bugs.oxid-esales.com/view.php?id=7249) [PR-874](https://github.com/OXID-eSales/oxideshop_ce/pull/874)
- Fix moduleSettings existence requirement in environment file [#0007241](https://bugs.oxid-esales.com/view.php?id=7241) [PR-868](https://github.com/OXID-eSales/oxideshop_ce/pull/868)
- Fix cached delivery rules reuse [PR-869](https://github.com/OXID-eSales/oxideshop_ce/pull/869)

### Deprecated
- Management of Newsletter emails:
    - `Application\Controller\Admin\NewsletterList`
    - `Application\Controller\Admin\NewsletterMain`
    - `Application\Controller\Admin\NewsletterPlain`
    - `Application\Controller\Admin\NewsletterPreview`
    - `Application\Controller\Admin\NewsletterSelectionAjax`
    - `Application\Controller\Admin\NewsletterSelection`
    - `Application\Controller\Admin\NewsletterSend`
    - `Application\Model\Newsletter`
    - `Application\Controller\Admin\NewsletterMain::save()`
    - `Core\Email::sendNewsletterMail()`
    - `Application\Controller\AccountNewsletterController::_blNewsletter`
    - Language constants
        - `NEWSLETTER_DONE_GOTONEWSLETTE`
        - `NEWSLETTER_DONE_NEWSSEND`
        - `NEWSLETTER_DONE_TITLE`
        - `NEWSLETTER_MAIN_MODEL`
        - `NEWSLETTER_PLAIN_TEXT`
        - `NEWSLETTER_PREVIEW_HTML`
        - `NEWSLETTER_PREVIEW_PLAINTEXT`
        - `NEWSLETTER_SELECTION_SELMAILRESAVER`
        - `NEWSLETTER_SUBJECT`
        - `tbclnewsletter_main`
        - `tbclnewsletter_plain`
        - `tbclnewsletter_preview`
        - `tbclnewsletter_selection`
- Method
    - `Core\UtilsFile::_copyFile()`
    - `Core\UtilsFile::_moveImage()`

## [6.8.0] - 2021-04-13

### Added
- Support PHP 8.0

### Removed
- Support PHP 7.1 and 7.2

## [6.7.2] - 2021-07-21

### Changed
- Update PHPMailer to v6.5.0

### Fixed
- Usage of `tpl` request parameter in `Application\Component\UserComponent`
- Input parsing in `assign_adv` Smarty plugin

## [6.7.1] - 2021-04-13

### Fixed
- Fix order remark create date reset during review saving in admin with German time format [#0007217](https://bugs.oxid-esales.com/view.php?id=7217) [PR-857](https://github.com/OXID-eSales/oxideshop_ce/pull/857)

## [6.7.0] - 2021-03-25

### Deprecated
- Support for Module source-directory and target-directory in composer.json
    - Method:
        - `OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage::getPackageSourcePath()`
        - `OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage::getTargetDirectory()`
        - `OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage::setTargetDirectory()`
        - `OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage::getSourceDirectory()`
- `OxidEsales\EshopCommunity\Core\Config::getModulesDir()`
- `Path` parameter from moduleConfiguration:
    - `OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration::getPath()`
    - `OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration::setPath()`
- Console commands for module configuration management:
    - `oe:module:install-configuration`
    - `oe:module:uninstall-configuration`


### Fixed
- Reset voucher reservations after logout [PR-845](https://github.com/OXID-eSales/oxideshop_ce/pull/845)
- Remove wrong docblock on OrderArticle model assign method [PR-850](https://github.com/OXID-eSales/oxideshop_ce/pull/850)
- Disable browser autocomplete for user passwords in admin interface [PR-851](https://github.com/OXID-eSales/oxideshop_ce/pull/851)
- Do not reuse already initialized widget control on widget loading [PR-757](https://github.com/OXID-eSales/oxideshop_ce/pull/757)
- Module template cache is not cleared on deactivation
- Wrong voucher calculation - discount sharing between user's baskets [#0006854](https://bugs.oxid-esales.com/view.php?id=6854)

## [6.6.0] - 2020-11-10

### Added
- Add remove module configuration command
- Support symfony's optional class for named services
- Add index to oxuser:oxrights to fix performance issue for oxuser table

### Changed
- Streamlined lang files for work with translation platforms [PR-833](https://github.com/OXID-eSales/oxideshop_ce/pull/833)
- Improve English translations [PR-843](https://github.com/OXID-eSales/oxideshop_ce/pull/843)

### Deprecated
- Use of two stars (**) for filter strings in modules composer blacklist-filter.
- Econda smarty plugin:
    - Classes:
        - `OxidEsales\Eshop\Core\Smarty\Plugin\Emos`
        - `OxidEsales\Eshop\Core\Smarty\Plugin\EmosAdapter`
        - `OxidEsales\Eshop\Core\Smarty\Plugin\EmosItem`
- Language Constants:
    - `NAVIGATION_NEWVERSIONAVAILABLE`
    - `UPDATEAPP_DIRNOTDELETED_WARNING`
- `\OxidEsales\EshopCommunity\Core\Module\ModuleTemplatePathCalculator`
- `\OxidEsales\EshopCommunity\Core\Config::getModuleTemplatePathCalculator()`
- `\OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler\TemplatesModuleSettingHandler`
- `\OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting::MODULE_TEMPLATES`
- `\OxidEsales\EshopCommunity\Core\Module\ModuleTemplateBlockContentReader`
- `\OxidEsales\EshopCommunity\Core\Module\ModuleTemplateBlockPathFormatter`
- Config option `aModuleTemplates`
- Support of metadata version 1, 1.1 and 1.2
    - Class:
        - `OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ClassWithoutNamespace`
        - `OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\ClassesWithoutNamespaceDataMapper`
        - `OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler\ShopConfigurationClassesWithoutNamespaceHandler`
        - `OxidEsales\EshopCommunity\Core\Autoload\ModuleAutoload`
    - Method:
        - `OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration::addClassWithoutNamespace`
        - `OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration::getClassesWithoutNamespace`
        - `OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration::hasClassWithoutNamespaces`
        - `OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataNormalizer::lowerCaseFileClassesNames`
        - `OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator\EventsValidator::isNamespacedClass`
        - `OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\EventSubscriber\DispatchLegacyEventsSubscriber::invalidateModuleCache`
    - Constant:
        -  `OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting::MODULE_CLASSES_WITHOUT_NAMESPACES`
- `OxidEsales\EshopCommunity\Core\Module\ModuleTranslationPathFinder`
- `OxidEsales\EshopCommunity\Core\Language`:
    - Property:
        - `$_aActiveModuleInfo`
        - `$_aDisabledModuleInfo`
        - `$moduleTranslationPathFinder`
    - Method:
        - `_appendModuleLangFiles()`
        - `_getActiveModuleInfo()`
        - `_getDisabledModuleInfo()`
        - `getModuleTranslationPathFinder()`
- `OxidEsales\EshopCommunity\Core\Utils::getRemoteCachePath()`
- `OxidEsales\EshopCommunity\Core\ViewConfig::getServiceUrl()`
- `OxidEsales\EshopCommunity\Application\Controller\Admin\NavigationTree::$_sDynIncludeUrl`
- Language constants
    - `DYN_ECONDA_ACTIVE`
    - `DYN_ECONDA_ATTENTION`
    - `DYN_ECONDA_COPY_FILE`
    - `DYNSCREEN_TITLE`
    - `DYNSCREEN_LIST_SERVICE`
    - `LOAD_DYN_CONTENT_NOTICE`
    - `DYNSCREEN_LOCAL_TITLE`
    - `DYNSCREEN_LOCAL_TEXT`
    - `mxdynscreenlocal`
- Module blacklist-filter functionality
    - `OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage::getBlackListFilters()`
    - `OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage::setBlackListFilters()`

### Fixed
- Impossible save module settings from the admin area if module has services.yaml [#0007180](https://bugs.oxid-esales.com/view.php?id=7180)
- Collect unique article ids for seo links update [#0007167](https://bugs.oxid-esales.com/view.php?id=7167)
- Fix OXID in _set tables has wrong collation [#0007084](https://bugs.oxid-esales.com/view.php?id=7084)

## [6.5.6] - 2020-07-16

### Added
- Add module migrations
- New language constants in `Application/translations/[de/en]/lang.php` [PR-821](https://github.com/OXID-eSales/oxideshop_ce/pull/821)
    - `PAGE_TITLE_START`
- PHPCodeSniffer and PSR12 Coding guidelines

### Renamed
- Price alert is renamed to wished price

### Fixed
- MariaDB column default values must be trimmed [PR-796](https://github.com/OXID-eSales/oxideshop_ce/pull/796)
- Keep track of action type in Article model onChange method
- Module class extensions cannot be sorted in backend [#0007116](https://bugs.oxid-esales.com/view.php?id=7116)
- Take oxsort field into account for showing cross-seling articles [PR-738](https://github.com/OXID-eSales/oxideshop_ce/pull/738)
- Fix second page of article list load via SEO [#0007014](https://bugs.oxid-esales.com/view.php?id=7014) [PR-740](https://github.com/OXID-eSales/oxideshop_ce/pull/740)
- Fix using special symbols in smtp password [PR-806](https://github.com/OXID-eSales/oxideshop_ce/pull/806)
- Fix docblocks
    - [PR-815](https://github.com/OXID-eSales/oxideshop_ce/pull/815)
    - [PR-816](https://github.com/OXID-eSales/oxideshop_ce/pull/816)
- Fix Shop deletes savedbasket from all subshops when discarding reservation basket [#0007136](https://bugs.oxid-esales.com/view.php?id=7136)
- Fix a bug with composer uninstall if module has custom source  [#0007137](https://bugs.oxid-esales.com/view.php?id=7137)
- Fix composer update changes module state in yaml  [#0007130](https://bugs.oxid-esales.com/view.php?id=7130)

### Changed
- jQuery UI updated to v1.12.1
- Imports in `generated_services.yaml` changed from absolute paths to relative
- Updated PHPMailer to v6.1.6
- Make module deactivation possible if module service is missing in generated_services.yaml

### Deprecated
- News feature:
    - Classes:
        - `Application/Component/NewsComponent`
        - `Application/Controller/Admin/AdminNews`
        - `Application/Controller/Admin/NewsList`
        - `Application/Controller/Admin/NewsMainAjax`
        - `Application/Controller/Admin/NewsMain`
        - `Application/Controller/Admin/NewsText`
        - `Application/Controller/NewsController`
        - `Application/Model/NewsList`
        - `Application/Model/News`
    - Config options:
        - `blFooterShowNews`
        - `bl_perfLoadNewsOnlyStart`
        - `bl_perfLoadNews`
        - `sCntOfNewsLoaded`
    - Language Constants:
        - `LATEST_NEWS_AND_UPDATES_AT`
        - `LATEST_NEWS_NOACTIVENEWS`
        - `NEWS_LIST_MENUITEM`
        - `NEWS_LIST_MENUSUBITEM`
        - `NEWS_LIST_SHORTTEXT`
        - `NEWS_LIST_TITLE`
        - `NEWS_MAIN_NOTSHOWFORGROUP`
        - `NEWS_MAIN_SHORTDESC`
        - `NEWS_MAIN_SHOWFORGROUP`
        - `NEWS`
        - `ORDER_REMARK_NEWS`
        - `PAGE_TITLE_NEWS`
        - `SHOP_CONFIG_CNTOFNEWS`
        - `SHOP_MALL_MALLINHERIT_OXNEWS`
        - `SHOP_PERF_LOADNEWSONLYSTART`
        - `SHOP_PERF_LOADNEWS`
        - `USER_REMARK_NEWS`
        - `mxnews`
        - `tbclnews_main`
        - `tbclnews_mall`
        - `tbclnews_text`
        - `TOOLTIPS_NEWNEWS`

### Security
- [Bug 7134](https://bugs.oxid-esales.com/view.php?id=7134)

## [6.5.5] - 2020-05-05

### Deprecated
- Methods starting with underscore have been deprecated, these methods will be renamed

## [6.5.4] - 2020-04-21

### Deprecated
- Betanote:
    - Class: `OxidEsales\EshopCommunity\Application\Component\Widget\BetaNote`
    - Method: `OxidEsales\EshopCommunity\Core\Controller\BaseController::showBetaNote()`
- Suggest (Recommend Product) feature:
    - Class `OxidEsales\EshopCommunity\Application\Controller\SuggestController`
    - Method:
        - `OxidEsales\EshopCommunity\Core\ViewConfig::getShowSuggest`
        - `OxidEsales\EshopCommunity\Core\Email::sendSuggestMail`
    - Property:
        - `OxidEsales\EshopCommunity\Core\Email::$_sSuggestTemplate`
        - `OxidEsales\EshopCommunity\Core\Email::$_sSuggestTemplatePlain`
    - Language Constants:
        - `CARD_TO`
        - `CHECK`
        - `MESSAGE_ENTER_YOUR_ADDRESS_AND_MESSAGE`
        - `MESSAGE_RECOMMEND_CLICK_ON_SEND`
        - `PRODUCT_POST_CARD_FROM`
        - `RECOMMENDED_PRODUCTS`
        - `SHOP_CONFIG_ALLOW_SUGGEST_ARTICLE`
        - `HELP_SHOP_CONFIG_ALLOW_SUGGEST_ARTICLE`

### Fixed
- Change visibility of Session::setSessionCookie to protected for overwriting possibility [PR-785](https://github.com/OXID-eSales/oxideshop_ce/pull/785)
- Use cache directory from config file for the container cache: [#0007111](https://bugs.oxid-esales.com/view.php?id=7111)
- Get the correct path to admin menu file: [#0007126](https://bugs.oxid-esales.com/view.php?id=7126)

## [6.5.3] - 2020-03-25

### Fixed
- Issue with module controllers validator

### Changed
- Option `blSessionUseCookies` is no longer used in the Session class

## [6.5.2] - 2020-03-16

### Deprecated
- `OxidEsales\EshopCommunity\Application\Model\Article::getDeliveryDate()` [PR-768](https://github.com/OXID-eSales/oxideshop_ce/pull/768)
- Language Constants:
    - `SYSREQ_MYSQL_VERSION`
### Fixed
- Issue with session ID regeneration on user registration
- Problem with guest account update during checkout: [#0007109](https://bugs.oxid-esales.com/view.php?id=7109)
### Removed
- `OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler\ShopConfigurationModuleSettingHandler`

## [6.5.1] - 2020-02-25

### Deprecated
- `OxidEsales\Eshop\Core\Config::getDecodeValueQuery()`
- `OxidEsales\Eshop\Core\Config::$sConfigKey`
- `OxidEsales\EshopCommunity\Core\Base::$_oRights`
- `OxidEsales\Eshop\Core\Config::DEFAULT_CONFIG_KEY`
- `Conf`
- `OxidEsales\Eshop\Core\Session::$_blStarted`
- `OxidEsales\Eshop\Application\Model\UserPayment::$_sPaymentKey`
- `OxidEsales\Eshop\Application\Model\UserPayment::getPaymentKey()`
- `OxidEsales\EshopCommunity\Core\SystemRequirements::checkMysqlVersion()`
- `OxidEsales\EshopCommunity\Core\SystemRequirements::checkPhpVersion()`
- `OxidEsales\EshopCommunity\Core\MailValidator`
- Language Constants:
    - `SYSREQ_PHP_VERSION`
    - `MOD_PHP_VERSION`
- Credit Card:
    - Class:
        - `OxidEsales\Eshop\Core\CreditCardValidator`
    - Function:
        - `OxidEsales\Eshop\Application\Controller\PaymentController::_filterDynData()`
        - `OxidEsales\Eshop\Application\Model\UserPayment::setStoreCreditCardInfo()`
        - `OxidEsales\Eshop\Application\Model\UserPayment::getStoreCreditCardInfo()`
        - `OxidEsales\Eshop\Application\Controller\PaymentController::getCreditYears()`
        - `OxidEsales\Eshop\Application\Controller\PaymentController::getDynDataFiltered()`
    - Property:
        - `OxidEsales\Eshop\Core\InputValidator::$_aRequiredCCFields`
        - `OxidEsales\Eshop\Core\InputValidator::$_aPossibleCCType`
        - `OxidEsales\Eshop\Application\Controller\PaymentController::$_blDynDataFiltered`
        - `OxidEsales\Eshop\Application\Model\UserPayment::$_blStoreCreditCardInfo`
        - `OxidEsales\Eshop\Application\Controller\PaymentController::$_aCreditYears`
    - Language Constants:
        - `CREDITCARD`
        - `PAYMENT_CREDITCARD`
        - `SHOP_CONFIG_STORECREDITCARDINFO`
        - `PAYMENT_RDFA_CREDITCARD`
        - `PAYMENT_RDFA_MASTERCARD`
        - `PAYMENT_RDFA_VISA`
        - `PAYMENT_RDFA_AMERICANEXPRESS`
        - `PAYMENT_RDFA_DINERSCLUB`
        - `PAYMENT_RDFA_DISCOVER`
        - `PAYMENT_RDFA_JCB`
        - `PAGE_CHECKOUT_PAYMENT_CREDITCARD`
        - `CARD_SECURITY_CODE_DESCRIPTION`
        - `HELP_SHOP_CONFIG_ATTENTION`
        - `CARD_MASTERCARD`
        - `CARD_SECURITY_CODE`
        - `CARD_VISA`

### Fixed
- Warnings in order discounts recalculation [PR-742](https://github.com/OXID-eSales/oxideshop_ce/pull/742)
- Require at least 3.4.26 DI component [PR-746](https://github.com/OXID-eSales/oxideshop_ce/pull/746)
- Fix return type annotation for `OxidEsales\EshopCommunity\Application\Model::load()` to `bool`
- Handle translated error message from validator in password change correctly [PR-731](https://github.com/OXID-eSales/oxideshop_ce/pull/731)
- Fix docblock and var name in NavigationController::_doStartUpChecks [PR-751](https://github.com/OXID-eSales/oxideshop_ce/pull/751)

### Added
- Support MariaDB (tested for MariaDB 10.4)
- Support PHP 7.3 and 7.4
- Utilizes Travis CI caching feature for faster builds
- Uninstall method for removing module
- Add possibility to overwrite the offline page [PR-755](https://github.com/OXID-eSales/oxideshop_ce/pull/755)
- Email validation extracted to service `OxidEsales\EshopCommunity\Internal\Domain\Email\EmailValidationService`
- Events:
    - `\OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\ServicesYamlConfigurationErrorEvent`
    - `\OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterAdminAjaxRequestProcessedEvent`
    - `\OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterRequestProcessedEvent`

## [6.5.0] - 2019-11-07

### Added
- oe-console command: oe:module:apply-configuration
- Added new parameter to `executeQuery` method in `SeoEncoder` which allows to pass prepared statements parameters
- `OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface`

### Changed
- Most of SELECT, DELETE, UPDATE and INSERT queries do use prepared statements
- Use 301(moved permanently) redirect on missing slash in the url - we had 302(moved temporary) earlier [PR-722](https://github.com/OXID-eSales/oxideshop_ce/pull/722)
- Updated jQuery library in admin panel to 3.4.1

### Deprecated
- `OxidEsales\EshopCommunity\Core\DatabaseProvider`
- `OxidEsales\EshopCommunity\Core\Database\Adapter\Doctrine\Database`
- `OxidEsales\EshopCommunity\Core\Database\Adapter\Doctrine\ResultSet`
- `OxidEsales\EshopCommunity\Core\Database\Adapter\DatabaseInterface`
- `OxidEsales\EshopCommunity\Core\Database\Adapter\ResultSetInterface`
- `OxidEsales\EshopCommunity\Application\Model\SmartyRenderer`
- `OxidEsales\EshopCommunity\Core\Email:$_oSmarty`
- `OxidEsales\EshopCommunity\Core\Email:_getSmarty()`
- `OxidEsales\EshopCommunity\Core\UtilsView:getSmarty()`
- `OxidEsales\EshopCommunity\Core\UtilsView:parseThroughSmarty()`
- `OxidEsales\EshopCommunity\Core\UtilsView:_fillCommonSmartyProperties()`
- `OxidEsales\EshopCommunity\Core\UtilsView:getSmartyPluginDirectories()`
- `OxidEsales\EshopCommunity\Core\UtilsView:getShopSmartyPluginDirectories()`
- `OxidEsales\EshopCommunity\Core\UtilsView:_smartyCompileCheck()`
- `OxidEsales\EshopCommunity\Core\UtilsView:_smartyDefaultTemplateHandler()`
- `oxfunctions:ox_get_template()`
- `oxfunctions:ox_get_timestamp()`
- `oxfunctions:ox_get_secure()`
- `oxfunctions:ox_get_trusted()`

### Removed
- Support of GD1 library dropped [PR-672](https://github.com/OXID-eSales/oxideshop_ce/pull/672)
- Not used files anymore:
    - source/xd_receiver.htm [PR-689](https://github.com/OXID-eSales/oxideshop_ce/pull/689)

### Fixed
- Metadata 1.2 support
- Fix issue with fetch_mode_changing. [Bug 6892](https://bugs.oxid-esales.com/view.php?id=6892)
- Improve gift registry search [#0006698](https://bugs.oxid-esales.com/view.php?id=6698)
- Fix admin query logging [#0006999](https://bugs.oxid-esales.com/view.php?id=6999). Information will be written to
  to `source/log/oxadmin.log`.
- Removed hardcoded "http://" in oxexturl field edit [#0006993](https://bugs.oxid-esales.com/view.php?id=6993) [PR-726](https://github.com/OXID-eSales/oxideshop_ce/pull/726)

## [6.4.0] - 2019-08-02

### Fixed
- Fixed return type in Basket::getDiscounts [PR-659](https://github.com/OXID-eSales/oxideshop_ce/pull/659)
- Remove unused variables, decrease complexity [PR-668](https://github.com/OXID-eSales/oxideshop_ce/pull/668)
- Cleanup return statement from ShopList model constructor [PR-677](https://github.com/OXID-eSales/oxideshop_ce/pull/677)
- Fix warning if discounts variable is not array [PR-678](https://github.com/OXID-eSales/oxideshop_ce/pull/678)
- Fix phpdoc types and set consistent returns in BaseController [PR-676](https://github.com/OXID-eSales/oxideshop_ce/pull/676)
- Fix checkIniSet method in SystemRequirements for php 7.2 [PR-681](https://github.com/OXID-eSales/oxideshop_ce/pull/681)
- Fixed bug maintenance mode when changing e-mail address as a guest [#0006965](https://bugs.oxid-esales.com/view.php?id=6965)
- Fixed bug no possibility to sort accessories of articles in backend [#0003609](https://bugs.oxid-esales.com/view.php?id=3609)
- Fix php 7.2 compatibility of tests.
- Fix Bank code validation bug in Direct Debit [#0006939](https://bugs.oxid-esales.com/view.php?id=6939)
- Incorrect default values from database-columns, if empty, on MariaDB [PR-709](https://github.com/OXID-eSales/oxideshop_ce/pull/709) [#0006914](https://bugs.oxid-esales.com/view.php?id=6914) [#0006888](https://bugs.oxid-esales.com/view.php?id=6888)
- Fix sql error in category sort ajax popup [PR-707](https://github.com/OXID-eSales/oxideshop_ce/pull/707) [#0006985](https://bugs.oxid-esales.com/view.php?id=6985)
- Use oxideshop.log in place of EXCEPTION_LOG in comments/translations [PR-708](https://github.com/OXID-eSales/oxideshop_ce/pull/708)
- Fixed the code to fit PSR-2 [PR-711](https://github.com/OXID-eSales/oxideshop_ce/pull/711)
- Improved form validation [#0006924](https://bugs.oxid-esales.com/view.php?id=6924)
- Fix typo in comment [PR-717](https://github.com/OXID-eSales/oxideshop_ce/pull/717) [PR-719](https://github.com/OXID-eSales/oxideshop_ce/pull/719)
- Remove unnecessary parameters in addErrorToDisplay function call in ForgetPasswordController [PR-716](https://github.com/OXID-eSales/oxideshop_ce/pull/716)

### Added
- New methods:
    - `OxidEsales\EshopCommunity\Core\Exception\ExceptionToDisplay::getValues` [PR-660](https://github.com/OXID-eSales/oxideshop_ce/pull/660)
    - `OxidEsales\EshopCommunity\Application\Model\Article::getStock` [PR-640](https://github.com/OXID-eSales/oxideshop_ce/pull/640)
    - `OxidEsales\EshopCommunity\Application\Controller\Admin::sortAccessoriesList()` [#0003609](https://bugs.oxid-esales.com/view.php?id=3609)
    - `OxidEsales\EshopCommunity\Application\Model\Article::getActionType`
    - `OxidEsales\EshopCommunity\Application\Model\Article::getStockStatusOnLoad`
    - `OxidEsales\EshopCommunity\Core\Base::dispatchEvent`
- Log a warnings for missused db method calls [PR-649](https://github.com/OXID-eSales/oxideshop_ce/pull/649)
- New blocks:
    - `admin_module_sortlist` in `admin/tpl/module_sortlist.tpl` [PR-534](https://github.com/OXID-eSales/oxideshop_ce/pull/534)
    - `admin_order_overview_info_items` in `admin/tpl/include/order_info.tpl` [PR-688](https://github.com/OXID-eSales/oxideshop_ce/pull/688/files)
    - `admin_order_overview_info_sumtotal` in `admin/tpl/include/order_info.tpl` [PR-688](https://github.com/OXID-eSales/oxideshop_ce/pull/688/files)
- Log missing translations [PR-520](https://github.com/OXID-eSales/oxideshop_ce/pull/520)
- New features:
    - Reset category filter [0002046](https://bugs.oxid-esales.com/view.php?id=2046)
    - OXID eShop console, which allows to register custom commands for modules and for components via `services.yaml`.
    - New command to activate module.
    - New command to deactivate module.
    - New oe-console command to install module configuration: oe:module:install-configuration
    - New parameter in config file to change database connection charset - `dbCharset` [PR-670](https://github.com/OXID-eSales/oxideshop_ce/pull/670)

- Events:
    - `\OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\BeforeModuleDeactivationEvent`
    - `\OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleActivationEvent`
    - `\OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleDeactivationEvent`
    - `\OxidEsales\EshopCommunity\Internal\Framework\Config\Event\ShopConfigurationChangedEvent`
    - `\OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Event\SettingChangedEvent`
    - `\OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterModelDeleteEvent`
    - `\OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterModelInsertEvent`
    - `\OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AfterModelUpdateEvent`
    - `\OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\AllCookiesRemovedEvent`
    - `\OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\ApplicationExitEvent`
    - `\OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\BasketChangedEvent`
    - `\OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\BeforeHeadersSendEvent`
    - `\OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\BeforeModelDeleteEvent`
    - `\OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\BeforeModelUpdateEvent`
    - `\OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\BeforeSessionStartEvent`
    - `\OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\ViewRenderedEvent`
    - `\OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeSettingChangedEvent`
- Interface:
    - `\OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface` as the new default
      for hashing passwords. See https://docs.oxid-esales.com/developer/en/6.2/project/password_hashing.html
- Constants
    - `\OxidEsales\EshopCommunity\Application\Model\User::USER_COOKIE_SALT`
- Directory
    - var/ directory, it contains files to which the application writes data during the course of its operation. Must be writable by the HTTP server and CLI user.

### Changed
- category_main form layout improvements [PR-585](https://github.com/OXID-eSales/oxideshop_ce/pull/585)
- Split config parameter initialization from application initialization [PR-628](https://github.com/OXID-eSales/oxideshop_ce/pull/628)
- Increase default quantity of productimages to 12 (from 7) [PR-514](https://github.com/OXID-eSales/oxideshop_ce/pull/514)
- Make adding template blocks more fast andn reliable [PR-580](https://github.com/OXID-eSales/oxideshop_ce/pull/580)
- Change email encoding to base64 [0006468](https://bugs.oxid-esales.com/view.php?id=6468) [PR-697](https://github.com/OXID-eSales/oxideshop_ce/pull/697)
- Support PHP 7.2
- Modules will not be disabled on class loading errors anymore, Error is just logged [PR-661](https://github.com/OXID-eSales/oxideshop_ce/pull/661)
- Use facts to calculate CE location [PR-685](https://github.com/OXID-eSales/oxideshop_ce/pull/685)
- Load SystemRequirements via oxNew [PR-694](https://github.com/OXID-eSales/oxideshop_ce/pull/694)
- Initialize the session only once [PR-699](https://github.com/OXID-eSales/oxideshop_ce/pull/699)
- Backwards compatibility break: `\OxidEsales\EshopCommunity\Application\Model\User::_dbLogin` will only called until the user successfully logs in the
  first time. Afterwards the password hash will have been recreated and a new authentication mechanism will be used. This
  breaks backwards compatibility for modules, which directly override `_dbLogin` or one of the methods in the call stack.
- Fix typo in ident for help near name/surname in `application/views/admin/tpl/shop_main.tpl` [PR-701](https://github.com/OXID-eSales/oxideshop_ce/pull/701)
    - Was `HELP_ENERAL_NAME` changed to `HELP_GENERAL_NAME`
- Drop support for PHP 7.0
- Use user from Order::validateOrder method in validatePayment as well [PR-706](https://github.com/OXID-eSales/oxideshop_ce/pull/706)
- Methods in the following classes return information based on the project configuration. [See documentation about module installation](https://docs.oxid-esales.com/developer/en/6.2/development/modules_components_themes/module/installation_setup/)
    - `\OxidEsales\EshopCommunity\source\Module\Core\Module`
    - `\OxidEsales\EshopCommunity\source\Module\Core\ModuleList`
- The variable `aDisabledModules` in database table `oxconfig` isn't used anymore.
- The variable `aModulePaths` in database table `oxconfig`: Module path will be added on module activation and removed on module deactivation.
- The classes in the folder `Core/Module/` now mainly use the project configuration as a basis for information.
- File `metadata.php` in a module: the key `id` is mandatory and custom php code won't be executed any more. [See Metadata documentation](https://docs.oxid-esales.com/developer/en/6.2/development/modules_components_themes/module/skeleton/metadataphp/) 
- Running tests on travis against all php versions [PR-700](https://github.com/OXID-eSales/oxideshop_ce/pull/700)
- Travis runs phpcs and tests scripts with calling the php directly, not relying on script shebang anymore.
- Updated Yui library components to version 2.9
- Do not trust input from outside for listtype. Catch PHP Fatal error and show normal page. [PR-714](https://github.com/OXID-eSales/oxideshop_ce/pull/714)

### Removed
- Removed old not used blAutoSearchOnCat option from shop_config tab [PR-654](https://github.com/OXID-eSales/oxideshop_ce/pull/654)
- Removed unnecessary class imports [PR-667](https://github.com/OXID-eSales/oxideshop_ce/pull/667)
- Removed deprecated `\OxidEsales\EshopCommunity\Core\Email::$Version` use `\PHPMailer\PHPMailer\PHPMailer::VERSION` instead
- The value for the password salt will not be stored in the database column `oxuser.OXPASSSALT` anymore, but in the password hash itself

### Deprecated
- `\OxidEsales\EshopCommunity\Application\Controller\StartController::getArticleList`
- `\OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface` was added as the new default
  for hashing passwords. Hashing passwords with MD5 and SHA512 is still supported in order support login with
  older password hashes. Therefor the methods and classes below might not be compatible with the current passhword hash
  any more:
    - `\OxidEsales\EshopCommunity\Application\Model\User::_dbLogin`
    - `\OxidEsales\EshopCommunity\Application\Model\User::_getLoginQuery`
    - `\OxidEsales\EshopCommunity\Application\Model\User::_getLoginQueryHashedWithMD5`
    - `\OxidEsales\EshopCommunity\Application\Model\User::encodePassword`
    - `\OxidEsales\EshopCommunity\Core\Hasher`
    - `\OxidEsales\EshopCommunity\Core\PasswordHasher`
    - `\OxidEsales\EshopCommunity\Core\PasswordSaltGenerator`
    - `\OxidEsales\EshopCommunity\Core\Sha512Hasher`
    - `\OxidEsales\EshopCommunity\Application\Model\User::formQueryPartForMD5Password`
    - `\OxidEsales\EshopCommunity\Application\Model\User::formQueryPartForSha512Password`
- `\OxidEsales\EshopCommunity\Core\Base::setConfig`
- `\OxidEsales\EshopCommunity\Core\Base::getConfig`
- `\OxidEsales\EshopCommunity\Core\Base::$_oSession`
- `\OxidEsales\EshopCommunity\Core\Base::setSession`
- `\OxidEsales\EshopCommunity\Core\Base::getSession`
- `\OxidEsales\EshopCommunity\Core\Email::$_oConfig`
- `\OxidEsales\EshopCommunity\Core\Email::setConfig`
- `\OxidEsales\EshopCommunity\Core\Email::getConfig`
- `blDoNotDisableModuleOnError` config option
- `OrderArticle::$_aOrderCache`
- `\OxidEsales\EshopCommunity\Application\Controller\Admin\ModuleConfiguration::_getModuleForConfigVars`
- `\OxidEsales\EshopCommunity\Application\Controller\Admin\ModuleConfiguration::__loadMetadataConfVars`
- `\OxidEsales\EshopCommunity\Core\Module\ModuleChainsGenerator::filterInactiveExtensions()` Now, there are only extensions of active modules in the class chain. No need to filter inactive extensions any more.
- `\OxidEsales\EshopCommunity\Core\Module\ModuleChainsGenerator::cleanModuleFromClassChain()` If you want to clean a module from the class chain, deactivate the module.
- `\OxidEsales\EshopCommunity\Core\Module\ModuleChainsGenerator::getDisabledModuleIds()` Use `OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface` instead to get inactive modules.
- `\OxidEsales\EshopCommunity\Core\Module\ModuleChainsGenerator::getModuleDirectoryByModuleId()` Use `\OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface` instead.
- `\OxidEsales\EshopCommunity\Core\Module\ModuleSmartyPluginDirectoryValidator` Validation was moved to Internal\Framework\Module package and will be executed during the module activation.
- `\OxidEsales\EshopCommunity\Core\Module\ModuleSmartyPluginDirectoryRepository::save` Module smarty plugins directory are stored in project configuration file now. Use appropriate Dao to save them.
- `\OxidEsales\EshopCommunity\Core\Module\ModuleSmartyPluginDirectories::getWithRelativePath` Module smarty plugins directory are stored in project configuration file now. Use appropriate Dao to get them.
- `\OxidEsales\EshopCommunity\Core\Module\ModuleSmartyPluginDirectories::add` Module smarty plugins directory are stored in project configuration file now. Use appropriate Dao to add them.
- `\OxidEsales\EshopCommunity\Core\Module\ModuleSmartyPluginDirectories::set` Module smarty plugins directory are stored in project configuration file now. Use appropriate Dao to set them.
- `\OxidEsales\EshopCommunity\Core\Module\ModuleSmartyPluginDirectories::remove` Module smarty plugins directory are stored in project configuration file now. Use appropriate Dao to remove them.
- `\OxidEsales\EshopCommunity\Core\Module\ModuleExtensionsCleaner::cleanExtensions` will use internal module services instead aModulePaths
- `\OxidEsales\EshopCommunity\Core\Module\ModuleInstaller` Use service "OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface".
- `\OxidEsales\EshopCommunity\Core\Module\Module` Use service 'OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface'.
- `\OxidEsales\EshopCommunity\Core\Module\ModuleList` Use service 'OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface'.
- `\OxidEsales\EshopCommunity\Core\Contract\IModuleValidator` Validation was moved to Internal\Framework\Module package and will be executed during the module activation.
- `\OxidEsales\EshopCommunity\Core\Module\ModuleMetadataValidator` Validation was moved to Internal\Framework\Module package and will be executed during the module activation.
- `\OxidEsales\EshopCommunity\Core\Module\ModuleCache` ModuleCache moved to Internal\Framework\Module package.
- `\OxidEsales\EshopCommunity\Core\Module\ModuleExtensionsCleaner` The whole chain is updated during module activation and deactivation in the database. We do not need this functionality any more
- `\OxidEsales\EshopCommunity\Core\Module\ModuleValidatorFactory` Module metadata validation moved to Internal\Framework\Module package
- `\OxidEsales\EshopCommunity\Core\Routing\Module\ClassProviderStorage` Use `OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ModuleConfigurationDaoBridgeInterface`.
- `\OxidEsales\EshopCommunity\Core\Contract\ClassProviderStorageInterface` Use `OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ModuleConfigurationDaoBridgeInterface`.

## [6.3.8] - Unreleased

### Fixed
- Port missing resetFilter action [#0007051](https://bugs.oxid-esales.com/view.php?id=7051) [PR-739](https://github.com/OXID-eSales/oxideshop_ce/pull/739)

## [6.3.7] - 2020-03-16

### Deprecated
- `\OxidEsales\EshopCommunity\Application\Controller\StartController::getArticleList`

### Fixed
- Fix the host for checking the SystemRequirements->fsockopen to working one [#0006606](https://bugs.oxid-esales.com/view.php?id=6606) [PR-556](https://github.com/OXID-eSales/oxideshop_ce/pull/556)
- Fix more complex multiline query command detection [PR-734](https://github.com/OXID-eSales/oxideshop_ce/pull/734)
- Issue with session ID regeneration on user registration

## [6.3.6] - 2019-10-29

### Fixed
- Recover and use lost _preparePrice result in Article::_prepareModifiedPrice [PR-720](https://github.com/OXID-eSales/oxideshop_ce/pull/720)
- Load amount price list same way on frontend and backend [#0006671](https://bugs.oxid-esales.com/view.php?id=6671) [PR-712](https://github.com/OXID-eSales/oxideshop_ce/pull/712)
- Show product active check in admin panel when activation date has been set [#0006966](https://bugs.oxid-esales.com/view.php?id=6966)

## [6.3.5] - 2019-07-30

### Security
- [Bug 7002](https://bugs.oxid-esales.com/view.php?id=7002)

## [6.3.4] - 2019-05-24

### Changed
- Rename cust_lang.php files to cust_lang.php.dist
    - `source/Application/views/admin/de/cust_lang.php.dist`
    - `source/Application/views/admin/en/cust_lang.php.dist`

### Fixed
- Fix Bank code validation bug in Direct Debit [#0006939](https://bugs.oxid-esales.com/view.php?id=6939)

- Classes:
    - `OxidEsales\EshopCommunity\Core\Module\ModuleInstaller`
    - `OxidEsales\EshopCommunity\source\Module\Core\Module`
    - `OxidEsales\EshopCommunity\source\Module\Core\ModuleList`
    - `OxidEsales\EshopCommunity\Core\Contract\IModuleValidator `
    - `OxidEsales\EshopCommunity\Core\Module\ModuleMetadataValidator`

## [6.3.3] - 2019-04-16

### Fixed
- Ensure temp file in tmp directory [PR-683](https://github.com/OXID-eSales/oxideshop_ce/pull/683)
- Fix warning in inc_error.tpl [PR-690](https://github.com/OXID-eSales/oxideshop_ce/pull/690)
- Fix url protocol in version tags [PR-696](https://github.com/OXID-eSales/oxideshop_ce/pull/696)
- Use correct value for backing up the base language in email [PR-692](https://github.com/OXID-eSales/oxideshop_ce/pull/692)
- Read config parameter by getConfigParam, not by getRequestParam [#0006968](https://bugs.oxid-esales.com/view.php?id=6968) [PR-698](https://github.com/OXID-eSales/oxideshop_ce/pull/698)

## [6.3.2] - 2019-01-22

### Added
- Add method SystemEventHandler::onShopEnd() to be called for finishing actions e.g. from ShopControl::pageClose().

### Changed
- Call to SystemEventHandler::validateOnline() is now called as finishing action rather than startup.

### Deprecated
- `\OxidEsales\EshopCommunity\Core\Email::$Version` This fixes a missing deprecation in PHPMailer, which is the parent
  class of `\OxidEsales\EshopCommunity\Core\Email`. PHPMailer will be upgraded to version 6 in the next minor release
  of OXID eShop CE as PHPMailer 5 will be no longer maintained. Please note, that there are some breaking changes for
  code which extends `\OxidEsales\EshopCommunity\Core\Email`. The impact should be really small, but you should be
  familiar with them, as there are also changes in the SMTP and POP3 classes. Please read the
  [PHPMailer changelog](https://github.com/PHPMailer/PHPMailer/blob/master/changelog.md#version-60-august-28th-2017)

### Fixed
- Wrong behaviour from getOrderArticleSelectList when values from selectionlists and variantselections are selected [PR-507](https://github.com/OXID-eSales/oxideshop_ce/pull/507) [0006539](https://bugs.oxid-esales.com/view.php?id=6539)
- Fix SQL file upload error [Bug #5764](https://bugs.oxid-esales.com/view.php?id=5764)
- Fixed admin login display in Windows 7 IE11 [PR-671](https://github.com/OXID-eSales/oxideshop_ce/pull/671)
- Fix content page data of 8th+ language edit [PR-674](https://github.com/OXID-eSales/oxideshop_ce/pull/674)
- Fix unusable shop after activation of a module with migrated metadata (v2) [PR-663](https://github.com/OXID-eSales/oxideshop_ce/pull/663)
- Fix issue with shop roles readonly. [Bug 6851](https://bugs.oxid-esales.com/view.php?id=6851)

## [6.3.1] - 2018-10-16

### Added
- New settings:
  `includeProductReviewLinksInEmail` defines, if a link to the product review is included in order confirmation email
- Language constants `source/Application/views/admin/[de,en]/lang.php`:
  `SHOP_CONFIG_INCLUDE_PRODUCT_REVIEW_LINKS_IN_ORDER_EMAIL`

### Fixed
- Fix global variable name in startProfile [PR-651](https://github.com/OXID-eSales/oxideshop_ce/pull/651)
- Improve a check of module id in ModuleExtensionsCleaner::filterExtensionsByModuleId [PR-662](https://github.com/OXID-eSales/oxideshop_ce/pull/662)
- AccountReviewController extends correct AccountController [PR-664](https://github.com/OXID-eSales/oxideshop_ce/pull/664)
- Get correct oxid for attributes loaded by loadAttributesDisplayableInBasket [PR-452](https://github.com/OXID-eSales/oxideshop_ce/pull/452)
- Prevent usage of thankyou-controller in no order-context [PR-665](https://github.com/OXID-eSales/oxideshop_ce/pull/665)
- Send correct shop url to includeImages email template parser [PR-545](https://github.com/OXID-eSales/oxideshop_ce/pull/545)
- Wrong return value FrontendController.isVatIncluded [PR-666](https://github.com/OXID-eSales/oxideshop_ce/pull/666) [0006902](https://bugs.oxid-esales.com/view.php?id=6902)
- Fix filecache write/read race conditions [PR-658](https://github.com/OXID-eSales/oxideshop_ce/pull/658)
- Fix wrong variant article price calculation in rss [PR-498](https://github.com/OXID-eSales/oxideshop_ce/pull/498)
- Fix Syntax error in admin css [PR-669](https://github.com/OXID-eSales/oxideshop_ce/pull/669)

## [6.3.0] - 2018-07-31

### Added
- New blocks in `admin/tpl/voucherserie_groups.tpl`
    - `admin_voucherserie_relations`
    - `admin_voucherserie_groups_form`
    - `admin_voucherserie_categories_form`
    - `admin_voucherserie_articles_form`
- PSR3 Logger:
    - New settings:
        - `sLogLevel` in `config.inc.php`
    - New methods:
        - `OxidEsales\EshopCommunity\Core\Registry::getLogger`
        - `getLogger` in `overridablefunctions.php`
- Possibility to configure contact form required fields:
    - New settings:
        - `contactFormRequiredFields`
- Possibility for modules to add new smarty plugins:
    - New settings:
        - `moduleSmartyPluginDirectories`
    - New setting in module metadata.php
        - `smartyPluginDirectories`
- [Module metadata version 2.1](https://docs.oxid-esales.com/developer/en/6.1/modules/skeleton/metadataphp/version21.html)

### Changed
- Support for PHP 7.0 and 7.1, PHP 5.6 not supported any more
- Method visibility changed from private to protected [PR-636](https://github.com/OXID-eSales/oxideshop_ce/pull/636):
    - `OxidEsales\EshopCommunity\Core\Session::isSerializedBasketValid`
    - `OxidEsales\EshopCommunity\Core\Session::isClassInSerializedObject`
    - `OxidEsales\EshopCommunity\Core\Session::isClassOrNullInSerializedObjectAfterField`
    - `OxidEsales\EshopCommunity\Core\Session::isUnserializedBasketValid`
- Name attribute added to no wysiwyg textarea fields in admin

### Deprecated
- `writeToLog` in `bootstrap.php`
- `\OxidEsales\Eshop\Application\Model\FileChecker::class`
- `\OxidEsales\Eshop\Application\Model\FileCheckerResult::class`
- `\OxidEsales\EshopCommunity\Application\Controller\Admin\DiagnosticsMain::_checkOxidFiles`
- `\OxidEsales\EshopCommunity\Application\Controller\Admin\DiagnosticsMain::_getFileCheckReport`
- `\OxidEsales\EshopCommunity\Application\Controller\Admin\DiagnosticsMain::_getFilesToCheck`
- `\OxidEsales\EshopCommunity\Application\Model\Diagnostics::$_aFileCheckerExtensionList`
- `\OxidEsales\EshopCommunity\Application\Model\Diagnostics::$_aFileCheckerPathList`
- `\OxidEsales\EshopCommunity\Application\Model\Diagnostics::getFileCheckerExtensionList`
- `\OxidEsales\EshopCommunity\Application\Model\Diagnostics::getFileCheckerPathList`
- `\OxidEsales\EshopCommunity\Application\Model\Diagnostics::setFileCheckerExtensionList`
- `\OxidEsales\EshopCommunity\Application\Model\Diagnostics::setFileCheckerPathList`
- `\OxidEsales\EshopCommunity\Application\Model\FileChecker::class`
- `\OxidEsales\EshopCommunity\Application\Model\FileCheckerResult::class`
- `\OxidEsales\EshopCommunity\Core\Base::$_oConfig`
- `\OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::$_iDebug`
- `\OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::setIDebug`
- `\OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::writeExceptionToLog`
- `\OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::displayOfflinePage`
- `\OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::displayDebugMessage`
- `\OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::getFormattedException`
- `\OxidEsales\EshopCommunity\Core\Exception\StandardException::debugOut`
- `\OxidEsales\EshopCommunity\Core\OnlineCaller::_castExceptionAndWriteToLog`

### Removed
- Language constants `source/Application/views/admin/[de,en]/lang.php`:
    - `OXDIAG_CHKVERS_FULLREP`
    - `OXDIAG_CHKVERSION`
    - `OXDIAG_COLL_CHKV_FILE_GET`
    - `OXDIAG_COLL_CHKV_NOTINST`
    - `OXDIAG_COLLECT_CHKVERS`
    - `OXDIAG_COLLECT_CHKVERS_DURATION`
    - `OXDIAG_ERRORMESSAGEVERSIONDOESNOTEXIST`
    - `OXDIAG_ERRORMESSAGEWEBSERVICEISNOTREACHABLE`
    - `OXDIAG_ERRORMESSAGEWEBSERVICERETURNEDNOXML`
    - `OXDIAG_ERRORVERSIONCOMPARE`
    - `OXDIAG_FORM_LIST_ALL_FILES`
    - `OXDIAG_INTROINFORMATION`
    - `OXDIAG_INTROINFORMATION_DATA_TRANSMITTED`
    - `OXDIAG_INTROINFORMATION_FILENAME_TO_BE_CHECKED`
    - `OXDIAG_INTROINFORMATION_MD5_CHECKSUM`
    - `OXDIAG_INTROINFORMATION_MORE_INFORMATION`
    - `OXDIAG_INTROINFORMATION_NO_PERSONAL_INFO`
    - `OXDIAG_INTROINFORMATION_OXID_ESALES_BLOG`
    - `OXDIAG_INTROINFORMATION_REVISION_DETECTED`
    - `OXDIAG_INTROINFORMATION_VERSION_DETECTED`
    - `OXDIAG_OBSOLETE`

### Fixed
- Use error_404_handler in article list controller in place of outdated 404 handling [PR-643](https://github.com/OXID-eSales/oxideshop_ce/pull/643)
- Fix indents in config.inc.php.dist [PR-527](https://github.com/OXID-eSales/oxideshop_ce/pull/527)

## [6.2.4] - 2019-10-29

### Fixed
- Improve gift registry search [#0006698](https://bugs.oxid-esales.com/view.php?id=6698)
- Improve coupon concurrency using [#0006819](https://bugs.oxid-esales.com/view.php?id=6819)

### Security
- [Bug 7023](https://bugs.oxid-esales.com/view.php?id=7023)

## [6.2.3] - 2019-07-30

### Security
- [Bug 7002](https://bugs.oxid-esales.com/view.php?id=7002)

## [6.2.2] 2019-02-21

### Fixed
- Fix issue with shop roles readonly. [Bug 6851](https://bugs.oxid-esales.com/view.php?id=6851)

## [6.2.1] - 2018-07-31

### Added

### Changed
-  `\OxidEsales\EshopCommunity\Application\Component\BasketComponent::getPersistedParameters` filter simplified to allow
   arrays in persparams [PR-641](https://github.com/OXID-eSales/oxideshop_ce/pull/641)

### Deprecated

### Removed

### Fixed
- `\OxidEsales\EshopCommunity\Application\Controller\FrontendController::isVatIncluded` Fixed notices and performance
  improved [PR-642](https://github.com/OXID-eSales/oxideshop_ce/pull/642)

### Security
- [Bug 6818](https://bugs.oxid-esales.com/view.php?id=6818)

## [6.2.0] - 2018-03-27

### Added
- Possibility to delete shipping address via new method:
    - `OxidEsales\Eshop\Application\Component\UserComponent::deleteShippingAddress`
- Possibility to delete user account via new methods:
    - `OxidEsales\EshopCommunity\Application\Controller\AccountController::deleteAccount()`
    - `OxidEsales\EshopCommunity\Application\Controller\AccountController::isUserAllowedToDeleteOwnAccount()`
    - `OxidEsales\EshopCommunity\Application\Controller\AccountController::getAccountDeletionStatus()`
- Possibility for shop users to manage their reviews, configurable by admin:
    - New classes:
        - `OxidEsales\EshopCommunity\Application\Controller\AccountReviewController`
    - New methods:
        - `OxidEsales\EshopCommunity\Application\Controller\AccountController::isUserAllowedToManageOwnReviews`
        - `OxidEsales\EshopCommunity\Application\Controller\AccountController::getReviewAndRatingItemsCount`
        - `OxidEsales\EshopCommunity\Application\Controller\CompareController::isUserAllowedToManageOwnReviews`
        - `OxidEsales\EshopCommunity\Application\Controller\CompareController::getReviewAndRatingItemsCount`
        - `OxidEsales\EshopCommunity\Application\Model\Review::getProductReviewItemsCntByUserId`
        - `OxidEsales\EshopCommunity\Application\Model\Review::getReviewAndRatingListByUserId`
    - New language constants in `Application/translations/[de/en]/lang.php`:
        - `ERROR_REVIEW_AND_RATING_NOT_DELETED`
        - `MY_REVIEWS`
    - New language constants in `Application/views/admin/[de/en]/lang.php`:
        - `SHOP_CONFIG_ALLOW_USERS_MANAGE_REVIEWS`
        - `SHOP_CONFIG_ALLOW_USERS_MANAGE_PRODUCT_REVIEWS`
- For displaying recommendations feature new method introduced:
    - `OxidEsales\EshopCommunity\Core\ViewConfig::getShowSuggest()`
- New settings which are configurable in admin area:
    - `blAllowSuggestArticle` - it's possible to disable recommendation feature.
    - `blAllowUsersToDeleteTheirAccount` - it's possible to allow users to delete their account.
    - `blAllowUsersToManageTheirReviews` - it's possible to allow users to manage their reviews.
- New methods:
    - `OxidEsales\EshopCommunity\Application\Model\User::isMallAdmin()`
    - `OxidEsales\EshopCommunity\Core\Registry::getRequest` [PR-626](https://github.com/OXID-eSales/oxideshop_ce/pull/626)
- Filter by working title in admin Selection lists list [PR-632](https://github.com/OXID-eSales/oxideshop_ce/pull/632)
- Article _oAmountPriceInfo list have unit prices calculated if quantity set for product [PR-619](https://github.com/OXID-eSales/oxideshop_ce/pull/619)
    - `fbrutamountprice` and `fnetamountprice` available for usage in template
    - prices already preformatted with current language/currency settings
- `\OxidEsales\Eshop\Application\Model\Order::finalizeOrder` triggers a complete re-validation of the selected payment
  method.  
  New private methods:
    - `\OxidEsales\EshopCommunity\Application\Model\Order::isValidPaymentId`
    - `\OxidEsales\EshopCommunity\Application\Model\Order::isValidPayment`

### Changed
- Loading for non widget classes via `widget.php` entry point have been declined. To allow a class to be loaded
  via `widget.php` it must extend `\OxidEsales\Eshop\Application\Component\Widget\WidgetController`.
- `SeoEncoderArticle::_prepareArticleTitle` now uses `_getUrlExtension()` method in place of hardcoded `.html` extension [PR-634](https://github.com/OXID-eSales/oxideshop_ce/pull/634).
- Add ^ to version constraint on doctrine/dbal [PR-635](https://github.com/OXID-eSales/oxideshop_ce/pull/635)
- Model performance micro optimizations [PR-646](https://github.com/OXID-eSales/oxideshop_ce/pull/646)

### Deprecated
- Recommendations feature will be moved to separate module:
    - `OxidEsales\EshopCommunity\Application\Controller\SuggestController`
    - `OxidEsales\EshopCommunity\Core\ViewConfig::getShowSuggest()`
    - Config option - `blAllowSuggestArticle`
    - Language constants: `SHOP_CONFIG_ALLOW_SUGGEST_ARTICLE`, `HELP_SHOP_CONFIG_ALLOW_SUGGEST_ARTICLE`
- `sOXIDPHP` parameter in `config.inc.php`

### Removed

### Fixed
- Banner image upload is not working [PR-624](https://github.com/OXID-eSales/oxideshop_ce/pull/624)
- imagecreatefromjpeg can't handle sequential jpeg's correctly [PR-627](https://github.com/OXID-eSales/oxideshop_ce/pull/627)
- Support large module list in oxconfig table [PR-633](https://github.com/OXID-eSales/oxideshop_ce/pull/633)
- Use flow theme logo image in offline page [PR-637](https://github.com/OXID-eSales/oxideshop_ce/pull/637)
- Use correct performance checkbox for index page manufacturers [PR-625](https://github.com/OXID-eSales/oxideshop_ce/pull/625)
- VAT message for b2b users with valid company id [PR-495](https://github.com/OXID-eSales/oxideshop_ce/pull/495)

## [6.1.0] - 2018-01-23

### Added
- Added classes:
    - Core\Form\FormFieldsTrimmer
    - Core\Form\FormFieldsTrimmerInterface
- Template blocks:
    - admin_article_variant_selectlist
    - admin_article_variant_extended
    - admin_article_variant_language_edit
    - admin_article_variant_bottom_extended
    - admin_order_remark_type
    - admin_user_remark_type
- New methods:
    - `OxidEsales\EshopCommunity\Core\InputValidator::addValidationError`
    - `OxidEsales\EshopCommunity\Application\Controller\Admin\ActionsMain::checkAccessToEditAction()`
    - `OxidEsales\EshopCommunity\Application\Controller\Admin\AdminController::isNewEditObject()`
    - `OxidEsales\EshopCommunity\Application\Model\Actions::isDefault()`
    - `OxidEsales\EshopCommunity\Core\Model\BaseModel::isPropertyLoaded()`
    - `OxidEsales\EshopCommunity\Application\Controller\TextEditorHandler::disableTextEditor()`
    - `OxidEsales\EshopCommunity\Application\Controller\TextEditorHandler::isTextEditorDisabled()`
    - `OxidEsales\EshopCommunity\Application\Controller\Admin\AdminDetailsController::configureTextEditorHandler()`
    - `OxidEsales\EshopCommunity\Application\Controller\Admin\AdminDetailsController::getTextEditorHandler()`

### Changed
- In voucher series generation, if Coupon Number radio button checked, the number is marked as Required now. [PR-476](https://github.com/OXID-eSales/oxideshop_ce/pull/476)
- Display full field names in product filter dropdown. [PR-614](https://github.com/OXID-eSales/oxideshop_ce/pull/614)
- Use getAdminTplLanguageArray() in Admin only. [PR-592](https://github.com/OXID-eSales/oxideshop_ce/pull/592)
- Delivery dates from past shouldn't be displayed. [PR-543](https://github.com/OXID-eSales/oxideshop_ce/pull/543)
- Readme.md and Contributing.md files are updated.
- CSS adapted in OXID eShop Setup to reflect new design, extracted styles to separate file `Setup/out/src/main.css`
- The function isset on a not loaded property of a model with lazy loading loads the property if it's possible and returns true. To check if property is loaded use BaseModel::isPropertyLoaded()
- admin template `article_main.tpl`

### Deprecated
- \OxidEsales\EshopCommunity\Application\Controller\Admin\ArticleSeo::_getSaveObjectId
- \OxidEsales\EshopCommunity\Application\Component\Widget\ServiceMenu::getCompareItemsCnt
- \OxidEsales\EshopCommunity\Core\Utils::strRot13
- \OxidEsales\EshopCommunity\Core\InputValidator::_addValidationError
- \OxidEsales\EshopCommunity\Application\Model\Order::ORDER_STATE_INVALIDDElADDRESSCHANGED
- \OxidEsales\EshopCommunity\Application\Model\Diagnostics::$_sRevision
- \OxidEsales\EshopCommunity\Application\Model\Diagnostics::getRevision()
- \OxidEsales\EshopCommunity\Application\Model\Diagnostics::setRevision()
- \OxidEsales\EshopCommunity\Application\Model\FileChecker::$_sRevision
- \OxidEsales\EshopCommunity\Application\Model\FileChecker::setRevision()
- \OxidEsales\EshopCommunity\Application\Model\FileChecker::getRevision()
- \OxidEsales\EshopCommunity\Core\Config::getRevision()
- \OxidEsales\EshopCommunity\Core\Controller\BaseController::getRevision()

### Removed
- 'Your market' selection was removed from Setup wizard, as this value is no longer evaluated
- Database transaction was removed from finalizeOrder method. Fixes bug [#6736](https://bugs.oxid-esales.com/view.php?id=6736)

### Fixed
- [Missing translations](https://bugs.oxid-esales.com/view.php?id=6721)
- [Manufacturer Seo urls not properly stored in database](https://bugs.oxid-esales.com/view.php?id=6694)
- [Change robots.txt entry from "Disallow: /agb/" to "Disallow: /AGB/"](https://bugs.oxid-esales.com/view.php?id=6703)
- [Not trimmed ZIP-Codes"](https://bugs.oxid-esales.com/view.php?id=6693)
- [admin/oxajax.php needs to handle module not namespaced ajax container classes](https://bugs.oxid-esales.com/view.php?id=6729)
- [Compare links in lists do not work correctly](https://bugs.oxid-esales.com/view.php?id=5354)
- [Transparent gif looses transparency when generated to different size](https://bugs.oxid-esales.com/view.php?id=3194)
- Additional cache variable for Article::getAttributesDisplayableInBasket [PR-616](https://github.com/OXID-eSales/oxideshop_ce/pull/616)
- [RSS feed for categories not sorted desc by time](https://bugs.oxid-esales.com/view.php?id=6739)
- [VariantHandler always uses brutto price for MD Variants](https://bugs.oxid-esales.com/view.php?id=6761)
- Expire SEO links for correct shop id [PR-594](https://github.com/OXID-eSales/oxideshop_ce/pull/594)
- [Module with namespaces not working on Windows](https://bugs.oxid-esales.com/view.php?id=6737)

## [6.0.0] - 2017-11-17

### Fixed
- [in source/admin/oxajax.php ControllerClassNameResolver is not used for resolving container. Can't create custom drag&drop in mod](https://bugs.oxid-esales.com/view.php?id=6668)
- [admin/oxajax.php needs to handle module namespaced ajax container classes](https://bugs.oxid-esales.com/view.php?id=6711)
- [Disabled controls are not clearly visible as not writable](https://bugs.oxid-esales.com/view.php?id=6702)

## [6.0.0-rc.3] - 2017-11-02

### Changed
- `\OxidEsales\Eshop\Application\Controller\FrontendController::getUserSelectedSorting()`
  checks if element to sort is configured in Admin.
- Removed `exec()` calls in setup.
- Pagination has been changed:
  for example previously it was "Geschenke/100/", now it will be "Geschenke?pgNr=99".
  In addition these pages come with "robots" meta tag "noindex, follow".

### Deprecated
- `\OxidEsales\Eshop\Application\Controller\Admin\AdminController::$_sShopVersion`
- `\OxidEsales\Eshop\Application\Controller\Admin\AdminController::_getShopVersionNr()`
- `\OxidEsales\Eshop\Core\Config::getVersion()`
- In `oxshops` table field - `OXVERSION` is deprecated. This field value will not be updated anymore and will contain
  "6.0.0" value. To retrieve correct shop version `OxidEsales\Eshop\Core\ShopVersion::getVersion()` must be used.
- `\OxidEsales\Eshop\Core\Config::getEdition()`
- In `oxshops` table field - `OXEDITION` is deprecated. To retrieve OXID eShop edition
  facts component should be used: `\OxidEsales\Facts\Facts::getEdition()`.
- `\OxidEsales\Eshop\Application\Controller\Admin\ShopRdfa::submitUrl()`, because GR-Notify page feature was removed.
- `\OxidEsales\Eshop\Application\Controller\Admin\ShopRdfa::getHttpResponseCode()`, because GR-Notify page feature was
  removed.
- Template block in *Application/views/admin/tpl/shop_rdfa.tpl* - `admin_shop_rdfa_submiturl`, because GR-Notify page
  feature was removed.
- Config option blLoadDynContents as it's part of dynamic pages.
- `\OxidEsales\Eshop\Core\ShopControl::$_blHandlerSet`. This property is not used anymore.
- `\OxidEsales\Eshop\Core\WidgetControl::$_blHandlerSet`. This property is not used anymore.

### Removed
- Dynamic pages related code including.
- GR-Notify page feature.

### Fixed
- https://bugs.oxid-esales.com/view.php?id=6474 with PR#457
- https://bugs.oxid-esales.com/view.php?id=6155 with PR#431
- https://bugs.oxid-esales.com/view.php?id=6579 with PR#487
- https://bugs.oxid-esales.com/view.php?id=6465 with PR #458
- https://bugs.oxid-esales.com/view.php?id=6683
- https://bugs.oxid-esales.com/view.php?id=6695
- https://bugs.oxid-esales.com/view.php?id=6716

### Security
- https://bugs.oxid-esales.com/view.php?id=6678


## [6.0.0-rc.2] - 2017-08-15

### Added
- Integrate new Admin UI from digidesk backend UI Kit
- ddoe/wysiwyg-editor-module was added as requirement of OXID eShop Community Edition in composer.json
- Grace period reset email is sent on grace period reset.
- User and admin sessions are detached on E_ERROR type errors (in register_shutdown_function).
- Translation for GENERAL_ARTICLE_OXVARMAXPRICE, [Pull Request 572](https://github.com/OXID-eSales/oxideshop_ce/pull/572), [Pull Request 573](https://github.com/OXID-eSales/oxideshop_ce/pull/573)
- Added mkdir if folders not exist in _copyFile method, [Pull Request 590](https://github.com/OXID-eSales/oxideshop_ce/pull/590)

### Changed
- language constant `HELP_SHOP_CONFIG_SETORDELETECURRENCY`, [Pull Request 547](https://github.com/OXID-eSales/oxideshop_ce/pull/547)
- language constant `SHOP_CONFIG_SETORDELETECURRENCY`, [Pull Request 547](https://github.com/OXID-eSales/oxideshop_ce/pull/547)
- template `admin/tpl/shop_config.tpl`, [Pull Request 547](https://github.com/OXID-eSales/oxideshop_ce/pull/547)
- Css from admin login page moved to `out/admin/src/login.css`, [Pull Request 558](https://github.com/OXID-eSales/oxideshop_ce/pull/558)
- Database migrations and views regeneration is operating system independent which makes OXID eShop installable on Windows.
- Classes of the `\OxidEsales\Eshop\` namespace are real (empty) classes now and called [`unified namespace classes`](http://oxid-eshop-developer-documentation.readthedocs.io/en/latest/modules/using_namespaces_in_modules.html).
- [Pull Request 557: Remove duplicate directory separator](https://github.com/OXID-eSales/oxideshop_ce/pull/557)
- [Pull Request 561: Fixup basket wrapping calculation](https://github.com/OXID-eSales/oxideshop_ce/pull/561)
- Introduce colon and ellipsis, [Pull Request 579](https://github.com/OXID-eSales/oxideshop_ce/pull/579), [Pull Request 581](https://github.com/OXID-eSales/oxideshop_ce/pull/581)

### Deprecated
- iUtfMode in config.inc.php. This property will be removed in the future as the shop will always use UTF-8.
- Class Core/Email: Rename $SMTP_PORT to $smtpPort, [Pull Request 563](https://github.com/OXID-eSales/oxideshop_ce/pull/563)

### Removed
- Azure theme was extracted from the OXID eShop CE repository to [separate repository](https://github.com/OXID-eSales/azure-theme).
    - Azure theme should not be used for new projects.
    - In case there is a need to use azure theme, install it via command: `composer require oxid-esales/azure-theme:^1.4.1`.

### Fixed
- Date formatting in EXCEPTION_LOG.txt: textual representation of the day was replaced by numerical representation (01 to 31)
- iUtfMode in config.inc.php: backwards compatibility restored. This setting was removed, but it is introduced again, as some modules still might use it.
- BaseModel::_update(): backwards compatibility restored. Returns always true on success or throws an exception.
- Removed duplicate directory separators in vendor directory calculation methods, [Pull Request 557](https://github.com/OXID-eSales/oxideshop_ce/pull/557)
- BaseController::executeFunction throws ERROR_MESSAGE_SYSTEMCOMPONENT_CLASSNOTFOUND for metadata v2 modules in some cases, [#0006627](https://bugs.oxid-esales.com/view.php?id=6627)
- Template directories local class cache is cleared on smarty reinitialization [Change](https://github.com/OXID-eSales/oxideshop_ce/blob/90bf9facc7f7d80f48f72e631555d0ac29a3e061/source/Core/UtilsView.php#L82)
- Change primary key of database table `oxstates` to composite, [#0005029](https://bugs.oxid-esales.com/view.php?id=5029)
- Issue with basket reservations causing wrong stock levels in high load scenarios, [#0006102](https://bugs.oxid-esales.com/view.php?id=6102)
- Deactivating a module which extends basket causes shop maintenance mode, [#0006659](https://bugs.oxid-esales.com/view.php?id=6659)
- Pass along shopid to call to _loadFromDb(), [Pull Request 571](https://github.com/OXID-eSales/oxideshop_ce/pull/571)

## [6.0.0-rc.1] - 2017-04-07

### Added
- [Pull Request 425: Compatibility with Apache 2.4](https://github.com/OXID-eSales/oxideshop_ce/pull/425)
- [Metadata version 2.0](http://oxid-eshop-developer-documentation.readthedocs.io/en/latest/modules/metadata/version20.html)
- Added classes and methods:
    - ModuleChainsGenerator::getActiveChain()
    - ModuleList::parseModuleChains()
    - Core\Module\ModuleTranslationPathFinder

### Changed
- [Pull Request 550: replace intval with typecast](https://github.com/OXID-eSales/oxideshop_ce/pull/550)
- [Pull Request 555: Removed a commented debugging line](https://github.com/OXID-eSales/oxideshop_ce/pull/555)
- Module section `extend` in the file metadata.php gets validated since metadata version 2.0.
- Database columns were changed due to unification of OXID eShop editions.
- In case OXID development tools are installed, IDE Helper generator will be run on every composer install/update.
- Not loadable module classes are now shown in `Problematic files` section.
- Only backwards compatible classes (e.g oxarticle) or classes from virtual namespace can be extended by modules.
- PayPal module, which is compatible with OXID eShop 6, has been added to the compilation.
- Changed templates and blocks:
    - block `admin_order_overview_total`, file `admin/tpl/order_overview.tpl`.
    - template `admin/tpl/order_article.tpl`
    - template `admin/tpl/order_overview.tpl`
    - template `admin/tpl/include/order_info.tpl`

### Deprecated
- Azure theme is deprecated and in next release it will be removed from compilation.
- Deprecated classes and methods: Search for the notation `@deprecated` in the sourcecode.

### Removed
- config.inc.php options `iUtfMode`, `sDefaultDatabaseConnection` and `blSkipEuroReplace` because shop is utf-8.
- config.inc.php option `vendorDirectory`. Instead the constant VENDOR_PATH was introduced.

### Fixed
- Module deactivation/deletion/cleanup issues fixed which occured because of namespaces in modules.


## [6.0-beta.3] - 2017-03-14

## [6.0-beta.2] - 2017-12-13

## [6.0-beta.1] - 2016-11-30

[7.0.0-rc.4]: https://github.com/OXID-eSales/oxideshop_ce/compare/v7.0.0-rc.3...v7.0.0-rc.4
[7.0.0-rc.3]: https://github.com/OXID-eSales/oxideshop_ce/compare/v7.0.0-rc.2...v7.0.0-rc.3
[7.0.0-rc.2]: https://github.com/OXID-eSales/oxideshop_ce/compare/v7.0.0-rc.1...v7.0.0-rc.2
[7.0.0-rc.1]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.13.0...v7.0.0-rc.1
[6.13.0]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.12.0...v6.13.0
[6.12.0]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.11.0...v6.12.0
[6.11.0]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.10.3...v6.11.0
[6.10.3]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.10.2...v6.10.3
[6.10.2]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.10.1...v6.10.2
[6.10.1]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.10.0...v6.10.1
[6.10.0]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.9.1...v6.10.0
[6.9.1]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.9.0...v6.9.1
[6.9.0]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.8.0...v6.9.0
[6.8.0]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.7.1...v6.8.0
[6.7.2]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.7.1...v6.7.2
[6.7.1]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.7.0...v6.7.1
[6.7.0]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.6.0...v6.7.0
[6.6.0]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.5.6...v6.6.0
[6.5.6]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.5.5...v6.5.6
[6.5.5]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.5.4...v6.5.5
[6.5.4]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.5.3...v6.5.4
[6.5.3]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.5.2...v6.5.3
[6.5.2]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.5.1...v6.5.2
[6.5.1]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.5.0...v6.5.1
[6.5.0]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.4.0...v6.5.0
[6.4.0]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.3.5...v6.4.0
[6.3.8]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.3.7...b-6.1.x
[6.3.7]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.3.6...v6.3.7
[6.3.6]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.3.5...v6.3.6
[6.3.5]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.3.4...v6.3.5
[6.3.4]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.3.3...v6.3.4
[6.3.3]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.3.2...v6.3.3
[6.3.2]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.3.1...v6.3.2
[6.3.1]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.3.0...v6.3.1
[6.3.0]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.2.1...v6.3.0
[6.2.4]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.2.3...v6.2.4
[6.2.3]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.2.2...v6.2.3
[6.2.2]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.2.1...v6.2.2
[6.2.1]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.2.0...v6.2.1
[6.2.0]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.1.0...v6.2.0
[6.1.0]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0.0...v6.1.0
[6.0.0]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0.0-rc.3...v6.0.0
[6.0.0-rc.3]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0.0-rc.2...v6.0.0-rc.3
[6.0.0-rc.2]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0.0-rc.1...v6.0.0-rc.2
[6.0.0-rc.1]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0-beta.3...v6.0.0-rc.1
[6.0-beta.3]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0-beta.2...v6.0-beta.3
[6.0-beta.2]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0-beta.1...v6.0-beta.2
[6.0-beta.1]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0-beta.1...v6.0-beta.2
