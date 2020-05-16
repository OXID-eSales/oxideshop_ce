# Change Log for OXID eShop Community Edition Core Component

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

### Added
- Support for MySQL v8.0
- More information about problems is shown after permission check in Setup [PR-764](https://github.com/OXID-eSales/oxideshop_ce/pull/764)
- Logging to shop constructor if shop is not valid [PR-733](https://github.com/OXID-eSales/oxideshop_ce/pull/733)
- Checking if multilanguage base table from configuration exists, before trying to generate its views [PR-754](https://github.com/OXID-eSales/oxideshop_ce/pull/754)
- Performance improvement of Field class [PR-771](https://github.com/OXID-eSales/oxideshop_ce/pull/771)

### Changed
- Source/Application/views/admin/tpl/shop_license.tpl
- Block names in source/Application/views/admin/tpl/shop_main.tpl were switched [PR-730](https://github.com/OXID-eSales/oxideshop_ce/pull/730):
    - ``admin_shop_main_leftform``
    - ``admin_shop_main_rightform``
- Added arguments to oxNew method signature to improve static analysis possibilities [PR-744](https://github.com/OXID-eSales/oxideshop_ce/pull/744)
- Skip currency url generation if "Display Currencies" option is disabled [PR-750](https://github.com/OXID-eSales/oxideshop_ce/pull/750)
- Removed multilines in translation files to make it fit for localization platforms [PR-729](https://github.com/OXID-eSales/oxideshop_ce/pull/729)
- Update symfony components to version 5
- Change translations loading source for themes to be same as for core and modules

### Deprecated
- `\OxidEsales\EshopCommunity\Core\Controller\BaseController::getConfig`
- `\OxidEsales\EshopCommunity\Core\ViewConfig::getConfig`

### Removed
- Support for PHP v7.1, v7.2
- Support for MySQL v5.5, v5.6
- Database encoding:
    - Changed database fields:
        - `oxvalue` field in `oxconfig` table changed from `blob` to `text`
        - `oxvalue` field in `oxuserpayments` table changed from `blob` to `text`
    - Removed methods and properties:
        - `OxidEsales\Eshop\Core\Config::getDecodeValueQuery()`
        - `OxidEsales\Eshop\Core\Config::$sConfigKey`
        - `OxidEsales\Eshop\Core\Config::DEFAULT_CONFIG_KEY`
    - Removed classes:
        - `Conf`
    - Removed settings:
        - `sConfigKey` from `config.inc.php`
- Functionality for MySQL version check in Setup
- `OxidEsales\EshopCommunity\Core\SystemRequirements::checkMysqlVersion()`
- \OxidEsales\EshopCommunity\Application\Controller\Admin\AdminController::getServiceUrl()
- Class \OxidEsales\EshopCommunity\Application\Controller\Admin\DynEconda
- Class \OxidEsales\EshopCommunity\Application\Controller\Admin\DynamicInterface
- Class \OxidEsales\EshopCommunity\Application\Controller\Admin\DynamicScreenController
- Class \OxidEsales\EshopCommunity\Application\Controller\Admin\DynamicScreenList
- Class \OxidEsales\EshopCommunity\Application\Controller\Admin\DynamicScreenLocal
- \OxidEsales\EshopCommunity\Application\Controller\Admin\NavigationTree::_addDynLinks()
- \OxidEsales\EshopCommunity\Application\Controller\Admin\NavigationTree::_checkDynFile()
- \OxidEsales\EshopCommunity\Application\Controller\Admin\NavigationTree::_getDynMenuLang()
- \OxidEsales\EshopCommunity\Application\Controller\Admin\NavigationTree::_getDynMenuUrl()
- \OxidEsales\EshopCommunity\Application\Component\Widget::getCompareItemsCnt()
- \OxidEsales\EshopCommunity\Core\Config::getRevision()
- \OxidEsales\EshopCommunity\Core\Controller\BaseController::getRevision()
- Template source/Application/views/admin/tpl/dyn_econda.tpl
- Template source/Application/views/admin/tpl/dynscreen.tpl
- Template source/Application/views/admin/tpl/dynscreen_list.tpl
- Template source/Application/views/admin/tpl/dynscreen_local.tpl
- Template source/Application/views/admin/tpl/version_checker_result.tpl
- `sOXIDPHP` parameter in `config.inc.php`
- `blDoNotDisableModuleOnError` parameter in `config.inc.php`
- \OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::writeExceptionToLog
- \OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::displayOfflinePage
- \OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::setLogFileName
- \OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::getLogFileName
- \OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::setIDebug
- \OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::$_sFileName
- \OxidEsales\EshopCommunity\Core\Exception\StandardException::debugOut
- \OxidEsales\EshopCommunity\Core\Exception\StandardException::setLogFileName
- \OxidEsales\EshopCommunity\Core\Exception\StandardException::getLogFileName
- \OxidEsales\EshopCommunity\Core\Exception\StandardException::$_sFileName
- \OxidEsales\EshopCommunity\Core\OnlineCaller::_castExceptionAndWriteToLog
- \OxidEsales\EshopCommunity\Core\Utils::writeToLog
- `writeToLog` in `bootstrap.php`
- \OxidEsales\EshopCommunity\Core\Base::$_oConfig
- \OxidEsales\EshopCommunity\Core\Base::$_oSession
- \OxidEsales\EshopCommunity\Core\Base::$_oRights
- Class `\OxidEsales\Eshop\Application\Model\FileChecker`
- Class `\OxidEsales\Eshop\Application\Model\FileCheckerResult`
- Class `\OxidEsales\EshopCommunity\Application\Model\FileChecker`
- Class `\OxidEsales\EshopCommunity\Application\Model\FileCheckerResult`
- `\OxidEsales\EshopCommunity\Application\Controller\Admin\DiagnosticsMain::_checkOxidFiles`
- `\OxidEsales\EshopCommunity\Application\Controller\Admin\DiagnosticsMain::_getFileCheckReport`
- `\OxidEsales\EshopCommunity\Application\Controller\Admin\DiagnosticsMain::_getFilesToCheck`
- `\OxidEsales\EshopCommunity\Application\Model\Diagnostics::$_aFileCheckerExtensionList`
- `\OxidEsales\EshopCommunity\Application\Model\Diagnostics::$_aFileCheckerPathList`
- `\OxidEsales\EshopCommunity\Application\Model\Diagnostics::getFileCheckerExtensionList`
- `\OxidEsales\EshopCommunity\Application\Model\Diagnostics::getFileCheckerPathList`
- `\OxidEsales\EshopCommunity\Application\Model\Diagnostics::setFileCheckerExtensionList`
- `\OxidEsales\EshopCommunity\Application\Model\Diagnostics::setFileCheckerPathList`
- `\OxidEsales\EshopCommunity\Core\Base::setConfig`
- `\OxidEsales\EshopCommunity\Core\Base::getConfig`
- `\OxidEsales\EshopCommunity\Core\Email::$_oConfig`
- `\OxidEsales\EshopCommunity\Core\Email::setConfig`
- `\OxidEsales\EshopCommunity\Core\Email::getConfig`
- `\OxidEsales\EshopCommunity\Core\SystemRequirements::getConfig`
- `\OxidEsales\EshopCommunity\Core\Module::getConfigBlDoNotDisableModuleOnError`
- `\OxidEsales\EshopCommunity\Application\Model\OrderArticle::$_aOrderCache`
- Removed deprecated getSession and setSession usages around the code
- `OxidEsales\Eshop\Application\Model\UserPayment::$_sPaymentKey`
- `OxidEsales\Eshop\Application\Model\UserPayment::getPaymentKey()`
- PHP version checker
    - `OxidEsales\EshopCommunity\Core\SystemRequirements::checkPhpVersion()`
    - Language Variable:
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
    - Language Variable:
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
- Language Variable:
    - `SYSREQ_MYSQL_VERSION`
- Betanote:
    - Class: `OxidEsales\EshopCommunity\Application\Component\Widget\BetaNote`
    - Method: `OxidEsales\EshopCommunity\Core\Controller\BaseController::showBetaNote()`
- Suggest (Recommend Product) feature:
    - Class: `OxidEsales\EshopCommunity\Application\Controller\SuggestController`
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
    - Config option (usage): `blAllowSuggestArticle`
### Fixed
- Fix not working actions and promotions [#0005526](https://bugs.oxid-esales.com/view.php?id=5526)
- Refactor calls to deprecated `getStr` [PR-758](https://github.com/OXID-eSales/oxideshop_ce/pull/758)
- Fixed missed deprecated getConfig and getSession method usages [PR-721](https://github.com/OXID-eSales/oxideshop_ce/pull/721)
- Improve oxseo::OXOBJECTID index to fit current queries [PR-466](https://github.com/OXID-eSales/oxideshop_ce/pull/466)
- Replaced BC classes with namespaced ones [PR-772](https://github.com/OXID-eSales/oxideshop_ce/pull/772)
- Ensure out/pictures/generated directory existance [PR-789](https://github.com/OXID-eSales/oxideshop_ce/pull/789)
- Improved various docs, variable and other coding style problems:
    - [PR-741](https://github.com/OXID-eSales/oxideshop_ce/pull/741)
    - [PR-761](https://github.com/OXID-eSales/oxideshop_ce/pull/761)
    - [PR-748](https://github.com/OXID-eSales/oxideshop_ce/pull/748) 
    - [PR-756](https://github.com/OXID-eSales/oxideshop_ce/pull/756)
    - [PR-765](https://github.com/OXID-eSales/oxideshop_ce/pull/765)
    - [PR-780](https://github.com/OXID-eSales/oxideshop_ce/pull/780)
    - [PR-778](https://github.com/OXID-eSales/oxideshop_ce/pull/778)
    - [PR-779](https://github.com/OXID-eSales/oxideshop_ce/pull/779)
    - [PR-777](https://github.com/OXID-eSales/oxideshop_ce/pull/777)
    - [PR-774](https://github.com/OXID-eSales/oxideshop_ce/pull/774)
    - [PR-773](https://github.com/OXID-eSales/oxideshop_ce/pull/773)
    - [PR-775](https://github.com/OXID-eSales/oxideshop_ce/pull/775)
    - [PR-776](https://github.com/OXID-eSales/oxideshop_ce/pull/776)
    - [PR-790](https://github.com/OXID-eSales/oxideshop_ce/pull/790)
    
### Security

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
- Methods starting with underscore have been deprecated, these methods will be renamed

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
- Language Variable:
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
- Language Variable:
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
    - Language Variable:
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
- Methods in the following classes return information based on the project configuration. [See documentation about module installation](https://docs.oxid-esales.com/developer/en/6.2/modules/installation/)
    - `\OxidEsales\EshopCommunity\source\Module\Core\Module`
    - `\OxidEsales\EshopCommunity\source\Module\Core\ModuleList` 
- The variable `aDisabledModules` in database table `oxconfig` isn't used anymore.
- The variable `aModulePaths` in database table `oxconfig`: Module path will be added on module activation and removed on module deactivation.
- The classes in the folder `Core/Module/` now mainly use the project configuration as a basis for information.
- File `metadata.php` in a module: the key `id` is mandatory and custom php code won't be executed any more. [See Metadata documentation](https://docs.oxid-esales.com/developer/en/6.2/modules/skeleton/metadataphp/) 
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
- Deprecated classes and methods: Search for the notation `@deprecated` in the sourcecode. At a later time, please 
  use [this overview of source code documentation](https://oxidforge.org/en/source-code-documentation-overview), 
  pick the version you need and follow the link to it. On the navigation to the left hand side you will find a link 
  called `Deprecated list` which leads you to the wanted information.

### Removed
- config.inc.php options `iUtfMode`, `sDefaultDatabaseConnection` and `blSkipEuroReplace` because shop is utf-8.
- config.inc.php option `vendorDirectory`. Instead the constant VENDOR_PATH was introduced.

### Fixed
- Module deactivation/deletion/cleanup issues fixed which occured because of namespaces in modules.


## [6.0-beta.3] - 2017-03-14

See 
- [OXID eShop v6.0.0 Beta3 is published](https://oxidforge.org/en/oxid-eshop-v6-0-0-beta3-is-published.html)


## [6.0-beta.2] - 2017-12-13

See 
- [OXID eShop v6.0.0 Beta2 is published](https://oxidforge.org/en/oxid-eshop-v6-0-0-beta2-published.html)


## [6.0-beta.1] - 2016-11-30

See 
- [OXID eShop v6.0.0 Beta1 released](https://oxidforge.org/en/oxid-eshop-v6-0-0-beta1-released.html)
- [OXID eShop v6.0.0 Beta1: Overview of Changes](https://oxidforge.org/en/oxid-eshop-v6-0-0-beta1-overview-of-changes.html)
- [OXID eShop v6.0.0 Beta1: Detailed Code Changelog](https://oxidforge.org/en/oxid-eshop-v6-0-0-beta1-detailed-code-changelog.html)

[Unreleased]: https://github.com/OXID-eSales/oxideshop_ce/compare/b-6.2.x...master
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
