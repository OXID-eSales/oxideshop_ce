[{if $oxid != "-1"}]
<script type="text/javascript">
<!--
function JumpVariant(obj)
{
    var oTransfer = document.getElementById("transfer");
    oTransfer.oxid.value=obj.value;
    oTransfer.cl.value='article_main';

    //forcing edit frame to reload after submit
    top.forceReloadingEditFrame();

    var oSearch = parent.list.document.getElementById("search");
    oSearch.oxid.value=obj.value;
    oSearch.submit();
}
//-->
</script>
<table cellspacing="2" cellpadding="2" border="0">
  <tr>
    <td>
      <select name="art_variants" onChange="Javascript:JumpVariant(this);" class="editinput">
        [{foreach from=$thisvariantlist key=num item=variant}]
          <option value="[{$variant[0]}]" [{if $oxid eq $variant[0]}]selected[{/if}]>[{$variant[1]}]</option>
        [{/foreach}]
      </select>
    </td>
  </tr>
</table>
[{/if}]
