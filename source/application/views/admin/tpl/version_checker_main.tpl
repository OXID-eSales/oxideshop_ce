[{include file="headitem.tpl" title="OXCHKVERSION_MAIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
    <!--

    function handleSubmit()
    {   var aButton = document.myedit.submitButton;
        aButton.disabled = true;
    }
    //-->
</script>

<p>[{ oxmultilang ident="OXCHKVERSION_INTROINFORMATION" }]</p>
<p>[{ oxmultilang ident="OXCHKVERSION_INTROINFORMATION_NO_PERSONAL_INFO" }]</p>
<p>[{ oxmultilang ident="OXCHKVERSION_INTROINFORMATION_DATA_TRANSMITTED" }]</p>
    <ul>
        <li>[{ oxmultilang ident="OXCHKVERSION_INTROINFORMATION_FILENAME_TO_BE_CHECKED" }]</li>
        <li>[{ oxmultilang ident="OXCHKVERSION_INTROINFORMATION_MD5_CHECKSUM" }]</li>
        <li>[{ oxmultilang ident="OXCHKVERSION_INTROINFORMATION_VERSION_DETECTED" }]</li>
        <li>[{ oxmultilang ident="OXCHKVERSION_INTROINFORMATION_REVISION_DETECTED" }]</li>
    </ul>
<p>[{ oxmultilang ident="OXCHKVERSION_INTROINFORMATION_MORE_INFORMATION" }] <strong><a href="http://www.oxid-esales.com/de/news/blog/shop-checking-tool-oxchkversion-v3" target=_blank>[{ oxmultilang ident="OXCHKVERSION_INTROINFORMATION_OXID_ESALES_BLOG" }]</a></strong>.</p>
<p>[{ oxmultilang ident="OXCHKVERSION_INTROINFORMATION_CONTACT_US" }] <strong><a href="[{$oView->getSupportContactForm()}]" target=_blank>[{ oxmultilang ident="OXCHKVERSION_INTROINFORMATION_ONLINE_CONTACT_FORM" }]</a></strong>.</p>


[{ if !empty($sErrorMessage) }]
<p><span style="color: red"><b>[{ oxmultilang ident="OXCHKVERSION_ERRORMESSAGETEMPLATE" }]</b></span></p>
    <span style="color: red">[{ $sErrorMessage }]</span>
[{else}]
    <form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" onsubmit="handleSubmit()" method="post">
        <input type="hidden" name="cl" value="version_checker_main">
        <input type="hidden" name="fnc" value="startCheck">
        <input type="checkbox" name="listAllFiles" value="listAllFiles" id="listAllFiles">
        <label for="listAllFiles">[{ oxmultilang ident="OXCHKVERSION_FORM_LIST_ALL_FILES" }]</label>
        <br><br>
        <input type="submit" class="edittext" id="submitButton" name="submitButton" value=" [{ oxmultilang ident="OXCHKVERSION_FORM_START_CHECK" }] " >

    </form>
[{ /if}]

[{ if !empty($sResult) }]
<h1>[{ oxmultilang ident="OXCHKVERSION_RESULT_SUCCESSFUL" }]</h1>
<h2><strong><a href="[{ $oViewConf->getSelfLink() }]&amp;cl=version_checker_main&amp;fnc=downloadResultFile">[{ oxmultilang ident="OXCHKVERSION_DOWNLOAD_FILE" }]</a></strong>.</h2>

<h3>[{ oxmultilang ident="OXCHKVERSION_RESULT" }]:</h3>
    <p>
    [{ $sResult }]
    </p>
[{ /if}]

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]