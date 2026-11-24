# Change Log for OXID eShop Community Edition Core Component

## v7.7.0 - Unreleased

### Added
- PHPUnit v13 support
- Community and project migrations are registered as tagged `oxid_esales.migration_path_provider` services (`CommunityEditionMigrationPathProvider`, `ProjectMigrationPathProvider`)
- `MigrationsNotFoundException`

### Changed
- `MigrationExecutor` no longer uses `oxid-esales/oxideshop-doctrine-migration-wrapper` internally
- `Utilities::createMigrations()` throws a `LogicException`

### Deprecated
- `Utilities::createMigrations()`
- Theme configuration classes and methods
  - `Theme` and `SettingsHandler`
  - `ThemeConfiguration::saveConfVars()`, `ThemeConfiguration::$_sTheme` and `ThemeMain::themeInConfigFile()`
  - `Config::isThemeOption()`, `Config::$_aThemeConfigParams` and `Config::OXMODULE_THEME_PREFIX`
  - `ViewConfig::getViewThemeParam()`
  - `Language::getCustomThemeLanguageFiles()`
  - `ShopAdapterInterface`: `getActiveThemesList()`, `getCustomTheme()`, `getActiveThemeId()`, `themeExists()` and `activateTheme()`
- `ThemeSettingChangedEvent`
- `AdminThemeBridgeInterface`, use the DI parameter `oxid_esales.theme.admin.name` instead
- `Internal\Framework\Theme\Event\ThemeActivatedEvent`
- SSL related functionality, the shop will run on HTTPS only in v8.0
  - `Config::isSsl()`, `Config::setIsSsl()`, `Config::checkSsl()`, `Config::isHttpsOnly()`, `Config::isCurrentProtocol()` and `Config::$_blIsSsl`
  - `Config::getShopSecureHomeUrl()` and `Config::getShopCurrentUrl()`, use `Config::getShopHomeUrl()` instead
  - `ViewConfig::getSslSelfLink()` and `ViewConfig::getSelfActionLink()`, use `ViewConfig::getSelfLink()` instead
  - `ViewConfig::isSsl()` and `ViewConfig::getNoSslImageDir()`
  - `SystemRequirements::getShopSSLHostInfo()` and `SystemRequirements::getShopSSLHostInfoFromConfig()`
  - `AdminController::getServiceProtocol()`
  - `UtilsUrl::getCurrentUrl()`
  - `Wrapping::getNoSslDynImageDir()`, use `Config::getPictureUrl()` instead
  - `BasketItem::$_sDimageDirNoSsl`, `BasketItem::$_sDimageDirSsl` and the `nossl_dimagedir` / `ssl_dimagedir` properties of `Article`
  - `LanguageMain::$_aLanguagesUrls` and `LanguageMain::$_aLanguagesSslUrls`
  - `HttpsRequestResolverInterface` and `HttpsRequestResolver`
  - Transfer of cookies between HTTP and HTTPS via the session: `UtilsServer::loadSessionCookies()`, `UtilsServer::saveSessionCookie()`, `UtilsServer::getSessionCookieKey()`, `UtilsServer::mustSaveToSession()`, `UtilsServer::$_sSessionCookiesName`, `UtilsServer::$_sSessionCookies`, `UtilsServer::$_blSaveToSession` and the `aSessionCookies` session variable
  - Redirects to HTTPS on the admin login page, for the autologin cookie and on the payment page (`sslredirect` request parameter)
  - The `$ssl` parameter of `Config::getOutUrl()`, `Config::getUrl()`, `Config::getImageUrl()`, `Config::getPictureUrl()`, `Config::getTemplateUrl()`, `Config::getResourceUrl()`, `Config::getShopUrlByLanguage()`, `Config::getMallShopUrl()`, `ViewConfig::getImageUrl()`, `PictureHandler::getPicUrl()`, `PictureHandler::getProductPicUrl()`, `PictureHandler::getPictureInfo()` and `PictureHandler::getAltImageUrl()`, the following parameters will move one position forward
  - The `$blToSession` and `$blSecure` parameters of `UtilsServer::setOxCookie()`, `$blHttpOnly` will move two positions forward
  - The `$imageDirNoSSL` parameter of `Email::includeImages()`
  - The `SSLAltImageUrl` view variable of `ArticleDetails` and the `basesslurl` view data of `LanguageMain`
  - `sSSLShopURL`, `sAdminSSLURL`, `sSSLAltImageUrl` and `sSSLAltImageDir` config parameters
  - `aLanguageSSLURLs` and `sMallSSLShopURL` config parameters, use `aLanguageURLs` and `sMallShopURL` instead

### Fixed
- Password reset links are invalidated when the user's email address or password is changed
- `oe:database:migrate --write-sql` keeps the SQL of all migration sets instead of only the last one [#0006869](https://bugs.oxid-esales.com/view.php?id=6869)
- `class_exists()` checks for unknown classes no longer cause a circular service reference while the DI container is being built
- Product SEO url context is kept when the SEO settings are saved in the admin area, existing entries need to be saved again [#0007952](https://bugs.oxid-esales.com/view.php?id=7952)

### Removed
- PHP v8.3 support
- PHPUnit v12 support
- `oxid-esales/oxideshop-doctrine-migration-wrapper` composer dependency