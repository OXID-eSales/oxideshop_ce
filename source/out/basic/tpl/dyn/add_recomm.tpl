[{if !$oxcmp_user->oxuser__oxusername->value}]
  [{include file="inc/cmp_login.tpl" }]
[{else}]
  [{assign var="product" value=$oView->getProduct()}]
  <strong id="test_recommlistAddTitle" class="boxhead"><a href="[{ $product->getLink()|oxaddparams:$oViewConf->getNavUrlParams() }]">[{$template_title}]</a></strong>
  <div id="test_recommlistAdd" class="box info">
    [{ if $oxcmp_user->getRecommListsCount() }]
      <form action="[{ $oViewConf->getSelfActionLink() }]" method="post">
        <div>
            [{ $oViewConf->getHiddenSid() }]
            [{ $oViewConf->getNavFormParams() }]
            <input type="hidden" name="fnc" value="addToRecomm">
            <input type="hidden" name="cl" value="details">
            <input type="hidden" name="anid" value="[{ $product->oxarticles__oxid->value }]">
              [{ oxmultilang ident="ADD_RECOMM_SELECTLIST" }]: <br>
              <select id="test_recomListAddSelect" class="recomm_input" name="recomm">
                [{foreach from=$oView->getRecommLists() item=oList}]
                  <option value="[{$oList->oxrecommlists__oxid->value}]">[{$oList->oxrecommlists__oxtitle->value}]</option>
                [{/foreach}]
              </select>
            <br>
            <br>
            [{ oxmultilang ident="ADD_RECOMM_YOURCOMMENT" }]:
            <br>
            <textarea id="test_recommlistAddText" cols="102" rows="15" name="recomm_txt" class="fullsize"></textarea><br>
            <span class="btn"><input id="test_recommlistAddToList" type="submit" value="[{ oxmultilang ident="ADD_RECOMM_ADDTOLIST" }]" class="btn"></span>
         </div>
      </form>
    [{else}]
      [{ oxmultilang ident="ADD_RECOMM_ADDRECOMMLINK1" }] <a id="test_recommlistAddHere" href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_recommlist" }]">[{ oxmultilang ident="ADD_RECOMM_ADDRECOMMLINK2" }]</a>.
    [{/if}]
  </div>
[{/if}]
