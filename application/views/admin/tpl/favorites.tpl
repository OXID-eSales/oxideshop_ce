<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html id="top" >
<head>
    <title>[{ oxmultilang ident="MAIN_TITLE" }]</title>
    <link rel="stylesheet" href="[{$oViewConf->getResourceUrl()}]main.css">
    <link rel="stylesheet" href="[{$oViewConf->getResourceUrl()}]colors.css">
    <meta http-equiv="Content-Type" content="text/html; charset=[{$charset}]">
</head>
<body>

<script type="text/javascript">
    parent.sShopTitle = "[{$actshop|oxaddslashes}]";
    parent.setTitle();
</script>

<h1>[{ oxmultilang ident="NAVIGATION_FAVORITES" }]</h1>
<p class="desc">
    <b>[{ oxmultilang ident="FAVORITES_DESC" }]</b>
</p>


    <form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post" target="navigation">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="cl" value="navigation">
    <input type="hidden" name="favorites[]" value="">
    <select size="25" name="favorites[]" multiple="multiple" style="min-width:30%">
    [{foreach from=$menustructure item=menuholder }]
        [{if $menuholder->nodeType == XML_ELEMENT_NODE && $menuholder->childNodes->length }]

            [{foreach from=$menuholder->childNodes item=menuitem }]
                [{if $menuitem->nodeType == XML_ELEMENT_NODE && $menuitem->childNodes->length }]
                <optgroup label="[{ oxmultilang noerror=true ident=$menuitem->getAttribute('name')|default:$menuitem->getAttribute('id') }]">
                    [{foreach from=$menuitem->childNodes item=submenuitem }]
                        [{if $submenuitem->nodeType == XML_ELEMENT_NODE}]
                            <option value="[{ $submenuitem->getAttribute('cl') }]" [{if in_array($submenuitem->getAttribute('cl'),$aFavorites) }]selected[{/if}]>[{ oxmultilang noerror=true ident=$submenuitem->getAttribute('name')|default:$submenuitem->getAttribute('id') }]</option>
                        [{/if}]
                    [{/foreach}]
                </optgroup>
                [{/if}]
            [{/foreach}]
        [{/if}]
    [{/foreach}]
    </select><br>
    <input type="submit" value="[{ oxmultilang ident="GENERAL_SAVE" }]">
    </form>
</body>
</html>