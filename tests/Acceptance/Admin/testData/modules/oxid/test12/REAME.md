### Test module #12 for oxajax container-class resolution

**Short description**

This test module is made for testing ajax functionality in OXID eShop.
It is used together with acceptance tests to simulate a module which has an OXID ajax based functionality.

NOTE: this module uses metadata Version 1 without own module namespaces.

**Detailed description**

* The module extends the module list menu to add a new tab (test_12_tab).
* New tab contains a button to create a popup for ajax functionality.
* The test_12_ajax_controller is reponsible for rendering the popup template.
* The ajax call to admin/oxajax.php is triggered by javascript when the popup gets displayed.
* For simplicity the result is rendered through a config value without implementing drag and drop features.
  Due to this decision the popup should be opened twice in order to see the result from an ajax call.
