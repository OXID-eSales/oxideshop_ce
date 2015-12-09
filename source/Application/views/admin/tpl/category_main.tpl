[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
function SchnellSortManager(oObj)
{   oRadio = document.getElementsByName("editval[oxcategories__oxdefsortmode]");
    if(oObj.value)
        for ( i=0; i<oRadio.length; i++)
            oRadio.item(i).disabled="";
    else
        for ( i=0; i<oRadio.length; i++)
            oRadio.item(i).disabled = true;
}

function DeletePic( sField )
{
    var oForm = document.getElementById("myedit");
    oForm.fnc.value="deletePicture";
    oForm.masterPicField.value=sField;
    oForm.submit();
}

function LockAssignment(obj)
{   var aButton = document.myedit.assignArticle;
    if ( aButton != null && obj != null )
    {
        if (obj.value > 0)
        {
            aButton.disabled = true;
        }
        else
        {
            aButton.disabled = false;
        }
    }
}
//-->
</script>
<!-- END add to *.css file -->
<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" id="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="category_main">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

[{if $readonly_fields}]
    [{assign var="readonly_fields" value="readonly disabled"}]
[{else}]
    [{assign var="readonly_fields" value=""}]
[{/if}]

<form name="myedit" id="myedit" enctype="multipart/form-data" action="[{$oViewConf->getSelfLink()}]" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="[{$iMaxUploadFileSize}]">
[{$oViewConf->getHiddenSid()}]
<input type="hidden" name="cl" value="category_main">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{$oxid}]">
<input type="hidden" name="editval[oxcategories__oxid]" value="[{$oxid}]">
<input type="hidden" name="masterPicField" value="">

[{if $oViewConf->isAltImageServerConfigured()}]
    <div class="warning">[{oxmultilang ident="ALTERNATIVE_IMAGE_SERVER_NOTE"}] [{oxinputhelp ident="HELP_ALTERNATIVE_IMAGE_SERVER_NOTE"}]</div>
[{/if}]

<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>
    <td valign="top" class="edittext">

    [{include file="include/category_main_form.tpl"}]

    </td>
    </tr>
</table>

</form>
[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
