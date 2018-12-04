[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign skip_onload="true"}]

<script type="text/javascript">
    if (top)
    {   top.sShopTitle   = "[{$actshopobj->oxshops__oxname->getRawValue()|oxaddslashes}]";
        top.sMenuItem    = "[{oxmultilang ident="GENIMPORT_MENUITEM"}]";
        top.sMenuSubItem = "[{oxmultilang ident="GENIMPORT_MENUSUBITEM"}]";
        top.sWorkArea    = "[{$_act}]";
        top.setTitle();
    }
</script>

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<div id="genimportnav_vline"></div>

<ul class="genimportnav">
    [{if $sNavStep == 2 || $sNavStep == 3}]
        [{assign var="blLinkToFirstStep" value="1"}]
    [{/if}]
    <li class="[{if $sNavStep == 1}]active[{/if}][{if $blLinkToFirstStep}] link[{/if}]">[{if $blLinkToFirstStep}]<a href="[{$oViewConf->getSelfLink()}]&cl=genimport_main">[{/if}][{oxmultilang ident="GENIMPORT_STEP"}] 1[{if $blLinkToFirstStep}]</a>[{/if}]</li>
    <li class="[{if $sNavStep == 2}]active[{/if}]">[{oxmultilang ident="GENIMPORT_STEP"}] 2</li>
    <li class="[{if $sNavStep == 3}]active[{/if}]">[{oxmultilang ident="GENIMPORT_FINISH"}]</li>
</ul>

