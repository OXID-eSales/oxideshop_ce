# Change Log for OXID eShop Community Edition Core Component

## v7.0.3 - 2024-02-20

### Fixed
- Fix skipped backend startup checks [PR-927](https://github.com/OXID-eSales/oxideshop_ce/pull/927)
- Upload everything as picture [#0005897](https://bugs.oxid-esales.com/view.php?id=5897)

### Changed
- Update shop version for compilation release


## v7.0.2 - 2023-11-28

### Fixed
- Attribute value of 0 breaks category filtering with attributes in frontend [#0007474](https://bugs.oxid-esales.com/view.php?id=7474)
- Database connection object fixation in ConnectionProvider service
- Inactive articles are shown in the detail page [PR-911](https://github.com/OXID-eSales/oxideshop_ce/pull/911) [#0007476](https://bugs.oxid-esales.com/view.php?id=7476)
- Broken child-theme in child sub-shops [#0007477](https://bugs.oxid-esales.com/view.php?id=7477)
- Fix exception after Setup via Browser [#0007478](https://bugs.oxid-esales.com/view.php?id=7478)
- Print a meaningful message when config file is missing in CLI
- Wrong logo reference [#0007487](https://bugs.oxid-esales.com/view.php?id=7487)

## v7.0.1 - 2023-05-22

### Fixed
- Fix "undefined variable" in `ClassPropertyModuleConfigurationCache::evict()`

## v7.0.0 - 2023-05-09

### Added
- Add sorting field for Manufacturer
- Add module class extensions and make it public: `ActiveModulesDataProviderBridge::getClassExtensions()`
- Extend cache invalidation under the Internal namespace, `FilesystemModuleCache::invalidateAll()`
- Add events: `AdminModeChangedEvent` and `ModuleConfigurationChangedEvent`

### Fixed
- Import of `oxartextends` with columns [#0007152](https://bugs.oxid-esales.com/view.php?id=7152)
- Invalidate module cache on module settings/configuration change
- Setup validation of module services with multiple shops
- Deprecation warnings

### Deprecated
- Deprecate `Manufacturer::getThumbnailUrl()` method.

### Removed
- Backwards compatibility name support for 'attribute' controller. Please use the full namespace. [PR-907](https://github.com/OXID-eSales/oxideshop_ce/pull/907)
- Dependency to `webmozart/path-util`

## v7.0.0-rc.4 - 2022-11-23

### Added
- Introduce `ModuleClassExtensionChainChangedEvent`

### Fixed
- Fix prepared statements in GUI Setup
- Fix deprecation warnings
- Fix notices during FormatDateLogic modifier handling of empty values
- Fix notices in ``Order::getBillCountry`` and ``Order::getDelCountry`` methods [PR-906](https://github.com/OXID-eSales/oxideshop_ce/pull/906)
- Fix warning during language sorting [PR-904](https://github.com/OXID-eSales/oxideshop_ce/pull/904)

### Deprecated
- Deprecate invalidate module cache functionality in `ShopAdapter`

## v7.0.0-rc.3 - 2022-11-04

### Added
- Configuration parameter `oxid_esales.templating.engine_autoescapes_html` that delegates HTML-escaping to templating engine
  (when set to true, `Core\Field` will contain non-escaped HTML special characters)

### Removed
- RSS functionality
- Support for Smarty-specific keys in `metadata.php` moved to the `oxid-esales/smarty-component`
- Google Webfonts usage in offline.html [PR-900](https://github.com/OXID-eSales/oxideshop_ce/pull/900)
- Remove module configuration service and functionalities
- Remove some templating methods and Smarty plugins
- Remove some core utilities and DI container methods

### Fixed
- Partly revert `Core\Autoload\ModuleAutoload`
- PHP-warning if varselid is not an array [PR-897](https://github.com/OXID-eSales/oxideshop_ce/pull/897)

### Changed
- Shop configuration files structure
- Change `BaseModel::assignRecord()` function visibility form public to protected
- Only module DI services, event subscribers, commands etc. of the modules which are active in the current shop will be loaded,
  no need to implement `ShopAwareInterface` or extend `ShopAwareEventDispatcher`, `AbstractShopAwareCommand`.

### Deprecated
- Deprecate `setEngine()` and `getEngine()` functionalities in `TemplateRendererBridgeInterface`

## v7.0.0-rc.2 - 2022-08-15

### Added
- Twig templates multi inheritance for modules
- Shop configuration to define loading order for module templates

### Changed
- Update Symfony components to v6
- Cache storage format in `FilesystemModuleCache` to `JSON`
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

### Added
- WebP image format support with an option to convert other format images automatically

### Deprecated
- Deprecate `ModuleSettingBridge`

## v7.0.0-rc.1 - 2021-07-07

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
  - Newsletter email management
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
