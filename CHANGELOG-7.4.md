# Change Log for OXID eShop Community Edition Core Component

## v7.4.0 - unreleased

### Added
- `oe:admin:create-user` command displays an error message when attempting to create an admin user that already exists
- Display a clear error message if the VAT-ID prefix is missing during user registration
- Value object for Oxid Unique Id
- Useful interfaces RequestInterface and SessionInterface
- Support for edition and project root locator classes 

### Deprecated
- `ShopAdapterInterface::generateUniqueId()` use `Id::generate()` instead
- `UtilsObject::generateUId()` use `Id::generate()` instead
- The partner related method `getBelboonParam()` of class `BaseController`
- `ContainerBuilderFactory` will be removed
- `Request::getRequestParameter()` use `get` instead
- Methods `hasVariable`, `setVariable`, `getVariable` and `deleteVariable` of `Session` use new methods of `SessionInterface`
- Method `Language::translateString()` use `ShopAdapterInterface::translateString()`
- Method `TableViewNameGenerator::getViewName()` use `ShopAdapterInterface::generateDatabaseViewName()`

### Fixed
- Multilanguage field detection with lowercase field names [#0005244](https://bugs.oxid-esales.com/view.php?id=5244)
- Allow "Core" to be used as manufacturer name in URLs [#0005242](https://bugs.oxid-esales.com/view.php?id=5242)
- Mismatching html tags in setup templates [#0006144](https://bugs.oxid-esales.com/view.php?id=6144)
- Fixed incorrect email validation error when wrong password is entered in delivery address form [#0006026](https://bugs.oxid-esales.com/view.php?id=6026)
- Order remark no longer defaults to 1 when the input field is missing in the frontend [#0007721](https://bugs.oxid-esales.com/view.php?id=7721)
- Notice list and wish list data loss caused by basket expiration [#0007293](https://bugs.oxid-esales.com/view.php?id=7293)
- Handle possible null values [PR-995](https://github.com/OXID-eSales/oxideshop_ce/pull/995)

### Changed
- Newsletter export trims salutation values, preventing discrepancies between empty string and spaces
