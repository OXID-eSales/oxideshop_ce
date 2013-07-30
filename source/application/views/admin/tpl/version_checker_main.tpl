[{include file="headitem.tpl" title="VERSIONCHECK_MAIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
    <!--

    function handleSubmit()
    {   var aButton = document.myedit.submitButton;
        aButton.disabled = true;
    }
    //-->
</script>

<p>This script is intended to check consistency of your OXID eShop.
    It collects names of php files and templates, detects their MD5 checksum,
    connects for each file to OXID\'s webservice to determine if it fits this shop version.
</p>
<p>It does neither collect nor transmit any license or personal information.</p>
<p>Data to be transmitted to OXID is:</p>
    <ul>
        <li>Filename to be checked</li>
        <li>MD5 checksum</li>
        <li>Version which was detected</li>
        <li>Revision which was detected</li>
    </ul>
<p>For more detailed information check out <strong><a href="http://www.oxid-esales.com/de/news/blog/shop-checking-tool-oxchkversion-v3" target=_blank>OXID eSales' Blog</a></strong>.</p>
<p>You can contact us using <strong><a href="[{$oView->getSupportContactForm()}]" target=_blank>Online Contact Form</a></strong>.</p>


[{ if !empty($sErrorMessage) }]
    <span style="color: red">[{ $sErrorMessage }]</span>
[{else}]
    <form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" onsubmit="handleSubmit()" method="post">
        <input type="hidden" name="cl" value="version_checker_main">
        <input type="hidden" name="fnc" value="startCheck">
        <input type="checkbox" name="listAllFiles" value="listAllFiles" id="listAllFiles">
        <label for="listAllFiles">List all files (also those which were OK)</label>
        <br><br>
        <input type="submit" class="edittext" id="submitButton" name="submitButton" value="[{ oxmultilang ident="GENERAL_CHECKSTART" }]" >

    </form>
[{ /if}]

[{ if !empty($sResult) }]
<h1>System check successful.</h1>
<h2>You can <strong><a href="[{ $oViewConf->getSelfLink() }]&amp;cl=version_checker_main&amp;fnc=downloadResultFile">download the result file here</a></strong>.</h2>

<h3>Version Checker Result:</h3>
    <p>
    [{ $sResult }]
    </p>
[{ /if}]

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]