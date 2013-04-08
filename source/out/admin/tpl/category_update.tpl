[{include file="headitem.tpl" title="CATEGORY_UPDATE_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
window.onload = function ()
{
    var oSearch = opener.parent.list.document.getElementById("search");
    oSearch.oxid.value='-1';
    oSearch.submit();
}
//-->
</script>

[{foreach from=$oView->getCatListUpdateInfo() item=curr_data }]
  [{ $curr_data }]
[{/foreach}]

<br>
&nbsp;&nbsp;&nbsp;<button onclick="window.close()">[{ oxmultilang ident="CATEGORY_UPDATE_CLOSE" }]</button>
<br><br>


[{include file="bottomitem.tpl"}]
