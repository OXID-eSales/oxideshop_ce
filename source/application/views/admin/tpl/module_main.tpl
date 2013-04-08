[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign box="box"}]

[{if $updatenav }]
    [{oxscript add="top.oxid.admin.reloadNavigation('`$shopid`');" priority=10}]
[{/if}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="module_main">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>

[{oxscript include="js/libs/jquery.min.js"}]
[{oxscript include="js/libs/jquery-ui.min.js"}]

[{if $oModule}]

<table cellspacing="10" width="98%">
    <tr>
        <td width="245" valign="top">
            [{if $oModule->getInfo('thumbnail')}]
            <img src="[{ $oViewConf->getBaseDir() }]/modules/[{$oModule->getModulePath()}]/[{$oModule->getInfo('thumbnail')}]" hspace="20" vspace="10"></td>
        [{else}]
    <img src="[{ $oViewConf->getResourceUrl() }]bg/module.png" hspace="20" vspace="10"></td>
    [{/if}]
    <td width="" valign="top">
        <h1 style="color:#000;font-size:25px;">[{$oModule->getTitle()}]</h1>
        <p>[{$oModule->getDescription()}]</p>
        <hr>

        <dl class="moduleDesc clear">
            <dt>[{ oxmultilang ident="MODULE_VERSION" }]</dt>
            <dd>[{$oModule->getInfo('version')|default:'-'}]</dd>

            <dt>[{ oxmultilang ident="MODULE_AUTHOR" }]</dt>
            <dd>[{$oModule->getInfo('author')|default:'-'}]</dd>

            <dt>[{ oxmultilang ident="GENERAL_EMAIL" }]</dt>
            <dd>
                [{ if $oModule->getInfo('email') }]
                    <a href="mailto:[{$oModule->getInfo('email')}]">[{$oModule->getInfo('email')}]</a>
                [{else}]
                    -
                [{/if}]
            </dd>

            <dt>[{ oxmultilang ident="GENERAL_URL" }]</dt>
            <dd>
                [{ if $oModule->getInfo('url') }]
                    <a href="[{$oModule->getInfo('url')}]" target="_blank">[{$oModule->getInfo('url')}]</a>
                [{else}]
                    -
                [{/if}]
            </dd>
        </dl>
    </td>

    <td width="25" style="border-right: 1px solid #ddd;">

    </td>
    <td width="260" valign="top">
        [{*assign var='_sError' value=$oModule->checkForActivationErrors()*}]

        [{if ( $oModule->isFile() )}]
        <div class="info">
            <div class="infoNotice"> [{ oxmultilang ident="MODULE_STANDALONEFILE" }]</div>
        </div>
        [{/if}]

        [{if !$oModule->hasMetadata() && !$oModule->isRegistered()}]
        <div class="info">
            [{ oxmultilang ident="MODULE_ENABLEACTIVATIONTEXT" }]
        </div>
        [{/if}]
        [{if !$_sError}]

        [{if $oModule->hasMetadata() || $oModule->isRegistered() }]
        <form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
            <div>
                [{ $oViewConf->getHiddenSid() }]
                <input type="hidden" name="cl" value="module_main">
                <input type="hidden" name="updatelist" value="1">
                <input type="hidden" name="oxid" value="[{$oModule->getId()}]">
                [{if $oModule->hasMetadata() || $oModule->isLegacy()}]
                    [{if $oModule->isActive()}]
                    <input type="hidden" name="fnc" value="deactivateModule">
                    <div align="center">
                        <input type="submit" id="module_deactivate" class="saveButton" value="[{ oxmultilang ident="MODULE_DEACTIVATE" }]">
                    </div>
                    [{else}]
                    <input type="hidden" name="fnc" value="activateModule">

                    <div align="center">
                        <input type="submit" id="module_activate" class="saveButton" value="[{ oxmultilang ident="MODULE_ACTIVATE" }]">
                    </div>
                    [{/if}]
                [{/if}]
            </div>
        </form>
        [{/if}]

        [{if !$oModule->hasMetadata()}]
        <form name="myedit2" id="myedit2" action="[{ $oViewConf->getSelfLink() }]" method="post">
            <div>
                [{ $oViewConf->getHiddenSid() }]
                <input type="hidden" name="cl" value="module_main">
                <input type="hidden" name="oxid" value="[{$oModule->getId()}]">
                <input type="hidden" name="updatelist" value="1">
                <input type="hidden" name="fnc" value="saveLegacyModule">

                [{if $oModule->isRegistered()}]
                <div id="editLegacyInfoBox" class="info" align="center">
                    <br>
                    <a href="#" id="editLegacyInfoLink" class="textButton">[{ oxmultilang ident="MODULE_EDITEXTENSION" }]</a>
                </div>
                [{/if}]

                <div id="legacyExtensionDataBox" [{if $oModule->isRegistered()}]style="display: none;"[{/if}]>
                    <fieldset>
                        <div class="fieldDesc">
                            [{ oxmultilang ident="MODULE_ENTERMODULENAME" }]:
                        </div>
                        <input type="text" name="moduleName" style="width: 233px;" value="[{$sModuleName}]">
                    </fieldset>

                    <fieldset>
                        <div class="fieldDesc">
                            [{ oxmultilang ident="MODULE_ENTERMODULEID" }]:
                        </div>
                        <input type="text" name="moduleId" style="width: 233px;" value="[{$sModuleId}]">
                    </fieldset>

                    <fieldset>
                        <div class="fieldDesc">
                            [{ oxmultilang ident="MODULE_ENTERMODULEEXTENDSCLASSES" }]:
                        </div>
                        [{ if ($oModule->getInfo('extend')) }]
                        [{assign var="sModuleClasses" value=""}]
                        [{foreach from=$oModule->getInfo('extend') item=moduleClass key=oxClass}]
                        [{ assign var="sModuleClasses" value="`$sModuleClasses``$oxClass`=>`$moduleClass`\n" }]
                        [{/foreach}]
                        [{/if}]
                        <textarea name="aExtendedClasses" style="width: 233px; height: 70px;">[{$sModuleClasses}]</textarea>
                    </fieldset>

                    <input type="submit" id="module_saveLegacyModule" class="saveButton" value="[{ oxmultilang ident="GENERAL_SAVE" }]">
                </div>

            </div>
        </form>
        [{/if}]
        [{else}]
        <div class="error">[{ oxmultilang ident="MODULE_DRAGANDDROP" }]</div>
        [{/if}]
    </td>

</tr>
</table>
[{/if}]
[{include file="bottomnaviitem.tpl"}]
<script language="javascript">
    //$("#legacyExtensionDataBox").click(function() {
    //    $("#legacyExtensionDataBox").toggle();
    //});
</script>

[{oxscript add="$('#editLegacyInfoLink').click(function() { $('#editLegacyInfoBox').hide(); $('#legacyExtensionDataBox').slideToggle('fast'); } );" priority=10}]

[{include file="bottomitem.tpl"}]