<div id="genimportpage">
    <form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="oxid" value="1">
        <input type="hidden" name="cl" value="">
    </form>

    [{if $sNavStep == 1}]
    <h3>[{oxmultilang ident="GENIMPORT_STEP_1_TITLE"}]</h3>
    [{if $Errors.genimport}]
    <div class="errorbox">
        [{foreach from=$Errors.genimport item=oEr key=key}]
            <p>[{$oEr->getOxMessage()}]</p>
        [{/foreach}]
    </div>
    <br>
    [{/if}]

    [{if $iRepeatImport}]
    <p>[{oxmultilang ident="GENIMPORT_IMPORTDONE"}]</p>
    <p>[{oxmultilang ident="GENIMPORT_TOTALROWS"}]: <b>[{$iTotalRows}]</b></p>
    <p>[{oxmultilang ident="GENIMPORT_REPEATINGIMPORT"}]...</p>
    <br>
    [{/if}]


    <table cellspacing="0" cellpadding="0" border="0">
        <form name="myedit" id="myedit" method="post" action="[{$oViewConf->getSelfLink()}]" enctype="multipart/form-data">
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="cl" value="genimport_main">
        <input type="hidden" name="fnc" value="">
        <input type="hidden" name="sNavStep" value="[{$sNavStep}]">
        <tr>
            <td class="edittext" width="60" height="40">[{oxmultilang ident="GENIMPORT_TABLE"}]:</td>
            <td class="edittext">
                <select name="sType" [{$readonly}] style="width: 210px;">
                [{foreach from=$aImportTables item=sTableType key=sTableTypePrefix}]
                    <option value="[{$sTableTypePrefix}]">[{$sTableType}]</option>
                [{/foreach}]
                </select>
                [{oxinputhelp ident="HELP_GENIMPORT_TABLE"}]
            </td>
        </tr>
        <tr>
            <td class="edittext" height="40">[{oxmultilang ident="GENIMPORT_CSVFILE"}]:</td>
            <td class="edittext">
            <input type="file" class="edittext" style="width: 210px;" name="csvfile" [{$readonly}]>
            [{oxinputhelp ident="HELP_GENIMPORT_CSVFILE"}]
            </td>
        </tr>
        <tr>
            <td class="edittext" height="40" nowrap>[{oxmultilang ident="GENIMPORT_FIELDSTERMINATEDBY"}]:</td>
            <td class="edittext">
            <input type="input" class="edittext" style="width: 20px;" maxlength="1" name="sGiCsvFieldTerminator" value="[{$sGiCsvFieldTerminator}]" [{$readonly}]>
            </td>
        </tr>
        <tr>
            <td class="edittext" height="40">[{oxmultilang ident="GENIMPORT_FIELDSENCLOSEDBY"}]:</td>
            <td class="edittext">
            <input type="input" class="edittext" style="width: 20px;" maxlength="1" name="sGiCsvFieldEncloser" value="[{$sGiCsvFieldEncloser}]" [{$readonly}]>
            </td>
        </tr>
        <tr>
            <td class="edittext" height="40"></td>
            <td class="edittext">
                <input type="checkbox" class="edittext" name="blContainsHeader" value="1" [{$readonly}]>[{oxinputhelp ident="HELP_GENIMPORT_FIRSTCOLHEADER"}] [{oxmultilang ident="GENIMPORT_FIRSTCOLHEADER"}]
            </td>
        </tr>
        <tr>
            <td class="edittext" height="40"></td>
            <td class="edittext">
            <input type="submit" class="edittext" style="width: 210px;" name="save" value="[{oxmultilang ident="GENIMPORT_UPLOADFILE"}]" [{$readonly}]>
            </td>
        </tr>
        </form>
    </table>
    [{/if}]

    [{if $sNavStep == 2}]
    <h3>[{oxmultilang ident="GENIMPORT_STEP_2_TITLE"}]</h3>

    [{if $Errors.genimport}]
    <div class="errorbox">
        [{foreach from=$Errors.genimport item=oEr key=key}]
            <p>[{$oEr->getOxMessage()}]</p>
        [{/foreach}]
    </div>
    [{/if}]

    <p>[{oxmultilang ident="GENIMPORT_ASSIGNFIELDS"}] <b>"[{$sImportTable}]"</b></p>
    <table cellspacing="1" cellpadding="0" border="0" class="genImportFieldsAssign">
        <form name="myedit" id="myedit" method="post" action="[{$oViewConf->getSelfLink()}]">
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="cl" value="genimport_main">
        <input type="hidden" name="fnc" value="">
        <input type="hidden" name="sNavStep" value="[{$sNavStep}]">
        <input type="hidden" name="sType" value="[{$sType}]">
        <thead>
            <td class="edittext">[{oxmultilang ident="GENIMPORT_CSVFILE"}]</td>
            <td class="edittext">[{oxmultilang ident="GENIMPORT_DBFIELDS"}]</td>
        </thead>
        [{foreach from=$aCsvFieldsList item=sCsvField}]
        <tr>
            <td class="edittext" width="1%" nowrap>[{$sCsvField}]: &nbsp;</td>
            <td class="edittext">
                <select name="aCsvFields[[{$sCsvField}]]" style="width: 210px;">
                    <option value="">--- [{oxmultilang ident="GENIMPORT_SKIP"}] ---</option>
                [{foreach from=$aDbFieldsList item=sDbField}]
                    <option value="[{$sDbField}]" [{if $sDbField == $sCsvField}]selected[{/if}]>[{$sDbField}]</option>
                [{/foreach}]
                </select>
                [{oxinputhelp ident="HELP_GENIMPORT_DBFIELDS"}]
            </td>
        </tr>
        [{/foreach}]
        <tr>
            <td class="edittext"></td>
            <td class="edittext">
                <input type="checkbox" class="edittext" name="iRepeatImport" value="1"> [{oxmultilang ident="GENIMPORT_REPEATIMPORT"}]
                [{oxinputhelp ident="HELP_GENIMPORT_REPEATIMPORT"}]
                <br>
            </td>
        </tr>
        <tr>
            <td class="edittext"></td>
            <td class="edittext">
            <input type="submit" class="edittext" style="width: 210px;" name="save" value="[{oxmultilang ident="GENIMPORT_BEGINIMPORT"}]" [{$readonly}]>
            </td>
        </tr>
        </form>
    </table>
    [{/if}]

    [{if $sNavStep == 3}]
    <h3>[{oxmultilang ident="GENIMPORT_STEP_3_TITLE"}]</h3>

    [{if $Errors.genimport}]
    <div class="errorbox">
        [{foreach from=$Errors.genimport item=oEr key=key}]
            <p>[{$oEr->getOxMessage()}]</p>
        [{/foreach}]
    </div>
    <p>[{oxmultilang ident="GENIMPORT_IMPORTDONEWITHERRORS"}]</p>
    [{else}]
    <p>[{oxmultilang ident="GENIMPORT_IMPORTDONE"}]</p>
    [{/if}]

    <p>[{oxmultilang ident="GENIMPORT_TOTALROWS"}]: <b>[{$iTotalRows}]</b></p>
    [{/if}]


</div>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]