[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
function editThis( sID )
{
    var oTransfer = top.basefrm.edit.document.getElementById( "transfer" );
    oTransfer.oxid.value = sID;
    oTransfer.cl.value = top.basefrm.list.sDefClass;

    //forcing edit frame to reload after submit
    top.forceReloadingEditFrame();

    var oSearch = top.basefrm.list.document.getElementById( "search" );
    oSearch.oxid.value = sID;
    oSearch.actedit.value = 0;
    oSearch.submit();
}
//-->
</script>

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="article_attribute">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>

[{ if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

  <table cellspacing="0" cellpadding="0" border="0" width="96%">

    <tr>
      <td valign="top" class="edittext">

        [{if $oxparentid }]
          <b>[{ oxmultilang ident="GENERAL_VARIANTE" }]<a href="Javascript:editThis('[{ $parentarticle->oxarticles__oxid->value}]');" class="edittext"><b>[{ $parentarticle->oxarticles__oxartnum->value }] [{ $parentarticle->oxarticles__oxtitle->value }]</b></a><br>
          <br>
        [{/if}]

          [{oxhasrights object=$edit readonly=$readonly }]
          <input type="button" value="[{ oxmultilang ident="ARTICLE_ATTRIBUTE_ASSIGNATTRIBUTE" }]" class="edittext" onclick="JavaScript:showDialog('&cl=article_attribute&aoc=1&oxid=[{ $oxid }]');">
          [{/oxhasrights}]

          [{ if !$edit->blForeignArticle }]
          <br><br>
          <a class="edittext" href="[{ $oViewConf->getSelfLink() }]&cl=attribute" target="_new"><b>[{ oxmultilang ident="ARTICLE_ATTRIBUTE_OPENINNEWWINDOW" }]</b></a>
          [{/if}]

      </td>

      <!-- Anfang rechte Seite -->
      <td valign="top" class="edittext" align="left" width="50%">
        [{oxhasrights object=$edit readonly=$readonly }]
          <input type="button" value="[{ oxmultilang ident="ARTICLE_ATTRIBUTE_ASSIGNSELECTLIST" }]" class="edittext" onclick="JavaScript:showDialog('&cl=article_attribute&aoc=2&oxid=[{ $oxid }]');">
        [{/oxhasrights}]
      </td>
      <!-- Ende rechte Seite -->
    </tr>
  </table>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
