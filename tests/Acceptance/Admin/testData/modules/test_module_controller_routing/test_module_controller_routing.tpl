<h1 class="page-header">Test module for controller routing noNamespace - [{$the_module_message}]</h1>

<form action="[{$oViewConf->getSelfActionLink()}]" name="MyModuleControllerAction" method="post" role="form">
    <div>
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="cl" value="test_module_controller_routing_MyModuleController">
        <input type="hidden" name="fnc" value="displayMessage">
        <input type="text" size="10" maxlength="200" name="mymodule_message" value="[{$the_module_message}]">
        <button type="submit" id="MyModuleControllerActionButton" class="submitButton">[{oxmultilang ident="SUBMIT"}]</button>
    </div>
</form>
