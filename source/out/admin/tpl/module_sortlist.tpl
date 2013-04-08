[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign box="box"}]

<div id="container">

    <form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="oxid" value="[{ $oxid }]">
        <input type="hidden" name="cl" value="module_main">
        <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
    </form>

     <div id="infoContent">

         [{ if $aDeletedExt }]
            <div class="msgBox">

                <div class="info">
                    <p>[{ oxmultilang ident="MODULE_EXTENSIONISDELETED" }]</p>
                    <p>[{ oxmultilang ident="MODULE_DELETEEXTENSION" }]</p>
                    <ul>
                        [{foreach from=$aDeletedExt item=aModules key=sOxClass }]
                            [{foreach from=$aModules item=sModule }]
                            <li>[{$sOxClass}]=&gt;[{$sModule}]</li>
                            [{/foreach}]
                        [{/foreach}]
                    </ul>
                </div>

                <div>
                    <form name="remove" action="[{ $oViewConf->getSelfLink() }]" method="post">
                        [{ $oViewConf->getHiddenSid() }]
                        <input type="hidden" name="cl" value="module_sortlist">
                        <input type="hidden" name="fnc" value="remove">
                        <input type="hidden" name="oxid" value="[{ $oxid }]">
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
                [{foreach from=$aExtClasses item=aModuleNames key=sClassName }]
                    <li id="[{$sClassName}]">
                        <span>[{$sClassName}]</span>
                        <ul class="sortable2" id="[{$sClassName}]_modules">
                            [{foreach from=$aModuleNames item=sModule }]
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

[{ if !$aDeletedExt && $aExtClasses}]
    <div id="footerBox">
        <div class="buttonsBox">
            <form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
                [{ $oViewConf->getHiddenSid() }]
                <input type="hidden" name="cl" value="module_sortlist">
                <input type="hidden" name="fnc" value="save">
                <input type="hidden" name="oxid" value="[{ $oxid }]">
                <input type="hidden" name="aModules" value="">
                <input type="button" name="saveButton" class="saveButton" value="[{ oxmultilang ident="GENERAL_SAVE" }]" disabled>
            </form>
        </div>

        <div class="description">
            <p>
                [{ oxmultilang ident="MODULE_DRAGANDDROP" }]

            </p>
        </div>
    </div>
[{/if}]


[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]

