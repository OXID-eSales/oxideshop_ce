[{if !$oxcmp_user->oxuser__oxusername->value}]
  [{include file="page/account/login.tpl" }]
[{else}]
    [{assign var="product" value=$oView->getProduct()}]
    [{ if $oxcmp_user->getRecommListsCount() }]
        <form action="[{ $oViewConf->getSelfActionLink() }]" method="post">
            <div>
                [{ $oViewConf->getHiddenSid() }]
                [{ $oViewConf->getNavFormParams() }]
                <input type="hidden" name="fnc" value="addToRecomm">
                <input type="hidden" name="cl" value="details">
                <input type="hidden" name="anid" value="[{ $product->oxarticles__oxid->value }]">
            </div>
            <ul class="form">
                <li>
                    <label>[{ oxmultilang ident="SELECT_LISTMANIA_LIST" suffix="COLON" }]</label>
                    <select name="recomm">
                        [{foreach from=$oView->getRecommLists() item=oList}]
                            <option value="[{$oList->oxrecommlists__oxid->value}]">[{$oList->oxrecommlists__oxtitle->value}]</option>
                        [{/foreach}]
                    </select>
                </li>
                <li>
                    <label>Description:</label>
                    <textarea cols="102" rows="7" name="recomm_txt" class="areabox"></textarea><br>
                </li>
                <li class="formSubmit">
                    <button class="submitButton largeButton" type="submit">[{ oxmultilang ident="ADD_TO_LISTMANIA_LIST" }]</button>
                </li>
            </ul>
      </form>
    [{else}]
        [{ oxmultilang ident="NO_LISTMANIA_LIST" }] <a href="[{ oxgetseourl ident=$oViewConf->getSelfLink()|cat:"cl=account_recommlist" }]">[{ oxmultilang ident="CLICK_HERE" }]</a>
    [{/if}]
[{/if}]
