[{capture append="oxidBlock_content"}]
    <h1 class="page-header">Metadata version 2.0 test module (with namespace) - [{$the_module_message}]</h1>

    <form action="[{$oViewConf->getSelfActionLink()}]" name="MyModuleControllerAction" method="post" role="form">
        <div>
            [{$oViewConf->getHiddenSid()}]
            <input type="hidden" name="cl" value="vendor1_metadatav2demo_mymodulecontroller">
            <input type="hidden" name="fnc" value="displayMessage">
            <input type="text" size="10" maxlength="200" name="mymodule_message" value="[{$the_module_message}]">
            <button type="submit" id="MyModuleControllerActionButton" class="submitButton">[{oxmultilang ident="SUBMIT"}]</button>
        </div>
    </form>

    [{/capture}]

[{include file="layout/page.tpl"}]
