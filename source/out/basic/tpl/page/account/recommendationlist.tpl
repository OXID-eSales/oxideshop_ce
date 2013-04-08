[{assign var="template_title" value="ACCOUNT_RECOMM_TITLE"|oxmultilangassign }]
[{include file="_header.tpl" title=$template_title location="ACCOUNT_RECOMM_LOCATION"|oxmultilangassign|cat:$template_title}]

[{include file="inc/account_header.tpl" active_link=8 }]
[{assign var="actvrecommlist" value=$oView->getActiveRecommList() }]
<strong id="test_recomListHeader1" class="boxhead">[{if $actvrecommlist}][{$actvrecommlist->oxrecommlists__oxtitle->value}][{else}][{ oxmultilang ident="ACCOUNT_RECOMM_NEWLIST" }][{/if}]</strong>
<div class="box info">
    <form action="[{ $oViewConf->getSelfActionLink() }]" name="saverecommlist" method="post">
      <div>
          [{ $oViewConf->getHiddenSid() }]
          [{ $oViewConf->getNavFormParams() }]
          <input type="hidden" name="fnc" value="saveRecommList">
          <input type="hidden" name="cl" value="account_recommlist">
          [{if $actvrecommlist}]
            <input type="hidden" name="recommid" value="[{$actvrecommlist->getId()}]">
          [{/if}]
      </div>

      [{if $actvrecommlist && $oView->isSavedList()}]
        [{ oxmultilang ident="ACCOUNT_RECOMM_LISTSAVED" }]
      [{/if}]

      <div class="dot_sep mid"></div>

      [{include file="inc/error.tpl" Errorlist=$Errors.user errdisplay="inbox"}]

      <table class="form" width="100%">
        <tr>
          <td><label>[{ oxmultilang ident="ACCOUNT_RECOMM_LISTTITLE" }]:</label></td>
          <td>
            <input type="text" name="recomm_title" size=73 maxlength=73 value="[{$actvrecommlist->oxrecommlists__oxtitle->value}]" >
            <span class="req">*</span>
          </td>
        </tr>
        <tr>
          <td><label>[{ oxmultilang ident="ACCOUNT_RECOMM_LISTAUTHOR" }]:</label></td>
          <td><input type="text" name="recomm_author" size=73 maxlength=73 value="[{if $actvrecommlist->oxrecommlists__oxauthor->value}][{$actvrecommlist->oxrecommlists__oxauthor->value}][{elseif !$actvrecommlist}][{ $oxcmp_user->oxuser__oxfname->value }] [{ $oxcmp_user->oxuser__oxlname->value }][{/if}]" ></td>
        </tr>
        <tr>
          <td valign="top"><label>[{ oxmultilang ident="ACCOUNT_RECOMM_LISTDESC" }]:</label></td>
          <td><textarea cols="70" rows="8" name="recomm_desc" >[{$actvrecommlist->oxrecommlists__oxdesc->value}]</textarea></td>
        </tr>
      </table>

      <div style="right">
        <span class="btn"><input id="test_recomListSave" type="submit" value="[{ oxmultilang ident="ACCOUNT_RECOMM_SAVE" }]" class="btn"></span>
      </div>

    </form>
</div>

  [{assign var="blEdit" value=true }]
  [{include file="inc/recomm_lists.tpl" blEdit=$blEdit template_title=$template_title }]
<br>



<div class="bar prevnext">
    <form action="[{ $oViewConf->getSelfActionLink() }]" name="order" method="post">
      <div>
          [{ $oViewConf->getHiddenSid() }]
          <input type="hidden" name="cl" value="start">
          <div class="right">
              <input id="test_BackToShop" type="submit" value="[{ oxmultilang ident="ACCOUNT_RECOMM_BACKTOSHOP" }]">
          </div>
      </div>
    </form>
</div>


[{insert name="oxid_tracker" title=$template_title }]
[{include file="_footer.tpl" }]
