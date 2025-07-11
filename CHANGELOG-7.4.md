# Change Log for OXID eShop Community Edition Core Component

## v7.4.0 - unreleased

### Added
- `oe:admin:create-user` command displays an error message when attempting to create an admin user that already exists
- Display a clear error message if the VAT-ID prefix is missing during user registration
- Value object for Oxid Unique Id

### Deprecated

- `ShopAdapterInterface::generateUniqueId()` use `Id::generate()` instead
- `UtilsObject::generateUId()` use `Id::generate()` instead
- The partner related method `getBelboonParam()` of class `BaseController`
- `ContainerBuilderFactory` will be removed

### Fixed
- Multilanguage field detection with lowercase field names [#0005244](https://bugs.oxid-esales.com/view.php?id=5244)
- Allow "Core" to be used as manufacturer name in URLs [#0005242](https://bugs.oxid-esales.com/view.php?id=5242)
- Mismatching html tags in setup templates [#0006144](https://bugs.oxid-esales.com/view.php?id=6144)
