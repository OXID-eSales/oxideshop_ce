# Change Log for OXID eShop Community Edition Core Component

## v7.6.0 - Unreleased

### Added
- `oe:database:migrate` console command
- Doctrine migrations can be registered via the `oxid_esales.migration_path_provider` DI tag (`MigrationPathProviderInterface`)
- `SeoEncoderArticle::generateArticleCategoryUri()` method for generating an article's SEO URI for a specific category

### Changed
- Storefront user data updates now accept only profile and address related fields
- Console commands return consistent exit codes on success (0) and failure (1)
- Default bcrypt password hashing cost was increased [#0007727](https://bugs.oxid-esales.com/view.php?id=7727)
- Remote address resolution now uses Symfony `Request::getClientIp()` — proxy headers are no longer trusted by default, configure trusted IPs via `oxid_esales.request.trusted_proxies` DI parameter
- Detect HTTPS behind SSL offloaders via forwarded headers from trusted proxies

### Deprecated
- `Argon2IPasswordHashService`
- `ModuleActivateCommand::MESSAGE_MODULE_ACTIVATED`
- `ModuleActivateCommand::MESSAGE_MODULE_NOT_FOUND`
- `ModuleDeactivateCommand::MESSAGE_MODULE_DEACTIVATED`
- `ModuleDeactivateCommand::MESSAGE_MODULE_NOT_FOUND`
- `UtilsServer::getRemoteAddress()`
- `ViewConfig::getRemoteAddress()`
- `MigrationExecutorInterface`
- `MigrationExecutor`
- `Utilities::executeExternalDatabaseMigrationCommand()`
- `oxid-esales/oxideshop-doctrine-migration-wrapper` package and `oe-eshop-db_migrate` script, use `oe:database:migrate` instead
- `SeoEncoderArticle::createArticleCategoryUri()`, use `generateArticleCategoryUri()` instead

### Fixed
- Cascade delete now removes all related records when deleting a user by ID [#0007138](https://bugs.oxid-esales.com/view.php?id=7138)
- Case-sensitive column name handling in admin order article search [#0006025](https://bugs.oxid-esales.com/view.php?id=6025)
- Endless redirect loop and wrong URL generation [#0007770](https://bugs.oxid-esales.com/view.php?id=7770)

### Removed
