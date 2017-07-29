[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign box="box"}]

<div id="container">

    <form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="oxid" value="[{$oxid}]">
        <input type="hidden" name="cl" value="module_main">
        <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
    </form>

     <div id="infoContent">

         [{if $aDeletedExt}]
            <div class="msgBox">

                <div class="info">
                    <p>[{oxmultilang ident="MODULE_EXTENSIONISDELETED"}]</p>
                    <p>[{oxmultilang ident="MODULE_DELETEEXTENSION"}]</p>

                    <table cellspacing="0" cellpadding="0" border="0" width="98%">
                        <tr>
                            <td class="listheader first">[{oxmultilang ident="MODULE_ID"}]</td>
                            <td class="listheader">[{oxmultilang ident="MODULE_PROBLEMATIC_FILES"}]</td>
                        </tr>
                        [{foreach from=$aDeletedExt item=aModules key=sModuleId}]
                            [{assign var="listclass" value=listitem$blWhite}]
                            <tr>
                                <td valign="top" class="[{$listclass}]">[{$sModuleId}]</td>
                                <td valign="top" class="[{$listclass}]">
                                    <ul>
                                    [{foreach from=$aModules.extensions item=mFile key=sClassName}]
                                        [{if is_array($mFile)}]
                                            [{foreach from=$mFile item=sFile}]
                                                <li>[{if !is_int($sClassName)}][{$sClassName}] =&gt; [{/if}][{$sFile}]</li>
                                            [{/foreach}]
                                        [{else}]
                                        <li>[{if !is_int($sClassName)}][{$sClassName}] =&gt; [{/if}][{$mFile}]</li>
                                        [{/if}]
                                    [{/foreach}]
                                    [{foreach from=$aModules.files item=sFile key=sClassName}]
                                        <li>[{if !is_int($sClassName)}][{$sClassName}] =&gt; [{/if}][{$sFile}]</li>
                                    [{/foreach}]
                                    </ul>
                                </td>
                            </tr>
                            [{if $blWhite == "2"}]
                                [{assign var="blWhite" value=""}]
                            [{else}]
                                [{assign var="blWhite" value="2"}]
                            [{/if}]
                        [{/foreach}]
                    </table>
                </div>

                <div>
                    <form name="remove" action="[{$oViewConf->getSelfLink()}]" method="post">
                        [{$oViewConf->getHiddenSid()}]
                        <input type="hidden" name="cl" value="module_sortlist">
                        <input type="hidden" name="fnc" value="remove">
                        <input type="hidden" name="oxid" value="[{$oxid}]">
                        <input type="hidden" name="aModules" value="">
                        <input type="hidden" name="updatelist" value="1">
                        <input type="submit" name="yesButton" class="saveButton" value="[{oxmultilang ident="GENERAL_YES"}]">
                        <input type="submit" name="noButton" class="saveButton" value="[{oxmultilang ident="GENERAL_NO"}]">
                    </form>
                </div>
            </div>
         [{else}]

             [{if $aExtClasses}]
                <ul class="sortable" id="aModulesList">
                [{foreach from=$aExtClasses item=aModuleNames key=sClassName}]
                    <li id="[{$sClassName}]">
                        <span>[{$sClassName|replace:'---':'&#92;'}]</span>
                        <ul class="sortable2" id="[{$sClassName}]_modules">
                            [{foreach from=$aModuleNames item=sModule}]
                                [{if is_array($aDisabledModules) && in_array($sModule, $aDisabledModules)}]
                                [{assign var="cssDisabled" value="disabled"}]
                                [{else}]
                                [{assign var="cssDisabled" value=""}]
                                [{/if}]
                                <li id="[{$sModule}]"><span class="[{$cssDisabled}]">[{$sModule}]</span></li>
                            [{/foreach}]
                        </ul>
                    </li>
                [{/foreach}]
                </ul>
             [{/if}]
         [{/if}]
     </div>


    [{oxscript add="$('#aModulesList').oxModulesList();" priority=10}]

    [{oxscript include="js/libs/jquery.min.js"}]
    [{oxscript include="js/libs/jquery-ui.min.js"}]
    [{oxscript include="js/libs/json2.js"}]

    [{oxscript include="js/widgets/oxmoduleslist.js"}]

</div>

[{if !$aDeletedExt && $aExtClasses}]
    <div id="footerBox">
        <div class="buttonsBox">
            <form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
                [{$oViewConf->getHiddenSid()}]
                <input type="hidden" name="cl" value="module_sortlist">
                <input type="hidden" name="fnc" value="save">
                <input type="hidden" name="oxid" value="[{$oxid}]">
                <input type="hidden" name="aModules" value="">
                <input type="button" name="saveButton" class="saveButton" value="[{oxmultilang ident="GENERAL_SAVE"}]" disabled>
            </form>
        </div>

        <div class="description">
            <p>
                [{oxmultilang ident="MODULE_DRAGANDDROP"}]

            </p>
        </div>
    </div>
[{/if}]


[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]

